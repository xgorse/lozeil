<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Bayesian_Dictionary extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"bayesiandictionaries"
		);
	}
	
	function test_save_load() {
		$bayesiandictionary = new Bayesian_dictionary();
		$bayesiandictionary->word = "filtre";
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->categories_id = 5;
		$bayesiandictionary->occurrences = 15;
		$bayesiandictionary->save();
		$bayesiandictionary_loaded = new Bayesian_dictionary();
		$bayesiandictionary_loaded->id = 1;
		$bayesiandictionary_loaded->load();
		$this->assertEqual($bayesiandictionary_loaded->word, $bayesiandictionary->word);
		$this->assertEqual($bayesiandictionary_loaded->field, $bayesiandictionary->field);
		$this->assertEqual($bayesiandictionary_loaded->categories_id, $bayesiandictionary->categories_id);
		$this->assertEqual($bayesiandictionary_loaded->occurrences, $bayesiandictionary->occurrences);
		$this->truncateTable("bayesiandictionaries");
	}
	
	function test_update() {
		$bayesiandictionary = new Bayesian_dictionary();
		$bayesiandictionary->word = "filtre";
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->categories_id = 5;
		$bayesiandictionary->occurrences = 15;
		$bayesiandictionary->save();
		$bayesiandictionary_loaded = new Bayesian_dictionary();
		$bayesiandictionary_loaded->id = 1;
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->field = "amount";
		$bayesiandictionary->categories_id = 4;
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary_loaded->update();
		$bayesiandictionary_loaded2 = new Bayesian_dictionary();
		$bayesiandictionary_loaded2->id = 1;
		$bayesiandictionary_loaded2->load();
		$this->assertNotEqual($bayesiandictionary_loaded2->word, $bayesiandictionary->word);
		$this->assertNotEqual($bayesiandictionary_loaded2->field, $bayesiandictionary->field);
		$this->assertNotEqual($bayesiandictionary_loaded2->categories_id, $bayesiandictionary->categories_id);
		$this->assertNotEqual($bayesiandictionary_loaded2->occurrences, $bayesiandictionary->occurrences);
		$this->truncateTable("bayesiandictionaries");
	}
	
	function test_delete() {
		$bayesiandictionary = new Bayesian_dictionary();
		$bayesiandictionary->word = "premier";
		$bayesiandictionary->save();
		$bayesiandictionary_loaded = new Bayesian_dictionary();
		$this->assertTrue($bayesiandictionary_loaded->load(1));
		$bayesiandictionary->delete();
		$this->assertFalse($bayesiandictionary_loaded->load(1));
	}
	
	function test_exists() {
		$bayesiandictionary = new Bayesian_dictionary();
		$bayesiandictionary->word = "premier";
		$bayesiandictionary->categories_id = 4;
		$bayesiandictionary->field = "virement";
		$bayesiandictionary->save();
		
		$this->assertTrue($bayesiandictionary->exists());
		$bayesiandictionary->word = "test";
		$this->assertFalse($bayesiandictionary->exists());
		$bayesiandictionary->word = "premier";
		$bayesiandictionary->categories_id = 3;
		$this->assertFalse($bayesiandictionary->exists());
		$bayesiandictionary->categories_id = 4;
		$bayesiandictionary->field = "test";
		$this->assertFalse($bayesiandictionary->exists());
		$this->truncateTable("bayesiandictionaries");
	}
}
