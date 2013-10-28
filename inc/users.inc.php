<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Users extends Collector {
	public $filters = null;
	
	function __construct($db = null, $class = null, $table = null) {
		if ($class === null) {
			$class = substr(__CLASS__, 0, -1);
		}
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_users'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
}
