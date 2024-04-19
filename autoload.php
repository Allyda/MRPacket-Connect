<?php
require_once 'environment.php';

//supported(PHP 7 >= 7.3.0, PHP 7)
spl_autoload_extensions(".php");

spl_autoload_register(function ($className) {
	$className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
	require_once $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $className . '.php';
});
