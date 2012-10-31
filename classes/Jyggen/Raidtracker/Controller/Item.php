<?php
namespace Jyggen\Raidtracker\Controller;

use bnetlib\WorldOfWarcraft;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use bnetlib\Exception\PageNotFoundException;

class Item implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];

		$controllers->post('/', function(Application $app, Request $request){ return $this->post_item($app, $request); });

		return $controllers;

	}

	protected function post_item(Application $app, Request $request) {

		$item_id = $request->request->get('id');

		if(is_null($item_id) or intval($item_id) != $item_id) {

			$response = new Response();
			$response->setStatusCode(400);

			return $response;

		}

		if(count($app['db']->select('id')->from('items')->where('id', $item_id)->execute()) != 0) {

			$response = new Response();
			$response->setStatusCode(400);

			return $response;

		}

		try {

			$wow = new WorldOfWarcraft();
			$wow->getConnection()->setOptions(array('defaults' => array('region' => strtolower($app['config']['guild']['region']))));

			$item    = $wow->getItem(array('id' => $item_id));
			$name    = $item->getName();
			$quality = $item->getQuality();

			$app['db']->insert('items')->values(array(
				'id'      => $item_id,
				'name'    => $name,
				'quality' => $quality
			))->execute();

			$response = new Response();
			$response->setStatusCode(201);

			return $response;


		} catch(PageNotFoundException $e) {

			$response = new Response();
			$response->setStatusCode(404);

			return $response;

		}

	}

}