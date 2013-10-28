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

require $current_directory."/collector.inc.php";
require $current_directory."/db.inc.php";
require $current_directory."/record.inc.php";

require $current_directory."/adodb-time.inc.php";
require $current_directory."/accounting_code.inc.php";
require $current_directory."/accounting_codes.inc.php";
require $current_directory."/bank.inc.php";
require $current_directory."/banks.inc.php";
require $current_directory."/bayesian_element.inc.php";
require $current_directory."/bayesian_elements.inc.php";
require $current_directory."/bot.inc.php";
require $current_directory."/bot_abstract.inc.php";
require $current_directory."/categories.inc.php";
require $current_directory."/category.inc.php";
require $current_directory."/collector_timed.inc.php";
require $current_directory."/config_file.inc.php";
require $current_directory."/database_tables.inc.php";
require $current_directory."/database_tables_source.inc.php";
require $current_directory."/database_tables_fr_fr.inc.php";
require $current_directory."/debug.inc.php";
require $current_directory."/excel.inc.php";
require $current_directory."/export_excel.inc.php";
require $current_directory."/file.inc.php";
require $current_directory."/files.inc.php";
require $current_directory."/format.inc.php";
require $current_directory."/heading_area.inc.php";
require $current_directory."/html_checkbox.inc.php";
require $current_directory."/html_cubismchart.inc.php";
require $current_directory."/html_input.inc.php";
require $current_directory."/html_input_ajax.inc.php";
require $current_directory."/html_input_date.inc.php";
require $current_directory."/html_list.inc.php";
require $current_directory."/html_radio.inc.php";
require $current_directory."/html_select.inc.php";
require $current_directory."/html_table.inc.php";
require $current_directory."/html_tag.inc.php";
require $current_directory."/html_textarea.inc.php";
require $current_directory."/menu_area.inc.php";
require $current_directory."/message.inc.php";
require $current_directory."/misc.inc.php";
require $current_directory."/param_file.inc.php";
require $current_directory."/plugins.inc.php";
require $current_directory."/source.inc.php";
require $current_directory."/sources.inc.php";
require $current_directory."/theme_default.inc.php";
require $current_directory."/update.inc.php";
require $current_directory."/user.inc.php";
require $current_directory."/users.inc.php";
require $current_directory."/user_authentication.inc.php";
require $current_directory."/writing.inc.php";
require $current_directory."/writing_imported.inc.php";
require $current_directory."/writings.inc.php";
require $current_directory."/writings_data_file.inc.php";
require $current_directory."/writings_export.inc.php";
require $current_directory."/writings_followup.inc.php";
require $current_directory."/writings_imported.inc.php";
require $current_directory."/writings_simulation.inc.php";
require $current_directory."/writings_simulations.inc.php";

if (function_exists("date_default_timezone_set")) {
	date_default_timezone_set("Europe/Paris");
}

if (strpos($_SERVER['SCRIPT_FILENAME'], "setup.php") === false  and strpos($_SERVER['SCRIPT_FILENAME'], "bot.php") === false) {
	$db = new db($dbconfig);
	$db->query("SET NAMES 'utf8'");
}
