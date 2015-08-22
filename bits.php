<?php

namespace Bits;

if(!defined('BITS_VERSION')) {
	define('BITS_VERSION', '0.1 beta');

	spl_autoload_register(function($class){
		$class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		$class = str_replace('/', DIRECTORY_SEPARATOR, $class);
		include_once $class . '.php';
	});

	/**
	 * 
	 * @return \Bits\Bit
	 */
	function __() {
		return Bit::getInstance();
	}

	__();
}

?>