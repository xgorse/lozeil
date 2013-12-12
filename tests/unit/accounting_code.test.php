<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Accounting_Code extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"accountingcodes"
		);
	}
	
	function test_save_load() {
		$accountingcode = new Accounting_Code();
		$accountingcode->name = "première accountingcode";
		$accountingcode->save();
		$accountingcode_loaded = new Accounting_Code();
		$accountingcode_loaded->load(array('id' => 1));
		$this->assertEqual($accountingcode_loaded->name, $accountingcode->name);
		$this->truncateTable("accountingcodes");
	}
	
	function test_update() {
		$accountingcode = new Accounting_Code();
		$accountingcode->name = "premier accountingcode";
		$accountingcode->save();
		$accountingcode_loaded = new Accounting_Code();
		$accountingcode_loaded->id = 1;
		$accountingcode_loaded->name = "changement de nom";
		$accountingcode_loaded->update();
		$accountingcode_loaded2 = new Accounting_Code();
		$accountingcode_loaded2->load(array('id' => 1));
		$this->assertNotEqual($accountingcode_loaded2->name, $accountingcode->name);
		$this->truncateTable("accountingcodes");
	}
	
	function test_delete() {
		$accountingcode = new Accounting_Code();
		$accountingcode->name = "premier accountingcode";
		$accountingcode->save();
		$accountingcode_loaded = new Accounting_Code();
		$this->assertTrue($accountingcode_loaded->load(array('id' => 1)));
		$accountingcode->delete();
		$this->assertFalse($accountingcode_loaded->load(array('id' => 1)));
		$this->truncateTable("accountingcodes");
	}
}
