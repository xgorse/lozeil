<?php
/*
	lozeil
	$Author: adrien $
	$URL: $
	$Revision:  $

	Copyright (C) No Parking 2013 - 2013
*/

class Banks extends Collector  {
	public $filters = null;
	
	function __construct($class = null, $table = null, $db = null) {
		if ($class === null) {
			$class = substr(__CLASS__, 0, -1);
		}
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_banks'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
	
	function names() {
		$names = array();
		$names[0] = "--";
		foreach ($this as $bank) {
			$names[$bank->id] = $bank->name();
		}
		return $names;
	}
}
