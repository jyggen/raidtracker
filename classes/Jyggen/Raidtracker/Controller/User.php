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
		$controllers->post('/login', function(Application $app, Request $request) use ($context) { return $context->post_login($app, $request); });

		return $controllers;

	}

	public function post_logout(Application $app) {

		$app['session']->set('user', null);

		return $app->json('Successfully logged-out.', 200);

	}

	public function post_login(Application $app, Request $request) {

		$audience  = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		$assertion = $request->get('assertion');

		if(is_null($assertion)) {

			return $app->json('Unable to fulfill request: Missing argument(s).', 400);

		} else {

			$response = \Zend\Http\ClientStatic::post('https://verifier.login.persona.org/verify', array(
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

				return $app->json('Successfully logged-in.', 200);

			} else {

				return $app->json('Unable to fulfill request: Verification failed, don\'t be evil!', 500);

			}

		}

	}

}