<?php
require '../vendor/autoload.php';

use Jet\Router\Router;

$router = new Router;

$router->addRoutes(array(
	'/' => '\jyggen\raidtracker\Index:index'
));

$router->launch();