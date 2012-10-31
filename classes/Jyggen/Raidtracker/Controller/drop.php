<?php
namespace Jyggen\Raidtracker\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Drop implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];

		$controllers->post('/', function(Application $app, Request $request){ return $this->post_index($app, $request); });

		return $controllers;

	}

	protected function post_index(Application $app, Request $request) {

		$event  = $request->request->get('event');
		$player = $request->request->get('player');
		$item   = $request->request->get('item');
		$boss   = $request->request->get('boss');

		if(is_null($event) or is_null($player) or is_null($item) or is_null($boss)) {

			$response = new Response();
			$response->setStatusCode(400);

			return $response;

		}

		if ((
			count($app['db']->select('id')->from('events')->where('id', $event)->execute())       == 0
		) or (
			count($app['db']->select('id')->from('players')->where('id', $player)->execute())     == 0
		) or (
			count($app['db']->select('id')->from('items')->where('id', $item)->execute())         == 0
		) or (
			count($app['db']->select('id')->from('npcs_in_zones')->where('id', $boss)->execute()) == 0
		)) {

			$response = new Response();
			$response->setStatusCode(404);

			return $response;

		}

		$app['db']->insert('drops')->values(array(
			'event_id'         => $event,
			'player_id'        => $player,
			'item_id'          => $item,
			'npcs_in_zones_id' => $boss,
		))->execute();

		$response = new Response();
		$response->setStatusCode(201);

		return $response;

	}

}
