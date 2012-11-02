<?php
function average($array, $subkey, $precision = 0) {

	$average = array();

	foreach ($array as $key => $value) {
		if(is_array($value)) {
			$average[] = $value[$subkey];
		} else {
			$average[] = $value->$subkey;
		}
	}

	return round(array_sum($average) / count($average), $precision);

}

function strToCssClass($str) {

	return strtolower(str_replace(array(' '), array('-'), $str));

}

function twigTestKeyin($key, $array) {

	return (array_key_exists($key, $array)) ? true : false;

}

function commandExists($cmd) {

	if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

		$output = executeCommand('where '.escapeshellarg($cmd).' 2> NUL');
		return (empty($output)) ? false : true;

	} else {

		$output = executeCommand('command -v '.escapeshellarg($cmd).' 2> /dev/null');
		return (empty($output)) ? false : true;

	}

}

function executeCommand($cmd) {

	$stdout         = array();
	$stderr         = array();
	$outfile        = tempnam('./cache', 'cmd');
	$errfile        = tempnam('./cache', 'cmd');
	$descriptorspec = array(0 => array("pipe", "r"), 1 => array("file", $outfile, "w"), 2 => array("file", $errfile, "w"));
	$proc           = proc_open($cmd, $descriptorspec, $pipes);

	if(!is_resource($proc)) { return 255; }

	fclose($pipes[0]); //Don't really want to give any input

	$exit   = proc_close($proc);
	$stdout = file($outfile);
	$stderr = file($errfile);

	unlink($outfile);
	unlink($errfile);

	if(!empty($stderr)) {

		$error  = $cmd;
		$error .= CRLF;

		foreach($stderr as $row) {
			$error .= $row.CRLF;
		}

		$error .= CRLF;

		throw new \Exception($error.'Command failed to execute, check output above for more information', E_USER_ERROR);

	}

	return $stdout;

}
