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

	return strtolower(str_replace(array(' '), array('-'), $str));;

}

function twigTestKeyin($key, $array) {

	return (array_key_exists($key, $array)) ? true : false;

}
