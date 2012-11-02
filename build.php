<?php
// Include required files.
require_once 'vendor/autoload.php';
require_once 'functions.php';

// Alias namespaces.
use Symfony\Component\Finder\Finder;

// Handlebars must be installed.
if(!commandExists('handlebars')) {
	die('handlebars: command not found'.PHP_EOL);
}

$files  = array();
$finder = new Finder();
$finder->files()->name('*.handlebars')->in(__DIR__.'/templates');

foreach($finder as $file) {

	$files[] = escapeshellarg($file->getRealPath());

}

$files  = implode(' ', $files);
$output = executeCommand('handlebars '.$files.' -k each -o -m -f '.escapeshellarg('./public/js/templates.js'));