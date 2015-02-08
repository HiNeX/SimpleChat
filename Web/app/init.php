<?php

defined('ROOT') or die('Directory path is not defined.');

use Core\General;

function __autoload($name) {
	$path = ROOT.'/app/'.str_replace('\\', '/', $name).'.php';

	if (is_file($path)) {
		require_once $path;
	} else {
		die('Class "'.$name.'" not found.');
	}
}

spl_autoload_register('__autoload');

// Initialization application.
$general = new General();
$general->init();