<?php
namespace Jyggen\Raidtracker;

class Controller {

	protected $config, $db, $template;

	public function __construct(\Pimple $di) {

		$this->config   = $di['config'];
		$this->db       = $di['database'];
		$this->template = $di['template'];

		$this->template->addExtension(new Twig\Extension());
		$this->template->addGlobal('config', $this->config);
		$this->template->addGlobal('VERSION', '1.0');
		$this->template->addFilter('avg', new \Twig_Filter_Function('average'));
		$this->template->addFilter('cssClass', new \Twig_Filter_Function('strToCssClass'));
		$this->template->addFilter('ucwords', new \Twig_Filter_Function('ucwords'));

	}

	protected function returnJson($data) {

		header('Content-Type: application/json');
		return json_encode($data);

	}

}