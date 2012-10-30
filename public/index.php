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
		'debug' => $app['config']['debug'],
		'cache' => '../cache',
	),
));

$app->register(new Silex\Provider\SessionServiceProvider());
$app->mount('/', new Jyggen\Raidtracker\Controller\Index());
$app->mount('/user', new Jyggen\Raidtracker\Controller\User());
$app->mount('/event', new Jyggen\Raidtracker\Controller\Event());

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
