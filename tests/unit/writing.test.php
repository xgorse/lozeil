<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Writing extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"categories",
			"sources",
			"writings",
			"banks",
			"accountingcodes"
		);
	}
	
	function test_save_load() {
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 209.030100;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 2;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->attachment = 1;
		$writing->number = 132;
		$writing->vat = 19.6;
		$writing->save();
		$writing_loaded = new Writing();
		$writing_loaded->id = 1;
		$writing_loaded->load();
		$this->assertEqual($writing_loaded->categories_id, $writing->categories_id);
		$this->assertEqual($writing_loaded->amount_excl_vat, $writing->amount_excl_vat);
		$this->assertEqual($writing_loaded->amount_inc_vat, $writing->amount_inc_vat);
		$this->assertEqual($writing_loaded->banks_id, $writing->banks_id);
		$this->assertEqual($writing_loaded->comment, $writing->comment);
		$this->assertEqual($writing_loaded->day, $writing->day);
		$this->assertEqual($writing_loaded->id, $writing->id);
		$this->assertEqual($writing_loaded->information, $writing->information);
		$this->assertEqual($writing_loaded->paid, $writing->paid);
		$this->assertEqual($writing_loaded->sources_id, $writing->sources_id);
		$this->assertEqual($writing_loaded->number, $writing->number);
		$this->assertEqual($writing_loaded->attachment, $writing->attachment);
		$this->assertEqual($writing_loaded->vat, $writing->vat);
		$this->truncateTable("writings");
	}
	
	function test_update() {
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 2;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->number = 132;
		$writing->attachment = 0;
		$writing->vat = 19.6;
		$writing->save();
		$writing_loaded = new Writing();
		$writing_loaded->id = 1;
		$writing_loaded->load();
		$writing_loaded->categories_id = 2;
		$writing_loaded->amount_excl_vat = 19.50;
		$writing_loaded->amount_inc_vat = 25;
		$writing_loaded->banks_id = 3;
		$writing_loaded->comment = "Ceci est un autre test";
		$writing_loaded->day = mktime(10, 30, 0, 7, 29, 2013);
		$writing_loaded->information = "Autre complément d'infos";
		$writing_loaded->paid = 1;
		$writing_loaded->sources_id = 1;
		$writing_loaded->number = 2;
		$writing_loaded->attachment = 1;
		$writing_loaded->vat = 5.5;
		$writing_loaded->save();
		$writing_loaded->load(1);
		$this->assertEqual($writing_loaded->categories_id, 2);
		$this->assertEqual($writing_loaded->amount_excl_vat, 23.696682);
		$this->assertEqual($writing_loaded->amount_inc_vat, 25);
		$this->assertEqual($writing_loaded->banks_id, 3);
		$this->assertEqual($writing_loaded->comment, "Ceci est un autre test");
		$this->assertEqual($writing_loaded->day, mktime(10, 30, 0, 7, 29, 2013));
		$this->assertEqual($writing_loaded->id, 1);
		$this->assertEqual($writing_loaded->information, "Autre complément d'infos");
		$this->assertEqual($writing_loaded->paid, 1);
		$this->assertEqual($writing_loaded->sources_id, 1);
		$this->assertEqual($writing_loaded->number, 2);
		$this->assertEqual($writing_loaded->attachment, 1);
		$this->assertEqual($writing_loaded->vat, 5.5);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 2;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->number = 132;
		$writing->vat = 19.6;
		$writing->save();
		$writing_loaded = new Writing();
		$writing_loaded->id = 1;
		$writing_loaded->load();
		$writing_loaded->categories_id = 2;
		$writing_loaded->amount_excl_vat = "";
		$writing_loaded->amount_inc_vat = "";
		$writing_loaded->banks_id = 3;
		$writing_loaded->comment = "Ceci est un autre test";
		$writing_loaded->day = mktime(10, 30, 0, 7, 29, 2013);
		$writing_loaded->information = "Autre complément d'infos";
		$writing_loaded->paid = 1;
		$writing_loaded->sources_id = 1;
		$writing_loaded->number = 2;
		$writing_loaded->vat = "";
		$writing_loaded->save();
		$this->assertEqual($writing_loaded->categories_id, 2);
		$this->assertEqual($writing_loaded->amount_excl_vat, 0);
		$this->assertEqual($writing_loaded->amount_inc_vat, 0);
		$this->assertEqual($writing_loaded->banks_id, 3);
		$this->assertEqual($writing_loaded->comment, "Ceci est un autre test");
		$this->assertEqual($writing_loaded->day, mktime(10, 30, 0, 7, 29, 2013));
		$this->assertEqual($writing_loaded->id, 1);
		$this->assertEqual($writing_loaded->information, "Autre complément d'infos");
		$this->assertEqual($writing_loaded->paid, 1);
		$this->assertEqual($writing_loaded->sources_id, 1);
		$this->assertEqual($writing_loaded->number, 2);
		$this->assertEqual($writing_loaded->vat, 0);
		$this->truncateTable("writings");
	}
	
	function test_delete() {
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->save();
		
		$this->assertTrue($writing->load());
		
		$writing->delete();
		
		$this->assertFalse($writing->load());
		$this->truncateTable("writings");
	}
	
	function test_merge_from() {
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->accountingcodes_id = 5;
		$writing->number = 1;
		$writing->vat = 19.6;
		
		$writing_to_merge = new Writing();
		$writing_to_merge->id = 1;
		$writing_to_merge->categories_id = 2;
		$writing_to_merge->amount_excl_vat = 23.696682;
		$writing_to_merge->amount_inc_vat = 25;
		$writing_to_merge->comment = "Ceci est un autre test";
		$writing_to_merge->day = mktime(10, 0, 0, 8, 26, 2013);
		$writing_to_merge->information = "Autre complément d'infos";
		$writing_to_merge->paid = 1;
		$writing_to_merge->sources_id = 1;
		$writing_to_merge->accountingcodes_id = 2;
		$writing_to_merge->number = 2;
		$writing_to_merge->vat = 5.5;
		$writing_to_merge->search_index = $writing_to_merge->search_index();
		
		$writing->merge_from($writing_to_merge);
		
		$this->assertIdentical($writing_to_merge, $writing);
		
		$writing_to_merge_2 = new Writing();
		$writing_to_merge_2->categories_id = 0;
		$writing_to_merge_2->amount_excl_vat = 0;
		$writing_to_merge_2->amount_inc_vat = 0;
		$writing_to_merge_2->comment = "";
		$writing_to_merge_2->day = 0;
		$writing_to_merge_2->information = "";
		$writing_to_merge_2->paid = 0;
		$writing_to_merge_2->sources_id = 0;
		$writing_to_merge_2->number = 0;
		$writing_to_merge_2->vat = 0;
		
		$writing_to_merge_3 = new Writing();
		$writing_to_merge_3->id = 1;
		$writing_to_merge_3->categories_id = 2;
		$writing_to_merge_3->amount_excl_vat = 0;
		$writing_to_merge_3->amount_inc_vat = 0;
		$writing_to_merge_3->comment = "Ceci est un autre test";
		$writing_to_merge_3->day = 0;
		$writing_to_merge_3->information = "Autre complément d'infos";
		$writing_to_merge_3->paid = 0;
		$writing_to_merge_3->sources_id = 1;
		$writing_to_merge_3->number = 2;
		$writing_to_merge_3->vat = 5.5;
		$writing_to_merge_3->search_index = $writing_to_merge_3->search_index();
		
		$writing->merge_from($writing_to_merge_2);
		
		$this->assertTrue($writing_to_merge_3->categories_id == $writing->categories_id);
		$this->assertTrue($writing_to_merge_3->amount_excl_vat == $writing->amount_excl_vat);
		$this->assertTrue($writing_to_merge_3->amount_inc_vat == $writing->amount_inc_vat);
		$this->assertTrue($writing_to_merge_3->comment == $writing->comment);
		$this->assertTrue($writing_to_merge_3->day == $writing->day);
		$this->assertTrue($writing_to_merge_3->information == $writing->information);
		$this->assertTrue($writing_to_merge_3->paid == $writing->paid);
		$this->assertTrue($writing_to_merge_3->sources_id == $writing->sources_id);
		$this->assertTrue($writing_to_merge_3->number == $writing->number);
		$this->assertTrue($writing_to_merge_3->vat == $writing->vat);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->categories_id = "1";
		$writing->banks_id = 1;
		$writing->save();
		
		$writing2 = new Writing();
		$writing2->categories_id = "2";
		$writing2->banks_id = 2;
		$writing2->save();
		
		$writing->merge_from($writing2);
		
		$writing_loaded = new Writing();
		$writing2_loaded = new Writing();
		$this->assertTrue($writing2_loaded->load(2));
		$this->assertTrue($writing_loaded->load(1));
		$this->assertTrue($writing2_loaded->categories_id == $writing2->categories_id);
		$this->assertTrue($writing2_loaded->banks_id == $writing2->banks_id);
		$this->assertTrue($writing_loaded->categories_id == $writing->categories_id);
		$this->assertTrue($writing_loaded->banks_id == $writing->banks_id);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 4;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->accountingcodes_id = 5;
		$writing->number = 1;
		$writing->vat = 19.6;
		$writing->save();
		
		$writing2 = new Writing();
		$writing2->categories_id = 2;
		$writing2->amount_excl_vat = 19.50;
		$writing2->amount_inc_vat = 25;
		$writing2->comment = "Ceci est un autre test";
		$writing2->day = mktime(10, 0, 0, 8, 26, 2013);
		$writing2->information = "Autre complément d'infos";
		$writing2->paid = 1;
		$writing2->sources_id = 1;
		$writing2->accountingcodes_id = 2;
		$writing2->number = 2;
		$writing2->vat = 5.5;
		$writing2->save();
		
		$writing->merge_from($writing2);
		$writing2_loaded = new Writing();
		$writing_loaded = new Writing();
		$this->assertTrue($writing2_loaded->load(1));
		$this->assertFalse($writing_loaded->load(2));
		
		$this->assertEqual($writing2_loaded->categories_id, 1);
		$this->assertEqual($writing2_loaded->amount_excl_vat, 236.966825);
		$this->assertEqual($writing2_loaded->amount_inc_vat, 250);
		$this->assertEqual($writing2_loaded->banks_id, 4);
		$this->assertEqual($writing2_loaded->comment, "Ceci est un test");
		$this->assertEqual($writing2_loaded->day, mktime(10, 0, 0, 7, 29, 2013));
		$this->assertEqual($writing2_loaded->information, "Complément d'infos");
		$this->assertEqual($writing2_loaded->paid, 0);
		$this->assertEqual($writing2_loaded->sources_id, 2);
		$this->assertEqual($writing2_loaded->accountingcodes_id, 5);
		$this->assertEqual($writing2_loaded->number, 1);
		$this->assertEqual($writing2_loaded->vat, 5.5);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 1;
		$writing->accountingcodes_id = 5;
		$writing->vat = 0;
		
		$writing2 = new Writing();
		$writing2->amount_excl_vat = 19.50;
		$writing2->amount_inc_vat = 25;
		$writing2->accountingcodes_id = 2;
		$writing2->vat = 19.6;
		
		$writing->merge_from($writing2);
		$this->assertTrue($writing->amount_inc_vat == 250);
		$this->assertTrue($writing->vat == 19.6);
		$this->assertTrue($writing->amount_excl_vat == 209.030100);
		$this->assertTrue($writing->accountingcodes_id == 5);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 1;
		$writing->vat = 0;
		
		$writing2 = new Writing();
		$writing2->amount_excl_vat = 19.50;
		$writing2->amount_inc_vat = 25;
		$writing2->vat = 19.6;
		
		$writing2->merge_from($writing);
		
		$this->assertTrue($writing2->amount_inc_vat == 250);
		$this->assertTrue($writing2->vat == 19.6);
		$this->assertTrue($writing2->amount_excl_vat == 209.030100);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 190.50;
		$writing->amount_inc_vat = 250;
		$writing->banks_id = 4;
		$writing->comment = "Ceci est un test";
		$writing->day = mktime(10, 0, 0, 7, 29, 2013);
		$writing->information = "Complément d'infos";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->number = 1;
		$writing->vat = 19.6;
		$writing->save();
		
		$writing2 = new Writing();
		$writing2->categories_id = 2;
		$writing2->amount_excl_vat = 19.50;
		$writing2->amount_inc_vat = 25;
		$writing2->comment = "Ceci est un autre test";
		$writing2->day = mktime(10, 0, 0, 8, 26, 2013);
		$writing2->information = "Autre complément d'infos";
		$writing2->paid = 1;
		$writing2->sources_id = 1;
		$writing2->number = 2;
		$writing2->vat = 5.5;
		$writing2->save();
		
		$writing2->merge_from($writing);
		$writing2_loaded = new Writing();
		$writing_loaded = new Writing();
		$this->assertTrue($writing_loaded->load(2));
		$this->assertFalse($writing2_loaded->load(1));
		
		$this->assertEqual($writing_loaded->categories_id, 1);
		$this->assertEqual($writing_loaded->amount_excl_vat, 209.030100);
		$this->assertEqual($writing_loaded->amount_inc_vat, 250);
		$this->assertEqual($writing_loaded->banks_id, 4);
		$this->assertEqual($writing_loaded->comment, "Ceci est un test");
		$this->assertEqual($writing_loaded->day, mktime(10, 0, 0, 7, 29, 2013));
		$this->assertEqual($writing_loaded->information, "Complément d'infos");
		$this->assertEqual($writing_loaded->paid, 0);
		$this->assertEqual($writing_loaded->sources_id, 2);
		$this->assertEqual($writing_loaded->number, 1);
		$this->assertEqual($writing_loaded->vat, 19.6);
		
		$this->truncateTable("writings");
	}
	
	function test_split() {
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 167.22;
		$writing->amount_inc_vat = 200;
		$writing->banks_id = 2;
		$writing->comment = "Ceci est un commentaire";
		$writing->day = mktime(10, 0, 0, 7, 31, 2013);
		$writing->information = "Informations";
		$writing->paid = 0;
		$writing->sources_id = 2;
		$writing->number = 1;
		$writing->vat = 19.6;
		$writing->save();
		
		$writing->split(250);
		$writing_splited = new Writing();
		$writing_splited->load(2);
		$this->assertEqual($writing->amount_inc_vat, -50);
		$this->assertEqual($writing->amount_excl_vat, -41.806020);
		$this->assertEqual($writing_splited->categories_id, 1);
		$this->assertEqual($writing_splited->amount_excl_vat, 209.030100);
		$this->assertEqual($writing_splited->amount_inc_vat, 250);
		$this->assertEqual($writing_splited->banks_id, 2);
		$this->assertEqual($writing_splited->comment, "Ceci est un commentaire");
		$this->assertEqual($writing_splited->day, mktime(10, 0, 0, 7, 31, 2013));
		$this->assertEqual($writing_splited->paid, 0);
		$this->assertEqual($writing_splited->sources_id, 2);
		$this->assertEqual($writing_splited->number, 1);
		$this->assertEqual($writing_splited->vat, 19.6);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 188.13;
		$writing->amount_inc_vat = 225;
		$writing->paid = 0;
		$writing->number = 1;
		$writing->vat = 19.6;
		$writing->sources_id = 2;
		$writing->day = mktime(10, 0, 0, 7, 31, 2013);
		$writing->save();
		
		$writing->split(225);
		$writing_splited = new Writing();
		$writing_splited->load(2);
		$this->assertEqual($writing->amount_inc_vat, 0);
		$this->assertEqual($writing->amount_excl_vat, 0);
		$this->assertEqual($writing_splited->categories_id, 1);
		$this->assertEqual($writing_splited->amount_excl_vat, 188.127090);
		$this->assertEqual($writing_splited->amount_inc_vat, 225);
		$this->assertEqual($writing_splited->paid, 0);
		$this->assertEqual($writing_splited->number, 1);
		$this->assertEqual($writing_splited->vat, 19.6);
		$this->assertEqual($writing_splited->sources_id, 2);
		$this->assertEqual($writing_splited->day, mktime(10, 0, 0, 7, 31, 2013));
		
		$this->truncateTable("writings");
	}
	
	function test_form() {
		$_SESSION['start'] = time();
		$writing = new Writing();
		$form = $writing->form();
		$this->assertPattern("/".date('m')."/", $form);
		$this->assertPattern("/".date('Y')."/", $form);
		$this->assertPattern("/<option value=\"0\" selected=\"selected\">--<\/option>/", $form);
		$this->assertPattern("/value=\"insert\"/", $form);
		$this->assertNoPattern("/value=\"edit\"/", $form);
		$category = new Category();
		$category->name = "Category 1";
		$category->save();
		$bank = new Bank();
		$bank->name = "Bank 1";
		$bank->save();
		$source = new Source();
		$source->name = "Source 1";
		$source->save();
		$form = $writing->form();
		$this->assertPattern("/".date('d', time())."/", $form);
		$this->assertPattern("/".date('m', time())."/", $form);
		$this->assertPattern("/".date('Y', time())."/", $form);
		$this->assertPattern("/<option value=\"1\">Source 1<\/option>/", $form);
		$this->assertNoPattern("/<option value=\"1\">Bank 1<\/option>/", $form);
		$this->assertPattern("/<option value=\"1\">Category 1<\/option>/", $form);
		$this->assertPattern("/value=\"insert\"/", $form);
		$this->assertNoPattern("/value=\"edit\"/", $form);
		
		$this->truncateTable("writings");
		$this->truncateTable("sources");
		$this->truncateTable("categories");
		$this->truncateTable("banks");
	}
	
	function test_form_duplicate() {
		$writing = new Writing();
		$writing->id = 40;
		$form_duplicate = $writing->form_duplicate();
		$this->assertPattern("/class=\"duplicate\"/", $form_duplicate);
		$this->assertPattern("/value=\"40\"/", $form_duplicate);
		$this->assertPattern("/table_writings_duplicate_submit/", $form_duplicate);
		$this->assertPattern("/table_writings_duplicate_amount/", $form_duplicate);
	}
	
	function test_form_delete() {
		$writing = new Writing();
		$writing->id = 40;
		$form_delete = $writing->form_delete();
		$this->assertPattern("/class=\"delete\"/", $form_delete);
		$this->assertPattern("/value=\"40\"/", $form_delete);
		$this->assertPattern("/table_writings_delete_submit/", $form_delete);
		$this->assertPattern("/javascript:return confirm/", $form_delete);
	}
	
	function test_form_split() {
		$writing = new Writing();
		$writing->id = 40;
		$form_split = $writing->form_split();
		$this->assertPattern("/class=\"split\"/", $form_split);
		$this->assertPattern("/value=\"40\"/", $form_split);
		$this->assertPattern("/table_writings_split_submit/", $form_split);
		$this->assertPattern("/table_writings_split_amount/", $form_split);
	}
		
	function test_duplicate() {
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 8, 26, 2013);
		$writing->save();
		$writing->duplicate(5);
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 6);
		$writing->load(1);
		$this->assertEqual(mktime(0, 0, 0, 8, 26, 2013), $writing->day);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 9, 26, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 10, 26, 2013), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 11, 26, 2013), $writing->day);
		$writing->load(5);
		$this->assertEqual(mktime(0, 0, 0, 12, 26, 2013), $writing->day);
		$writing->load(6);
		$this->assertEqual(mktime(0, 0, 0, 1, 26, 2014), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3q');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 12, 5, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 3, 5, 2014), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 6, 5, 2014), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3Q');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 12, 5, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 3, 5, 2014), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 6, 5, 2014), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3t');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 12, 5, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 3, 5, 2014), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 6, 5, 2014), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3T');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 12, 5, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 3, 5, 2014), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 6, 5, 2014), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3y');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2014), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2015), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2016), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3Y');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2014), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2015), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2016), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3a');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2014), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2015), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2016), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3A');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2014), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2015), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 9, 5, 2016), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3m');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 10, 5, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 11, 5, 2013), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 12, 5, 2013), $writing->day);
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(0, 0, 0, 9, 5, 2013);
		$writing->save();
		$writing->duplicate('3M');
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 4);
		$writing->load(2);
		$this->assertEqual(mktime(0, 0, 0, 10, 5, 2013), $writing->day);
		$writing->load(3);
		$this->assertEqual(mktime(0, 0, 0, 11, 5, 2013), $writing->day);
		$writing->load(4);
		$this->assertEqual(mktime(0, 0, 0, 12, 5, 2013), $writing->day);
		$this->truncateTable("writings");
	}
	
	function test_show_further_information() {
		$writing = new Writing();
		$this->assertEqual("", $writing->show_further_information());
		$writing->information = "Ceci est un complément d'information";
		$this->assertPattern("/class=\"table_writings_comment_further_information\"/", $writing->show_further_information());
		$this->assertPattern("/Ceci est un complément d'information/", $writing->show_further_information());
	}
	
	function test_search_index() {
		$bank = new Bank();
		$bank->banks_id = 1;
		$bank->name = "cic";
		$bank->save();
		$source = new Source();
		$source->sources_id = 1;
		$source->name = "source 1";
		$source->save();
		$category = new Category();
		$category->categories_id = 1;
		$category->name = "chèque";
		$category->save();
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->amount_excl_vat = 167.22;
		$writing->amount_inc_vat = 200;
		$writing->banks_id = 1;
		$writing->comment = "Ceci est un commentaire";
		$writing->information = "Ceci est une info";
		$writing->day = mktime(10, 0, 0, 7, 31, 2013);
		$writing->sources_id = 1;
		$writing->number = 1213546;
		$writing->vat = 19.6;
		
		$this->assertPattern("/cic/", $writing->search_index());
		$this->assertPattern("/source 1/", $writing->search_index());
		$this->assertPattern("/chèque/", $writing->search_index());
		$this->assertPattern("/Ceci est un commentaire/", $writing->search_index());
		$this->assertPattern("/Ceci est une info/", $writing->search_index());
		$this->assertPattern("/1213546/", $writing->search_index());
		$this->assertPattern("/31\/07\/2013/", $writing->search_index());
		$this->assertPattern("/19.6/", $writing->search_index());
		$this->assertPattern("/167.22/", $writing->search_index());
		$this->assertPattern("/200/", $writing->search_index());
		
		$this->truncateTable("writings");
		$this->truncateTable("sources");
		$this->truncateTable("categories");
		$this->truncateTable("banks");
	}
	
	function test_forward() {
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward(1);
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 9, 11, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward(3);
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 11, 11, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("1m");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 9, 11, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("3M");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 11, 11, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("1t");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 11, 11, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("3T");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 5, 11, 2014), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("1q");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 11, 11, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("3Q");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 5, 11, 2014), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("1y");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 8, 11, 2014), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("3Y");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 8, 11, 2016), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("1a");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 8, 11, 2014), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 8, 11, 2013);
		$writing->save();
		$writing->forward("3A");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 8, 11, 2016), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 9, 19, 2013);
		$writing->save();
		$writing->forward("3j");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 9, 22, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 9, 19, 2013);
		$writing->save();
		$writing->forward("12J");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 10, 1, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 9, 19, 2013);
		$writing->save();
		$writing->forward("3d");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 9, 22, 2013), $writing->day);
		
		$this->truncateTable("writings");
		
		$writing = new Writing();
		$writing->day = mktime(10, 0, 0, 9, 19, 2013);
		$writing->save();
		$writing->forward("12D");
		$writings = new Writings();
		$writings->select();
		$this->assertTrue(count($writings) == 1);
		$writing->load(1);
		$this->assertEqual(mktime(10, 0, 0, 10, 1, 2013), $writing->day);
		
		$this->truncateTable("writings");
	}
	
	function test_calculate_amount_excl_vat() {
		$writing = new Writing();
		$writing->amount_inc_vat = 200;
		$writing->vat = 19.6;
		$this->assertEqual($writing->calculate_amount_excl_vat(), 167.224080);
		$writing = new Writing();
		$writing->amount_inc_vat = 200;
		$writing->vat = 0;
		$this->assertEqual($writing->calculate_amount_excl_vat(), 200);
		$writing = new Writing();
		$writing->amount_inc_vat = 200;
		$writing->vat = -100;
		$this->assertEqual($writing->calculate_amount_excl_vat(), 0);
		$this->truncateTable("writings");
	}
	
	function test_get_data() {
		$writing = new Writing();
		$writing->accountingcodes_id = 601;
		$writing->categories_id = 3;
		$writing->comment = "Ceci est un commentaire";
		$writing->amount_inc_vat = 200;
		$writing->vat = 19.6;
				
		$expected = array(
			'classification_target' => array(
				'categories' => 3,
				'accountingcodes' => 601
			),
			'classification_data' => array(
				'comment' => array(
					'Ceci',
					'est',
					'commentaire'
				),
				'amount_inc_vat' => array(
					200
				)
			)
		);
	
		$this->assertEqual($expected, $writing->get_data());
	}
	
	function test_different_from() {
		$writing = new Writing();
		$writing->accountingcodes_id = 601;
		$writing->categories_id = 3;
		$writing->comment = "Ceci est un commentaire";
		$writing->amount_inc_vat = 200;
		$writing->sources_id = 2;
		
		$writing2 = new Writing();
		$writing2->accountingcodes_id = 601;
		$writing2->categories_id = 3;
		$writing2->comment = "Ceci est un commentaire";
		$writing2->amount_inc_vat = 200;
		$writing2->sources_id = 2;
		$this->assertFalse($writing->different_from($writing2));
		
		$writing2->categories_id = 2;
		$this->assertTrue($writing->different_from($writing2));
		$writing2->categories_id = 3;
		$writing2->comment = "Commentaire";
		$this->assertTrue($writing->different_from($writing2));
		$writing2->comment = "Ceci est un commentaire";
		$writing2->sources_id = 3;
		$this->assertTrue($writing->different_from($writing2));
		$writing2->sources_id = 2;
		$writing->accountingcodes_id = 51;
		$this->assertTrue($writing->different_from($writing2));
		$this->truncateTable("writings");
	}
	
	function test_clean() {
		$post = array(
			'action' => 'edit',
			'writings_id' => '310',
			'datepicker' => array (
							'd' => '09',
							'm' => '07',
							'Y' => '2013'
							  ),
			'categories_id' => '2',
			'sources_id' => '0',
			'e243c26543db4bd701a1f3563acf584b' => '512',
			'accountingcodes_id' => '546',
			'number' => '12345',
			'amount_excl_vat' => '-15000.000000' ,
			'vat' => '0.00',
			'amount_inc_vat' => '-15000.000000',
			'comment' => 'COOPA 09/07',
			'paid' => '1'
		);
		
		$cleaned = array(
			'day' => mktime(0, 0, 0, 7, 9, 2013),
			'accountingcodes_id' => '546',
			'categories_id' => '2',
			'sources_id' => '0',
			'paid' => '1',
			'comment' => 'COOPA 09/07',
			'amount_excl_vat' => '-15000.000000' ,
			'amount_inc_vat' => '-15000.000000',
			'vat' => '0.00',
			'number' => '12345',
			);
		$writing = new Writing();
		$this->assertEqual($cleaned, $writing->clean($post));
		
		$post = array(
			'action' => 'edit',
			'writings_id' => '310',
			'datepicker' => array (
							'd' => '09',
							'm' => '07',
							'Y' => '2013'
							  ),
			'categories_id' => '2',
			'sources_id' => '0',
			'e243c26543db4bd701a1f3563acf584b' => '',
			'accountingcodes_id' => '0',
			'number' => '12345',
			'amount_excl_vat' => '-15000.000000' ,
			'vat' => '0.00',
			'amount_inc_vat' => '-15000.000000',
			'comment' => 'COOPA 09/07',
			'paid' => '1'
		);
		
		$cleaned = array(
			'day' => mktime(0, 0, 0, 7, 9, 2013),
			'categories_id' => '2',
			'sources_id' => '0',
			'paid' => '1',
			'comment' => 'COOPA 09/07',
			'amount_excl_vat' => '-15000.000000' ,
			'amount_inc_vat' => '-15000.000000',
			'vat' => '0.00',
			'number' => '12345',
			);
		$this->assertEqual($cleaned, $writing->clean($post));
		
		$post = array(
			'action' => 'edit',
			'writings_id' => '310',
			'datepicker' => array (
							'd' => '09',
							'm' => '07',
							'Y' => '2013'
							  ),
			'categories_id' => '2',
			'sources_id' => '0',
			'e243c26543db4bd701a1f3563acf584b' => '',
			'number' => '12345',
			'amount_excl_vat' => '-15000.000000' ,
			'vat' => '0.00',
			'amount_inc_vat' => '-15000.000000',
			'comment' => 'COOPA 09/07',
			'paid' => '1'
		);
		
		$cleaned = array(
			'day' => mktime(0, 0, 0, 7, 9, 2013),
			'accountingcodes_id' => '0',
			'categories_id' => '2',
			'sources_id' => '0',
			'paid' => '1',
			'comment' => 'COOPA 09/07',
			'amount_excl_vat' => '-15000.000000' ,
			'amount_inc_vat' => '-15000.000000',
			'vat' => '0.00',
			'number' => '12345',
			);
		$this->assertEqual($cleaned, $writing->clean($post));
		$this->truncateTable("writings");
	}
	
	function test_is_recently_modified() {
		$writing = new Writing();
		$writing->save();
		$writing->load(1);
		$this->assertTrue($writing->is_recently_modified());
		$writing->timestamp = $writing->timestamp - 11;
		$this->assertFalse($writing->is_recently_modified());
		$this->truncateTable("writings");
	}
}
