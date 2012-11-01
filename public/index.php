<?php
require '../vendor/autoload.php';
require '../functions.php';

use Cabinet\DBAL\Db;
use Jyggen\Raidtracker\Controller;
use KevinGH\Wisdom\Loader\YAML;
use KevinGH\Wisdom\Wisdom;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;

$wisdom = new Wisdom('../');
$wisdom->addLoader(new YAML);
$wisdom->setCache('../cache');

$app = new Application;

$app['config'] = $wisdom->get('config.yml');
$app['debug']  = $app['config']['debug'];
$app['db']     = Db::connection(array(
	'driver'   => $app['config']['db']['driver'],
	'username' => $app['config']['db']['username'],
	'password' => $app['config']['db']['password'],
	'database' => $app['config']['db']['database'],
));

$app->register(new TwigServiceProvider, array(
	'twig.path'    => '../templates',
	'twig.options' => array(
		'debug' => $app['config']['debug'],
		'cache' => '../cache',
	),
));

$app->register(new SessionServiceProvider);

$app->mount('/', new Controller\Index);
$app->mount('/drop', new Controller\Drop);
$app->mount('/event', new Controller\Event);
$app->mount('/item', new Controller\Item);
$app->mount('/user', new Controller\User);

$app->run();
