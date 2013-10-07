<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Banks extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"bayesiandictionaries",
			"categories"
		);
	}
	
	function test_getData() {
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$writing = new Writing();
		$writing->amount_inc_vat = 52.35;
		$writing->categories_id = 5;
		$writing->comment = "ceci est un test de commentaire 28/05 321546";
		$writing->save();
		$words = $bayesiandictionaries->getData($writing);
		$words_expected = array (
			'comment' => array(
				'ceci',
				'est',
				'test',
				'commentaire',
				'321546'
			),
			'amount_inc_vat' => '52.35',
			'categories_id' => '5',
		);
		$this->assertEqual($words, $words_expected);
		$this->truncateTable("writings");
		$this->truncateTable("bayesiandictionaries");
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
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->train();
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		$bayesiandictionaries->filters = "";
		$bayesiandictionaries->select();
		$this->assertTrue(count($bayesiandictionaries) == 11);
		$this->assertRecordExists(
		"bayesiandictionaries",
			array(
				'id' => 1,
				'word' => "payement",
				'field' => "comment",
				'categories_id' => 5,
				'occurrences' => 1
			)
		);
		$this->assertRecordExists(
		"bayesiandictionaries",
			array(
				'id' => 3,
				'word' => "50.000000",
				'field' => "amount_inc_vat",
				'categories_id' => 5,
				'occurrences' => 1
			)
		);
		$this->assertRecordExists(
		"bayesiandictionaries",
			array(
				'id' => 4,
				'word' => "virement",
				'field' => "comment",
				'categories_id' => 3,
				'occurrences' => 2
			)
		);
		$this->truncateTable("bayesiandictionaries");
	}
	
	function test_word_probabilities() {
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 10;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		$this->assertEqual($bayesiandictionaries->word_probabilities("virement", 3), 10/17);
		$this->truncateTable("categories");
		$this->truncateTable("bayesiandictionaries");
	}
	
	function test_word_weighted_probabilities() {
		$category = new Category();
		$category->name = "cat 1";
		$category->save();
		$category = new Category();
		$category->name = "cat 2";
		$category->save();
		$category = new Category();
		$category->name = "cat 3";
		$category->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 10;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		$this->assertEqual($bayesiandictionaries->word_weighted_probabilities("virement", 3), (0.5 + 10*10/17)/11);
		$this->truncateTable("categories");
		$this->truncateTable("bayesiandictionaries");
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
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 10;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "virement en banque";
		
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		
		$this->assertEqual(round($bayesiandictionaries->data_probability($writing, 2), 6), 0.011364);
		$this->assertEqual(round($bayesiandictionaries->data_probability($writing, 3), 6), 0.145053);
		$this->truncateTable("categories");
		$this->truncateTable("bayesiandictionaries");
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
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 10;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "virement en banque";
		
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		
		$this->assertEqual(round($bayesiandictionaries->probability($writing, 3), 6), 0.015269);
		$this->assertEqual(round($bayesiandictionaries->probability($writing, 2), 6), 0.000598);
		$this->truncateTable("categories");
		$this->truncateTable("bayesiandictionaries");
	}
	
	function test_classify() {
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
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 25;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "test";
		$bayesiandictionary->occurrences = 10;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 20;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "50";
		$bayesiandictionary->occurrences = 10;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "virement en banque";
		
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		$this->assertEqual($bayesiandictionaries->classify($writing), 3);
		$this->truncateTable("bayesiandictionaries");
		
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 80;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "telecom";
		$bayesiandictionary->occurrences = 20;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 20;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "OVH";
		$bayesiandictionary->occurrences = 250;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "ceci est un telecom OVH";
		
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		$this->assertEqual($bayesiandictionaries->classify($writing), 2);
		$this->truncateTable("bayesiandictionaries");
		
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 80;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "telecom";
		$bayesiandictionary->occurrences = 20;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 20;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 3;
		$bayesiandictionary->field = "amount_inc_vat";
		$bayesiandictionary->word = "autre";
		$bayesiandictionary->occurrences = 5;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "OVH";
		$bayesiandictionary->occurrences = 250;
		$bayesiandictionary->save();
		$bayesiandictionary = new Bayesian_Dictionary();
		$bayesiandictionary->categories_id = 2;
		$bayesiandictionary->field = "comment";
		$bayesiandictionary->word = "virement";
		$bayesiandictionary->occurrences = 2;
		$bayesiandictionary->save();
		
		$writing = new Writing();
		$writing->amount_inc_vat = 50;
		$writing->comment = "ceci est un telecom OVH";
		
		$bayesiandictionaries = new Bayesian_Dictionaries();
		$bayesiandictionaries->prepare();
		$this->assertEqual($bayesiandictionaries->classify($writing), 2);
		$this->truncateTable("bayesiandictionaries");
	}
}
