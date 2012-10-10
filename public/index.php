<?php
require '../vendor/autoload.php';

use Jet\Router\Router;
use Cabinet\DBAL\Db;
use Kengai\Manager as Kengai;
use Kengai\SourceReader\YAML;

$config = new Kengai();

$config->add(new YAML('../config.yml'));
$config->fetch();

$router = new Router;

$router->addRoutes(array(
	'/' => '\Jyggen\Raidtracker\Controller\Index:index'
));

$router->launch();
