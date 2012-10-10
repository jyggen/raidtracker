<?php
require '../vendor/autoload.php';

use Jet\Router\Router;

$router = new Router;

$router->addRoutes(array(
	'/' => '\Jyggen\Raidtracker\Controller\Index:index'
));

$router->launch();