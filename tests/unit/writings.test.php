<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Writings extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"writings",
			"categories",
			"sources",
			"banks",
			"bayesianelements",
			"accountingcodes"
		);
	}
	
	function test_select_duplicate() {
		$_SESSION['filter']['start'] = mktime(0, 0, 0, 10, 14, 2013);
		$_SESSION['filter']['stop'] = mktime(0, 0, 0, 10, 16, 2013);
		$writing = new Writing();
		$writing->amount_inc_vat = 250;
		$writing->day = mktime(0, 0, 0, 10, 15, 2013);
		$writing->banks_id = 1;
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 250;
		$writing->day = mktime(0, 0, 0, 10, 15, 2013);
		$writing->banks_id = 0;
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -250;
		$writing->day = mktime(0, 0, 0, 10, 15, 2013);
		$writing->banks_id = 1;
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -250;
		$writing->day = mktime(0, 0, 0, 10, 15, 2013);
		$writing->banks_id = 1;
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 0;
		$writing->day = mktime(0, 0, 0, 10, 15, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->filter_with(array('start' => mktime(0, 0, 0, 10, 14, 2013), 'stop' => mktime(0, 0, 0, 10, 16, 2013), 'duplicate' => 1));
		$writings->select();
		$this->assertTrue(count($writings) == 2);
		$this->truncateTable("writings");
	}
	
	function test_get_join() {
		$writings = new Writings();
		$writings->filter_with(array("timestamp" => 3));
		$writings->add_order("amount_inc_vat DESC");
		$join = $writings->get_join();
		$this->assertPattern("/LEFT JOIN categories/", $join[0]);
		$this->assertPattern("/ON categories.id = writings.categories_id/", $join[0]);
		$this->assertPattern("/LEFT JOIN sources/", $join[1]);
		$this->assertPattern("/ON sources.id = writings.sources_id/", $join[1]);
		$this->assertPattern("/ON sources.id = writings.sources_id/", $join[1]);
		$this->assertPattern("/LEFT JOIN banks/", $join[2]);
		$this->assertPattern("/ON banks.id = writings.banks_id/", $join[2]);
	}
	
	function test_get_columns() {
		$writings = new Writings();
		$writings->add_order("amount_inc_vat DESC");
		$columns = $writings->get_columns();
		$this->assertPattern("/`writings`.*/", $columns[0]);
		$this->assertPattern("/categories.name as category_name, sources.name as source_name, banks.name as bank_name/", $columns[1]);
	}
	
	function test_show() {
		$_SESSION['filter']['start'] = mktime(0, 0, 0, 7, 1, 2013);
		list($start, $stop) = determine_month($_SESSION['filter']['start']);
		$category = new Category();
		$category->name = "Category 1";
		$category->save();
		$bank = new Bank();
		$bank->name = "Bank 1";
		$bank->save();
		$source = new Source();
		$source->name = "Source 1";
		$source->save();
		$category2 = new Category();
		$category2->name = "Category 2";
		$category2->save();
		$bank2 = new Bank();
		$bank2->name = "Bank 2";
		$bank2->save();
		$source2 = new Source();
		$source2->name = "Source 2";
		$source2->save();
		
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 1;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 1;
		$writing->number = 1;
		$writing->vat = 19.6;
		$writing->save();
		
		$writing2 = new Writing();
		$writing2->categories_id = 2;
		$writing2->amount_excl_vat = 90.50;
		$writing2->amount_inc_vat = 100;
		$writing2->banks_id = 2;
		$writing2->comment = "Ceci est un autre élément du test";
		$writing2->day = mktime(10, 0, 0, 7, 10, 2013);
		$writing2->information = "Autre complément d'infos";
		$writing2->paid = 1;
		$writing2->sources_id = 2;
		$writing2->number = 2;
		$writing2->vat = 5.5;
		$writing2->save();
		
		$writing3 = new Writing();
		$writing3->categories_id = 1;
		$writing3->amount_excl_vat = 190.50;
		$writing3->amount_inc_vat = 250;
		$writing3->paid = 0;
		$writing3->number = 2;
		$writing3->vat = 5.5;
		$writing3->sources_id = 2;
		$writing3->day = strtotime('+1 months', mktime(10, 0, 0, 7, 29, 2013));
		$writing3->save();
		
		$writing4 = new Writing();
		$writing4->categories_id = 1;
		$writing4->amount_excl_vat = 250;
		$writing4->amount_inc_vat = 279;
		$writing4->paid = 0;
		$writing4->number = 1;
		$writing4->vat = 5.5;
		$writing4->sources_id = 2;
		$writing4->day = strtotime('-1 months', mktime(10, 0, 0, 7, 29, 2013));
		$writing4->save();
		
		$writings = new Writings();
		$writings->add_order("day ASC");
		$writings->filter_with(array('start' => $start, 'stop' => $stop));
		$writings->select();
		
		$table = $writings->show();
		$this->assertPattern("/5.5/", $table);
		$this->assertPattern("/Bank 1/", $table);
		$this->assertPattern("/Source 1/", $table);
		$this->assertPattern("/Category 1/", $table);
		$this->assertPattern("/Ceci est un test/", $table);
		$this->assertPattern("/Autre complément d'infos/", $table);
		$this->assertNoPattern("/e50b79ffaccc6b50d018aad432711418/", $table);
		$this->assertPattern("/class=\"draggable/", $table);
		$this->assertNoPattern("/<td>250.00<\/td>/", $table);
		$this->assertNoPattern("/279/", $table);
		
		$writings = new Writings();
		$writings->add_order("day ASC");
		$writings->filter_with(array('search_index' => "élément"));
		$writings->select();
		
		$table = $writings->show();
		$this->assertPattern("/Ceci est un autre élément du test/", $table);
		$this->assertNoPattern("/Ceci est un test/", $table);
		
		$writings = new Writings();
		$writings->add_order("day ASC");
		$writings->filter_with(array('search_index' => "Bank"));
		$writings->select();
		
		$table = $writings->show();
		$this->assertPattern("/Bank 1/", $table);
		$this->assertPattern("/Bank 2/", $table);
		
		$writings = new Writings();
		$writings->add_order("day ASC");
		$writings->filter_with(array('search_index' => "Source 1"));
		$writings->select();
		
		$table = $writings->show();
		$this->assertPattern("/Source 1/", $table);
		$this->assertNoPattern("/Source 2/", $table);
		
		$this->truncateTable("writings");
		$this->truncateTable("sources");
		$this->truncateTable("categories");
		$this->truncateTable("banks");
	}
	
	function test_get_where() {
		$_SESSION['filter']['start'] = 1375308000;
		list($start, $stop) = determine_month($_SESSION['filter']['start']);
		$writings = new Writings();
		$writings->filter_with(array('start' => $start, 'stop' => $stop));
		$get_where = $writings->get_where();
		$this->assertPattern("/writings.day >= 1375308000/", $get_where[0]);
		$this->assertPattern("/writings.day <= 1377986399/", $get_where[1]);
		$writings2 = new Writings();
		$get_where2 = $writings2->get_where();
		$this->assertTrue(!isset($get_where2[0]));
		$this->assertFalse(isset($get_where2[1]));
	}
	
	function test_show_balance_at() {
		$writing1 = new Writing();
		$writing1->amount_inc_vat = 150.56;
		$writing1->day = mktime(10, 0, 0, 7, 20, 2013);
		$writing1->save();
		
		$writings = new Writings();
		$writings->select();
		$this->assertEqual($writings->show_balance_at(mktime(10, 0, 0, 7, 29, 2013)), 150.56);
		$this->assertEqual($writings->show_balance_at(mktime(10, 0, 0, 7, 19, 2013)), 0);
		
		$writing2 = new Writing();
		$writing2->amount_inc_vat = -2150.56;
		$writing2->day = mktime(10, 0, 0, 7, 18, 2013);
		$writing2->save();
		
		$writings = new Writings();
		$writings->select();
		$this->assertEqual($writings->show_balance_at(mktime(10, 0, 0, 7, 29, 2013)), -2000);

		$this->truncateTable("writings");
	}
	
	function test_show_timeline_at() {
		$writing = new Writing();
		$writing->amount_inc_vat = 15.50;
		$writing->day = mktime(10, 0, 0, 1, 20, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 16.80;
		$writing->day = mktime(10, 0, 0, 1, 10, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -15.5;
		$writing->day = mktime(10, 0, 0, 2, 20, 2013);
		$writing->save();
		$writings = new Writings();
		$timeline = $writings->show_timeline_at(mktime(0, 0, 0, 3, 1, 2013));
		$this->assertPattern("/cubismtimeline/", $timeline);
		$this->assertPattern("/cubism_data_title/", $timeline);
		$this->assertPattern("/cubism_data_positive_average/", $timeline);
		$this->assertPattern("/cubism_data_negative_average/", $timeline);
		$this->assertPattern("/32.3/", $timeline);
		$this->assertPattern("/16.8/", $timeline);
		$this->truncateTable("writings");
	}
	
	function test_balance_per_day_in_a_year_in_array() {
		$writing = new Writing();
		$writing->amount_inc_vat = 15;
		$writing->day = mktime(10, 0, 0, 1, 20, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 16;
		$writing->day = mktime(10, 0, 0, 1, 10, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -15;
		$writing->day = mktime(10, 0, 0, 2, 20, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 12;
		$writing->day = mktime(10, 0, 0, 2, 20, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = 10;
		$writing->day = mktime(10, 0, 0, 1, 5, 2013);
		$writing->save();
		$writings = new Writings();
		$writings->select();
		$balance_per_day = $writings->balance_per_day_in_a_year_in_array(mktime(0, 0, 0, 1, 1, 2013));
		$this->assertTrue(count($balance_per_day) == 365);
		$this->assertTrue($balance_per_day[0] == 0);
		$this->assertTrue($balance_per_day[4] == 10);
		$this->assertTrue($balance_per_day[9] == 26);
		$this->assertTrue($balance_per_day[19] == 41);
		$this->assertTrue($balance_per_day[50] == 38);
		$this->truncateTable("writings");
	}

	function test_filter_with() {
		$writings = new Writings();
		$writings->filter_with(array('start' => mktime(0, 0, 0, 3, 9, 2013), 'stop' => mktime(0, 0, 0, 3, 10, 2013), 'search_index' => 'fullsearch'));
		$this->assertEqual($writings->filters['start'], 1362783600);
		$this->assertEqual($writings->filters['stop'], 1362870000);
		$this->assertEqual($writings->filters['search_index'], "fullsearch");
		$this->truncateTable("writings");
	}
	
	function test_cancel_last_operation() {
		$writing = new Writing();
		$writing->save();
		$writing = new Writing();
		$writing->save();
		$writing = new Writing();
		$writing->save();
		$writing = new Writing();
		$writing->save();
		$writings = new Writings();
		$writings->cancel_last_operation();
		$writings->select();
		$this->assertTrue(count($writings) == 0);
		$this->truncateTable("writings");
	}
	
	function test_determine_show_form_modify() {
		$category = new Category();
		$category->name = "Category 1";
		$category->save();
		$source = new Source();
		$source->name = "Source 1";
		$source->save();
		$writings = new Writings();
		$this->assertPattern("/writings_modify_form/", $writings->determine_show_form_modify(""));
		$this->assertPattern("/categories_id/", $writings->determine_show_form_modify("change_category"));
		$this->assertPattern("/Category 1/", $writings->determine_show_form_modify("change_category"));
		$this->assertPattern("/sources_id/", $writings->determine_show_form_modify("change_source"));
		$this->assertPattern("/Source 1/", $writings->determine_show_form_modify("change_source"));
		$this->assertPattern("/accountingcodes_id/", $writings->determine_show_form_modify("change_accounting_code"));
		$this->assertPattern("/amount_inc_vat/", $writings->determine_show_form_modify("change_amount_inc_vat"));
		$this->assertPattern("/input/", $writings->determine_show_form_modify("change_amount_inc_vat"));
		$this->assertPattern("/vat/", $writings->determine_show_form_modify("change_vat"));
		$this->assertPattern("/input/", $writings->determine_show_form_modify("change_vat"));
		$this->assertPattern("/date/", $writings->determine_show_form_modify("change_day"));
		$this->assertPattern("/input/", $writings->determine_show_form_modify("change_day"));
		$this->assertPattern("/duplicate/", $writings->determine_show_form_modify("duplicate"));
		$this->assertPattern("/input/", $writings->determine_show_form_modify("duplicate"));
		$this->truncateTable("writings");
		$this->truncateTable("sources");
		$this->truncateTable("categories");
	}
	
	function test_clean_from_ajax() {
		$writings = new Writings();
		$post = array(
			'operation' => "change_category",
			'ids' => "[\"4\",\"1\"]",
			'categories_id' => 3
		);
		$expected = array(
			'operation' => "change_category",
			'value' => 3,
			'id' => array(
				0 => 4,
				1 => 1
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		$post = array(
			'operation' => "change_accounting_code",
			'ids' => "[\"4\",\"2\"]",
			'accountingcodes_id' => 3
		);
		$expected = array(
			'operation' => "change_accounting_code",
			'value' => 3,
			'id' => array(
				0 => 4,
				1 => 2
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		
		$post = array(
			'operation' => "change_source",
			'ids' => "[\"10\",\"13\"]",
			'sources_id' => 4
		);
		$expected = array(
			'operation' => "change_source",
			'value' => 4,
			'id' => array(
				0 => 10,
				1 => 13
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		
		$post = array(
			'operation' => "change_vat",
			'ids' => "[\"3\",\"4\"]",
			'vat' => "13,52"
		);
		$expected = array(
			'operation' => "change_vat",
			'value' => 13.52,
			'id' => array(
				0 => 3,
				1 => 4
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		
		$post = array(
			'operation' => "change_amount_inc_vat",
			'ids' => "[\"3\",\"4\"]",
			'amount_inc_vat' => "13,52"
		);
		$expected = array(
			'operation' => "change_amount_inc_vat",
			'value' => 13.52,
			'id' => array(
				0 => 3,
				1 => 4
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		
		$post = array(
			'operation' => "change_day",
			'ids' => "[\"30\",\"43\"]",
			'day' => array(
				'd' => 3,
				'm' => 2,
				'Y' => 2013
			)
		);
		$expected = array(
			'operation' => "change_day",
			'value' => 1359846000,
			'id' => array(
				0 => 30,
				1 => 43
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		
		$post = array(
			'operation' => "duplicate",
			'ids' => "[\"30\",\"43\"]",
			'duplicate' => "3m"
		);
		$expected = array(
			'operation' => "duplicate",
			'value' => "3m",
			'id' => array(
				0 => 30,
				1 => 43
			)
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		
		$post = array(
			'operation' => "duplicate",
			'ids' => "[]",
			'duplicate' => "3m"
		);
		$expected = array(
			'operation' => "duplicate"
		);
		$this->assertEqual($writings->clean_from_ajax($post), $expected);
		$this->truncateTable("writings");
	}
	
	function test_change_amount_inc_vat() {
		$writing = new Writing();
		$writing->amount_inc_vat = 125.218;
		$writing->save();
		$writing = new Writing();
		$writing->amount_inc_vat = -3250.21;
		$writing->save();
		$writings = new Writings();
		$writings->select();
		$writings->change_amount_inc_vat(200);
		$writing->load(1);
		$this->assertTrue($writing->amount_inc_vat == 200);
		$writing->load(2);
		$this->assertTrue($writing->amount_inc_vat == 200);
		$this->truncateTable("writings");
	}
	
	function test_duplicate_over_from_ids() {
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->id = array(1, 2);
		$writings->select();
		$writings->duplicate_over_from_ids("3m");
		$writings->id = "";
		$writings->select();
		$this->assertTrue(count($writings) == 8);
		
		$writing->load(3);
		$this->assertTrue($writing->day == mktime(0, 0, 0, 10, 25, 2013));
		
		$writing->load(4);
		$this->assertTrue($writing->day == mktime(0, 0, 0, 11, 25, 2013));
		
		$writing->load(5);
		$this->assertTrue($writing->day == mktime(0, 0, 0, 12, 25, 2013));
		$this->truncateTable("writings");
	}
	
	function test_delete_from_ids() {
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$this->assertTrue($writing->load(1));
		$this->assertTrue($writing->load(2));
		$writings = new Writings();
		$writings->delete_from_ids(array(1, 2));
		
		$this->assertFalse($writing->load(1));
		$this->assertFalse($writing->load(2));
		$this->truncateTable("writings");
	}
	
	function test_change_category() {
		$category = new Category();
		$category->vat = 5.5;
		$category->save();
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->categories_id = 2;
		$writing->vat = 19.6;
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->select();
		$writings->change_category(1);
		
		$writing->load(1);
		$this->assertTrue($writing->categories_id == 1);
		$this->assertTrue($writing->vat == 19.6);
		$writing->load(2);
		$this->assertTrue($writing->categories_id == 1);
		$this->assertTrue($writing->vat == 5.5);
		$this->truncateTable("writings");
	}
	
	
	function test_change_accounting_codes() {		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->accountingcodes_id = 125;
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->select();
		$writings->change_accounting_code(12);
		
		$writing->load(1);
		$this->assertTrue($writing->accountingcodes_id = 12);
		$writing->load(2);
		$this->assertTrue($writing->accountingcodes_id = 12);
		$this->truncateTable("writings");
	}
	
	function test_change_source() {
		$category = new Source();
		$category->save();
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->sources_id = 2;
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->select();
		$writings->change_source(1);
		
		$writing->load(1);
		$this->assertTrue($writing->sources_id == 1);
		$writing->load(2);
		$this->assertTrue($writing->sources_id == 1);
		$this->truncateTable("writings");
	}
	
	function test_change_vat() {
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->vat = 20;
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->select();
		$writings->change_vat(15.5);
		
		$writing->load(1);
		$this->assertTrue($writing->vat == 15.5);
		$writing->load(2);
		$this->assertTrue($writing->vat == 15.5);
		$this->truncateTable("writings");
	}
	
	function test_day() {
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 25, 2013);
		$writing->save();
		
		$writings = new Writings();
		$writings->select();
		$writings->change_day(mktime(0, 0, 0, 10, 25, 2013));
		
		$writing->load(1);
		$this->assertTrue($writing->day == mktime(0, 0, 0, 10, 25, 2013));
		$writing->load(2);
		$this->assertTrue($writing->day == mktime(0, 0, 0, 10, 25, 2013));
		$this->truncateTable("writings");
	}
	
	function test_clean_filter_from_ajax() {
		$post = array(
			"action" => "filter",
			"extra_filter_writings_value" => "test",
			"filter_day_start" => array(
				"d" => 01,
				"m" => 10,
				"Y" => 2013
				),
			"filter_day_stop" => array(
				"d" => 31,
				"m" => 10,
				"Y" => 2013
				),
			"filter_categories_id" => 1,
			"filter_sources_id" => 1,
			"filter_banks_id" => 1,
			"e243c26543db4bd701a1f3563acf584b" => 512,
			"filter_accountingcodes_id" => 546,
			"filter_number" => 124,
			"filter_amount_inc_vat" => 251,
			"filter_comment" => "Test de commentaire"
			);
		$expected = array(
			"search_index" => "test",
			"stop" => mktime(0, 0, 0, 10, 31, 2013),
			"start" => mktime(0, 0, 0, 10, 01, 2013),
			"categories_id" => 1,
			"sources_id" => 1,
			"banks_id" => 1,
			"accountingcodes_id" => 546,
			"number" => 124,
			"amount_inc_vat" => 251,
			"comment" => "Test de commentaire"
		);
		$writings = new Writings();
		$this->assertEqual($expected, $writings->clean_filter_from_ajax($post));
		
		$post = array(
			"action" => "filter",
			"extra_filter_writings_value" => "",
			"filter_day_start" => array(
				"d" => 01,
				"m" => 10,
				"Y" => 2013
				),
			"filter_day_stop" => array(
				"d" => 31,
				"m" => 10,
				"Y" => 2013
				),
			"filter_categories_id" => 0,
			"filter_sources_id" => 0,
			"filter_banks_id" => 0,
			"e243c26543db4bd701a1f3563acf584b" => 512,
			"filter_number" => "",
			"filter_amount_inc_vat" => "",
			"filter_comment" => ""
			);
		$expected = array(
			"stop" => mktime(0, 0, 0, 10, 31, 2013),
			"start" => mktime(0, 0, 0, 10, 01, 2013)
		);
		$writings = new Writings();
		$this->assertEqual($expected, $writings->clean_filter_from_ajax($post));
		
		$post = array(
			"action" => "filter",
			"extra_filter_writings_value" => "",
			"filter_day_start" => array(
				"d" => 01,
				"m" => 10,
				"Y" => 2013
				),
			"filter_day_stop" => array(
				"d" => 31,
				"m" => 10,
				"Y" => 2013
				),
			"filter_categories_id" => "none",
			"filter_sources_id" => "none",
			"filter_banks_id" => "none",
			"e243c26543db4bd701a1f3563acf584b" => 512,
			"filter_number" => "",
			"filter_amount_inc_vat" => "",
			"filter_comment" => ""
			);
		$expected = array(
			"stop" => mktime(0, 0, 0, 10, 31, 2013),
			"start" => mktime(0, 0, 0, 10, 01, 2013),
			"categories_id" => 0,
			"sources_id" => 0,
			"banks_id" => 0
		);
		$writings = new Writings();
		$this->assertEqual($expected, $writings->clean_filter_from_ajax($post));
	}
}
