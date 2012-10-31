<?php
namespace Jyggen\Raidtracker\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Event implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];
		$context     = $this;

		$controllers->post('/', function(Application $app, Request $request) use ($context) { return $context->post_index($app, $request); });

		return $controllers;

	}

	public function post_index(Application $app, Request $request) {

		$date = $request->request->get('date');

		if(is_null($date) or date('Y-m-d', strtotime($date)) !== $date) {

			$response = new Response();
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

		$response = new Response();
		$response->setStatusCode(201);

		return $response;

	}

}
