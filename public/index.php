<?php
require '../vendor/autoload.php';
require '../functions.php';

$wisdom = new KevinGH\Wisdom\Wisdom('../');
$wisdom->addLoader(new KevinGH\Wisdom\Loader\YAML);
$wisdom->setCache('../cache');

$app = new Silex\Application();

$app['config'] = $wisdom->get('config.yml');
$app['debug']  = $app['config']['debug'];
$app['db']     = Cabinet\DBAL\Db::connection(array(
	'driver'   => $app['config']['db']['driver'],
	'username' => $app['config']['db']['username'],
	'password' => $app['config']['db']['password'],
	'database' => $app['config']['db']['database'],
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path'    => '../templates',
	'twig.options' => array(
		'debug' => false,
		'cache' => '../cache',
	),
));

$app->register(new Silex\Provider\SessionServiceProvider());
$app->mount('/', new Jyggen\Raidtracker\Controller\Index());
$app->mount('/user', new Jyggen\Raidtracker\Controller\User());

$app->post('/event', function(Silex\Application $app, Symfony\Component\HttpFoundation\Request $request) {

	$date = $request->request->get('date');

	if(is_null($date) or date('Y-m-d', strtotime($date)) !== $date) {

		$response = new Symfony\Component\HttpFoundation\Response();
		$response->setStatusCode(400);

		return $response;

	}

	$result = $app['db']->insert('events')->values(array(
		'date' => $request->request->get('date'),
	))->execute();

	$event_id     = $result[0];
	$valid_ids    = array();
	$valid_status = array('OUT', 'STANDBY', 'OFFSPEC', 'CONFIRMED');

	foreach($request->request->keys() as $key) {
		if(substr($key, 0, 11) == 'attendance-' and in_array($request->request->get($key), $valid_status)) {
			$valid_ids[] = substr($key, 11);
		}
	}

	$players    = $app['db']->select('id')->from('players')->execute();
	$player_ids = array();

	foreach($players as $player) {
		$player_ids[] = $player->id;
	}

	foreach($valid_ids as $key => $id) {
		if(!in_array($id, $player_ids)) {
			unset($valid_ids[$key]);
		}
	}

	foreach($valid_ids as $id) {

		$app['db']->insert('attendance')->values(array(
			'player_id' => $id,
			'event_id'  => $event_id,
			'status'    => $request->request->get('attendance-'.$id)
		))->execute();

	}

	$response = new Symfony\Component\HttpFoundation\Response();
	$response->setStatusCode(200);

	return $response;

});

$app->post('/login', function(Silex\Application $app, Symfony\Component\HttpFoundation\Request $request) {

	$audience  = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
	$assertion = $request->get('assertion');

	if(is_null($assertion)) {

		$response = new Symfony\Component\HttpFoundation\Response();
		$response->setStatusCode(400);

		return $response;

	} else {

		$response = Zend\Http\ClientStatic::post('https://verifier.login.persona.org/verify', array(
			'assertion' => $assertion,
			'audience'  => $audience
		));

		$response = json_decode($response->getContent());

		if($response->status == 'okay') {

			if(in_array($response->email, $app['config']['admins'])) {

				$app['session']->set('user', array(
					'email' => $response->email,
					'admin' => true
				));

			} else {

				$app['session']->set('user', array(
					'email' => $response->email,
					'admin' => false
				));

			}

			$response = new Symfony\Component\HttpFoundation\Response();
			$response->setStatusCode(200);

			return $response;

		} else {

			$response = new Symfony\Component\HttpFoundation\Response();
			$response->setStatusCode(500);

			return $response;

		}

	}

});

$app->run();
