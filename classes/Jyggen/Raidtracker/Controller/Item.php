<?php
namespace Jyggen\Raidtracker\Controller;

use bnetlib\Exception\JsonException;
use bnetlib\Exception\PageNotFoundException;
use bnetlib\WorldOfWarcraft;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Item implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];
		$context     = $this;

		$controllers->post('/', function(Application $app, Request $request) use ($context) { return $context->post_item($app, $request); });

		return $controllers;

	}

	public function post_item(Application $app, Request $request) {

		$item_id = $request->request->get('id');

		if(is_null($item_id) or intval($item_id) != $item_id) {

			return $app->json('Unable to fulfill request: Missing argument(s).', 400);

		}

		if(count($app['db']->select('id')->from('items')->where('id', $item_id)->execute()) != 0) {

			return $app->json('Item already exists in database.', 400);

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

			return $app->json('Item successfully added to database.', 201);


		} catch(PageNotFoundException $e) {

			return $app->json('Blizzard says: Item ID not found.', 404);

		} catch(JsonException $e) {

			return $app->json('Blizzard returned weird data, please try again.', 500);

		}

	}

}