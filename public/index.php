<?php
require '../vendor/autoload.php';
require '../functions.php';

use Jet\Router\Router;
use Cabinet\DBAL\Db;
use Kengai\Manager as Kengai;
use Kengai\SourceReader\YAML;

/* Let's be lazy and just  try/catch everything! */
try {

	/* Configuration */
	$config = new Kengai();

	$config->add(new YAML('../config.yml'));
	$config->fetch();

	/* Database */
	$db = Db::connection(array(
		'driver'   => $config->get('database.driver'),
		'username' => $config->get('database.username'),
		'password' => $config->get('database.password'),
		'database' => $config->get('database.database'),
	));

	/* Template Engine */
	$loader = new \Twig_Loader_Filesystem('../templates');
	$twig   = new \Twig_Environment($loader);

	/* Dependency Injection */
	$container             = new Pimple();
	$container['config']   = $config;
	$container['database'] = $db;
	$container['template'] = $twig;

	/* Routing */
	$router = new Router;

	$router->addRoutes(array(
		'/'     => function(){ return array('Jyggen\Raidtracker\Controller\Index', 'index'); },
		'error' => function($error){ die('404'); },
	));

	list($controller, $method) = $router->launch();

	$controllerObject = new $controller($container);
	$method           = strtolower(Router::getMethod()).'_'.$method;

	if (method_exists($controllerObject, $method)) {

		print call_user_func(array($controllerObject, $method));

	} else throw new Exception('Invalid method "'.$method.'" for controller "'.$controller.'".');

} catch (Exception $e) {

	die('<pre><strong>Error:</strong> '.$e->getMessage().'</pre>');

}
