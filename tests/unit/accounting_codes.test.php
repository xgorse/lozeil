<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Sources extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"sources"
		);
	}
}
