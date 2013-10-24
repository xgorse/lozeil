<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Database_Tables_Source {
	protected $db;

	function __construct($db = null) {
		if ($db == null) {
			$db = new db();
		}
		$this->db = $db;
	}

	function enumerate() {
		return array();
	}
}
