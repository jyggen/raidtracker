<?php
namespace Jyggen\Raidtracker\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

class User implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];

		$controllers->post('/logout', function(Application $app){ return $this->post_logout($app); });

		return $controllers;

	}

	protected function post_logout(Application $app) {

		$app['session']->set('user', null);

		$response = new Response();
		$response->setStatusCode(200);

		return $response;

	}

}