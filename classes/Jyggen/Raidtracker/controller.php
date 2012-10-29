<?php
namespace Jyggen\Raidtracker;

class Controller {

	protected $config, $db, $template;

	public function __construct(\Silex\Application $app) {

		$this->app = $app;

		$app['twig']->addExtension(new Twig\Extension());
		$app['twig']->addGlobal('config', $app['config']);
		$app['twig']->addGlobal('VERSION', '1.0');
		$app['twig']->addFilter('avg', new \Twig_Filter_Function('average'));
		$app['twig']->addFilter('cssClass', new \Twig_Filter_Function('strToCssClass'));
		$app['twig']->addFilter('ucwords', new \Twig_Filter_Function('ucwords'));

	}

	protected function returnJson($data) {

		header('Content-Type: application/json');
		return json_encode($data);

	}

}