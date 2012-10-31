<?php
namespace Jyggen\Raidtracker\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class User implements ControllerProviderInterface {

	public function connect(Application $app) {

		$controllers = $app['controllers_factory'];
		$context     = $this;

		$controllers->post('/logout', function(Application $app) use ($context) { return $context->post_logout($app); });
		$controllers->post('/login', function(Application $app) use ($context) { return $context->post_login($app); });

		return $controllers;

	}

	public function post_logout(Application $app) {

		$app['session']->set('user', null);

		$response = new Response();
		$response->setStatusCode(200);

		return $response;

	}

	public function post_login(Application $app, Request $request) {

		$audience  = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		$assertion = $request->get('assertion');

		if(is_null($assertion)) {

			$response = new Response();
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

				$response = new Response();
				$response->setStatusCode(200);

				return $response;

			} else {

				$response = new Response();
				$response->setStatusCode(500);

				return $response;

			}

		}

	}

}