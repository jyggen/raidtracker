<?php
require '../vendor/autoload.php';
require '../functions.php';

$wisdom = new KevinGH\Wisdom\Wisdom('../');
$wisdom->addLoader(new KevinGH\Wisdom\Loader\YAML);
$wisdom->setCache('../cache');

$app = new Silex\Application();

$app['debug']  = false;
$app['config'] = $wisdom->get('config.yml');
$app['db']     = Cabinet\DBAL\Db::connection(array(
	'driver'   => $app['config']['db']['driver'],
	'username' => $app['config']['db']['username'],
	'password' => $app['config']['db']['password'],
	'database' => $app['config']['db']['database'],
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path'    => '../templates',
	'twig.options' => array(
		'debug' => false,
		'cache' => '../cache',
	),
));

$app->get('/', function() use($app){

	$controller = new Jyggen\Raidtracker\Controller\Index($app);
	return $controller->get_index();

});

$app->run();
