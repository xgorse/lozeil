<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Bayesian_Elements extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"bayesianelements",
			"categories",
			"writings"
		);
	}
	
	function test_stuff_with() {
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->categories_id = 5;
		$writing->accountingcodes_id = 205;
		$writing->comment = "payement de facture";
		$writing->save();
		$bayesianelements =new Bayesian_Elements();
		$bayesianelements->stuff_with($writing);
		$this->assertTrue(count($bayesianelements) == 6);
		$this->truncateTable("writings");
	}
	
	function test_increment() {
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->categories_id = 5;
		$writing->accountingcodes_id = 205;
		$writing->comment = "payement de facture";
		$writing->save();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->stuff_with($writing);
		$bayesianelements->increment();
		
		$bayesianelements_loaded = new Bayesian_Elements();
		$bayesianelements_loaded->select();
		$this->assertTrue(count($bayesianelements_loaded) == 6);
		$this->assertRecordExists("bayesianelements", array(
			'element' => 'payement',
			'field' => 'comment',
			'table_name' => 'categories',
			'table_id' => 5,
			'occurrences' => 1
		));
		
		$bayesianelements->increment();
		$bayesianelements_loaded = new Bayesian_Elements();
		$bayesianelements_loaded->select();
		$this->assertTrue(count($bayesianelements_loaded) == 6);
		$this->assertRecordExists("bayesianelements", array(
			'element' => 'payement',
			'field' => 'comment',
			'table_name' => 'categories',
			'table_id' => 5,
			'occurrences' => 2
		));
		$this->truncateTable("bayesianelements");
		$this->truncateTable("writings");
	}
	
	function test_increment_decrement() {
		$writing_before = new Writing();
		$writing_before->amount_inc_vat = 50;
		$writing_before->categories_id = 0;
		$writing_before->comment = "payement";
		$writing_before->save();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->stuff_with($writing_before);
		$bayesianelements->increment();
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->categories_id = 3;
		$writing->comment = "payement";
		$writing->save();
		
		$this->assertTrue(count($bayesianelements) == 0);
		$bayesianelements->increment_decrement($writing_before, $writing);
		$bayesianelements->select();
		$this->assertTrue(count($bayesianelements) == 2);
		$this->assertRecordExists("bayesianelements", array(
			'element' => 'payement',
			'field' => 'comment',
			'table_name' => 'categories',
			'table_id' => 3,
			'occurrences' => 1
		));
		$this->truncateTable("writings");
		$this->truncateTable("bayesianelements");
		
		$writing_before = new Writing();
		$writing_before->amount_inc_vat = 50;
		$writing_before->categories_id = 5;
		$writing_before->comment = "payement";
		$writing_before->save();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->stuff_with($writing_before);
		$bayesianelements->increment();
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->categories_id = 3;
		$writing->comment = "payement";
		$writing->save();
		
		$this->assertTrue(count($bayesianelements) == 2);
		$this->assertRecordExists("bayesianelements", array(
			'element' => 'payement',
			'field' => 'comment',
			'table_name' => 'categories',
			'table_id' => 5,
			'occurrences' => 1
		));
		$bayesianelements->increment_decrement($writing_before, $writing);
		$bayesianelements->select();
		$this->assertTrue(count($bayesianelements) == 4);
		$this->assertRecordExists("bayesianelements", array(
			'element' => 'payement',
			'field' => 'comment',
			'table_name' => 'categories',
			'table_id' => 5,
			'occurrences' => 0
		));
		$this->assertRecordExists("bayesianelements", array(
			'element' => 'payement',
			'field' => 'comment',
			'table_name' => 'categories',
			'table_id' => 3,
			'occurrences' => 1
		));
		$this->truncateTable("writings");
		$this->truncateTable("bayesianelements");
	}
	
	function test_train() {
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->categories_id = 5;
		$writing->comment = "payement de facture";
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -20;
		$writing->categories_id = 3;
		$writing->comment = "virement interne no parking";
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -20;
		$writing->categories_id = 3;
		$writing->comment = "virement interne no parking";
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 52.35;
		$writing->categories_id = 2;
		$writing->comment = "coopa pour un employé";
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 100;
		$writing->comment = "enregistrement non valide";
		$writing->save();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->train();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$bayesianelements->select();
		$this->assertTrue(count($bayesianelements) == 11);
		$this->assertRecordExists(
		"bayesianelements",
			array(
				'id' => 1,
				'element' => "payement",
				'field' => "comment",
				'table_id' => 5,
				'occurrences' => 1
			)
		);
		$this->assertRecordExists(
		"bayesianelements",
			array(
				'id' => 3,
				'element' => "50.000000",
				'field' => "amount_inc_vat",
				'table_id' => 5,
				'occurrences' => 1
			)
		);
		$this->assertRecordExists(
		"bayesianelements",
			array(
				'id' => 4,
				'element' => "virement",
				'field' => "comment",
				'table_id' => 3,
				'occurrences' => 2
			)
		);
		$this->truncateTable("bayesianelements");
	}
	
	function test_element_probabilities() {
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 10;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 5;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$this->assertEqual($bayesianelements->element_probabilities("virement", 3), 10/17);
		$this->truncateTable("categories");
		$this->truncateTable("bayesianelements");
	}
	
	function test_element_weighted_probabilities() {
		$category = new Category();
		$category->name = "cat 1";
		$category->save();
		$category = new Category();
		$category->name = "cat 2";
		$category->save();
		$category = new Category();
		$category->name = "cat 3";
		$category->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 10;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 5;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$this->assertEqual($bayesianelements->element_weighted_probabilities("virement", 3), (0.5 + 10*10/17)/11);
		$this->truncateTable("categories");
		$this->truncateTable("bayesianelements");
	}
	
	function test_data_probability() {
		$category = new Category();
		$category->name = "cat 1";
		$category->save();
		$category = new Category();
		$category->name = "cat 2";
		$category->save();
		$category = new Category();
		$category->name = "cat 3";
		$category->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 10;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 5;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "virement en banque";
		
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		
		$this->assertEqual(round($bayesianelements->data_probability($writing, 2), 6), 0.011364);
		$this->assertEqual(round($bayesianelements->data_probability($writing, 3), 6), 0.145053);
		$this->truncateTable("categories");
		$this->truncateTable("bayesianelements");
	}
	
	function test_probability() {
		$category = new Category();
		$category->name = "cat 1";
		$category->save();
		$category = new Category();
		$category->name = "cat 2";
		$category->save();
		$category = new Category();
		$category->name = "cat 3";
		$category->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 10;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 5;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "virement en banque";
		
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		
		$this->assertEqual(round($bayesianelements->probability($writing, 3), 6), 0.015269);
		$this->assertEqual(round($bayesianelements->probability($writing, 2), 6), 0.000598);
		$this->truncateTable("categories");
		$this->truncateTable("bayesianelements");
	}
	
	function test_categories_id_estimated() {
		$GLOBALS['param']['comment_weight'] = 1;
		$GLOBALS['param']['amount_inc_vat_weight'] = 3;
		
		$category = new Category();
		$category->name = "cat 1";
		$category->save();
		$category = new Category();
		$category->name = "cat 2";
		$category->save();
		$category = new Category();
		$category->name = "cat 3";
		$category->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 25;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "test";
		$bayesianelement->occurrences = 10;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 20;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "50";
		$bayesianelement->occurrences = 10;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "virement en banque";
		
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$this->assertEqual($bayesianelements->categories_id_estimated($writing), 3);
		$this->truncateTable("bayesianelements");
		
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 80;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "telecom";
		$bayesianelement->occurrences = 20;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 20;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 5;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "OVH";
		$bayesianelement->occurrences = 250;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "ceci est un telecom OVH";
		
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$this->assertEqual($bayesianelements->categories_id_estimated($writing), 2);
		$this->truncateTable("bayesianelements");
		
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 80;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "telecom";
		$bayesianelement->occurrences = 20;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 20;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 3;
		$bayesianelement->field = "amount_inc_vat";
		$bayesianelement->element = "autre";
		$bayesianelement->occurrences = 5;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "OVH";
		$bayesianelement->occurrences = 250;
		$bayesianelement->save();
		$bayesianelement = new Bayesian_Element();
		$bayesianelement->table_id = 2;
		$bayesianelement->field = "comment";
		$bayesianelement->element = "virement";
		$bayesianelement->occurrences = 2;
		$bayesianelement->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "ceci est un telecom OVH";
		
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$this->assertEqual($bayesianelements->categories_id_estimated($writing), 2);
		$this->truncateTable("bayesianelements");
	}
}
