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
		$context     = $this;

		$controllers->post('/', function(Application $app, Request $request) use ($context) { return $context->post_index($app, $request); });
		$controllers->get('/new', function(Application $app) use ($context) { return $context->get_new($app); });

		return $controllers;

	}

	public function post_index(Application $app, Request $request) {

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

	public function get_new(Application $app) {

		$events  = $app['db']->select('id', 'date')->from('events')->orderBy('date')->execute();
		$players = $app['db']->select('id', 'name')->from('players')->orderBy('name')->execute();
		$items   = $app['db']->select('id', 'name')->from('items')->orderBy('name')->execute();
		$bosses  = $app['db']->select('niz.id', array('z.name', 'zone'), 'n.name')
		                     ->from(array('npcs_in_zones', 'niz'))
		                     ->join(array('npcs', 'n'), 'left')
		                     ->on('niz.npc_id', 'n.id')
		                     ->join(array('zones', 'z'), 'left')
		                     ->on('niz.zone_id', 'z.id')
		                     ->orderBy('z.name')
		                     ->orderBy('n.name')
		                     ->execute();

		return $app->json(array(
			'events'  => $events,
			'players' => $players,
			'items'   => $items,
			'bosses'  => $bosses,
		));

	}

}
