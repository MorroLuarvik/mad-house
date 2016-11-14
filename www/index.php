<?
	include('settings.php');

	error_reporting(E_ALL|E_STRICT);
	ini_set('display_errors', DISPLAY_ERRORS);
	
	date_default_timezone_set(CURRENT_TZ);

	$paths = implode(PATH_SEPARATOR,
                    array (	'../libs', '../apps'));
	
	set_include_path($paths);
	
	include "Zend/Loader/Autoloader.php";
	$al = Zend_Loader_Autoloader::getInstance();
	$al->registerNamespace('Bootstrap');
	//$al->registerNamespace('Speculator');
	
	require 'Init.php';
	new Bootstrap();
