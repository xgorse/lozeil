<?php
/*
	application ofr
	$Author: frank $
	$URL: svn://svn.noparking.net/var/repos/projets/opentime.fr/applications/ofr/inc/bot.inc.php $
	$Revision: 937 $

	Copyright (C) No Parking 2010 - 2012
*/

abstract class Bot_Abstract {
	function execute($argv, $get) {
		if ($argv) {
			$params = array();
			foreach (array_slice($argv, 1) as $arg) {
				if (!isset($method) and strpos($arg, "--") === 0) {
					$method = substr($arg, 2);
				} else {
					if (strpos($arg, "=")) {
						list($key, $value) = explode("=", $arg);
						$params[$key] = $value;
					}
				}
			}
		} else {
			$method = key($get);
			$params = array_slice($get, 1);
		}
		
		if (isset($method) and method_exists($this, $method)) {
			return $this->$method($params);
		} else {
			return $this->help();
		}
	}
	
	function help() {
		$this_class_name = get_class($this);
		$help = __("Methods available with %s:", array($this_class_name))."\n";
		$class = new ReflectionClass($this_class_name);
		foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			if ($method->class == $this_class_name and strpos($method->name, "__") !== 0) {
 				$help .= "--".$method->name."\n";
			}
		}

		return $help;
	}
}
