<?php
require '../vendor/autoload.php';
require '../functions.php';

$wisdom = new KevinGH\Wisdom\Wisdom('../');
$wisdom->addLoader(new KevinGH\Wisdom\Loader\YAML);
$wisdom->setCache('../cache');

$app = new Silex\Application();

$app['config'] = $wisdom->get('config.yml');
$app['debug']  = $app['config']['debug'];
$app['db']     = Cabinet\DBAL\Db::connection(array(
	'driver'   => $app['config']['db']['driver'],
	'username' => $app['config']['db']['username'],
	'password' => $app['config']['db']['password'],
	'database' => $app['config']['db']['database'],
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path'    => '../templates',
	'twig.options' => array(
		'debug' => $app['config']['debug'],
		'cache' => '../cache',
	),
));

$app->register(new Silex\Provider\SessionServiceProvider());

$app->mount('/', new Jyggen\Raidtracker\Controller\Index());
$app->mount('/drop', new Jyggen\Raidtracker\Controller\Drop());
$app->mount('/event', new Jyggen\Raidtracker\Controller\Event());
$app->mount('/item', new Jyggen\Raidtracker\Controller\Item());
$app->mount('/user', new Jyggen\Raidtracker\Controller\User());

$app->run();
