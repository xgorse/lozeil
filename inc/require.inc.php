<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$current_directory = dirname(__FILE__);

if (file_exists($current_directory."/../cfg/config.inc.php")) {
	require $current_directory."/../cfg/config.inc.php";
}
if (file_exists($current_directory."/../cfg/param.inc.php")) {
	require $current_directory."/../cfg/param.inc.php";
}

if (isset($GLOBALS['pathconfig']['cfg']) and !empty($GLOBALS['pathconfig']['cfg'])) {
	if (file_exists($GLOBALS['pathconfig']['cfg']."config.inc.php")) {
		require($GLOBALS['pathconfig']['cfg']."config.inc.php");
	}
	if (file_exists($GLOBALS['pathconfig']['cfg']."param.inc.php")) {
		require($GLOBALS['pathconfig']['cfg']."param.inc.php");
	}
}

require $current_directory."/../lang/fr_FR.lang.php";

require $current_directory."/adodb-time.inc.php";
require $current_directory."/misc.inc.php";
require $current_directory."/email.inc.php";

require ($current_directory."/../inc/autoload.inc.php");
Lozeil_Autoload::register($current_directory, $current_directory."/../var/tmp/autoload.index");

$external_directories = array_merge(directories_for_plugins(), directories_for_applications());
foreach ($external_directories as $name => $path) {
	if (file_exists($path."/cfg/config.inc.php")) {
		require $path."/cfg/config.inc.php";
	}

	if (file_exists($path."/cfg/param.inc.php")) {
		require $path."/cfg/param.inc.php";
	}

	if (file_exists($path."/cfg/acl.inc.php")) {
		require $path."/cfg/acl.inc.php";
	}
}

if (function_exists("date_default_timezone_set")) {
	date_default_timezone_set("Europe/Paris");
}

if (strpos($_SERVER['SCRIPT_FILENAME'], "setup.php") === false  and strpos($_SERVER['SCRIPT_FILENAME'], "bot.php") === false) {
	$db = new db($dbconfig);
	$db->query("SET NAMES 'utf8'");
}
