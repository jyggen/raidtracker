<?php
namespace Jyggen\Raidtracker\Twig;

class Extension extends \Twig_Extension {

	public function getName() {
		return 'raidtracker';
	}

	public function getTests() {
		return array(
			'keyin' => new \Twig_Test_Function('twigTestKeyin'),
		);
	}

}