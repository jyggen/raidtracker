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

class Player implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];
		$context     = $this;

		$controllers->post('/', function(Application $app, Request $request) use ($context) { return $context->post_index($app, $request); });

		return $controllers;

	}

	public function post_index(Application $app, Request $request) {

		$player = $request->request->get('player');

		if(is_null($player)) {

			return $app->json('Unable to fulfill request: Missing argument(s).', 400);

		}

		if(count($app['db']->select('id')->from('players')->where('name', $player)->execute()) != 0) {

			return $app->json('Player already exists in database.', 400);

		}

		try {

			$wow = new WorldOfWarcraft();
			$wow->getConnection()->setOptions(array('defaults' => array('region' => strtolower($app['config']['guild']['region']))));

			$character = $wow->getCharacter(array('realm' => $app['config']['guild']['realm'], 'name' => $player, 'fields' => array('items', 'guild')));
			$guild     = $character->getGuild()->getName();

			if($guild != $app['config']['guild']['name']) {

				return $app->json($player.' is not a member of '.$app['config']['guild']['name'].'.', 400);

			}

			$app['db']->insert('players')->values(array(
				'class_id' => $character->getClass(),
				'name'     => $character->getName(),
				'ilvl'     => $character->getItems()->getAverageItemLevelEquipped()
			))->execute();

			return $app->json($character->getName().' successfully added to database.', 201);


		} catch(PageNotFoundException $e) {

			return $app->json('Blizzard says: Player not found.', 404);

		} catch(JsonException $e) {

			return $app->json('Blizzard returned weird data, please try again.', 500);

		}

	}

}