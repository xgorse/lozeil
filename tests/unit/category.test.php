<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Category extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"writings",
			"categories"
		);
	}
	
	function test_save_load() {
		$category = new Category();
		$category->name = "première category";
		$category->vat = "";
		$category->vat_category = 0;
		$category->save();
		$category = new Category();
		$category->name = "première category";
		$category->vat = "15.26";
		$category->save();
		$category = new Category();
		$category->name = "première category";
		$category->vat_category = 1;
		$category->save();
		$category_loaded = new Category();
		$this->assertTrue($category_loaded->load(1));
		$this->assertEqual($category_loaded->name, $category->name);
		$this->assertTrue($category_loaded->vat == 0);
		$this->assertTrue($category_loaded->vat_category == 0);
		$this->assertTrue($category_loaded->load(2));
		$this->assertTrue($category_loaded->vat == 15.26);
		$this->assertTrue($category_loaded->vat_category == 0);
		$this->assertTrue($category_loaded->load(3));
		$this->assertTrue($category_loaded->vat == 0);
		$this->assertTrue($category_loaded->vat_category == 1);
		$this->assertFalse($category_loaded->load(4));
		$this->truncateTable("categories");
	}
	
	function test_update() {
		$category = new Category();
		$category->name = "premier category";
		$category->vat = "20.56";
		$category->vat_category = 0;
		$category->save();
		$category_loaded = new Category();
		$category_loaded->id = 1;
		$category_loaded->name = "changement de nom";
		$category_loaded->vat = "";
		$category_loaded->vat_category = 1;
		$category_loaded->update();
		$category_loaded2 = new Category();
		$this->assertTrue($category_loaded2->load(1));
		$this->assertNotEqual($category_loaded2->name, $category->name);
		$this->assertNotEqual($category_loaded2->vat, $category->vat);
		$this->assertNotEqual($category_loaded2->vat_category, $category->vat_category);
		$this->truncateTable("categories");
	}
	
	function test_delete() {
		$category = new Category();
		$category->name = "première category";
		$category->save();
		$category_loaded = new Category();
		$this->assertTrue($category_loaded->load(1));
		$category->delete();
		$this->assertFalse($category_loaded->load(1));
		$this->truncateTable("categories");
	}
	
	function test_is_deletable() {
		$category = new Category();
		$category->id = 1;
		$category->save();
		$this->assertTrue($category->is_deletable());
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->save();
		$this->assertFalse($category->is_deletable());
		$this->assertFalse($category->is_deletable());
		$this->truncateTable("categories");
		$this->truncateTable("writings");
	}
	
	function test_is_in_use() {
		$category = new Category();
		$category->name = "premiere catégorie";
		$category->save();
		$this->assertFalse($category->is_in_use());
		$writing = new Writing();
		$writing->categories_id = 1;
		$writing->save();
		$this->assertTrue($category->is_in_use());
		$this->truncateTable("categories");
		$this->truncateTable("writings");
	}
	
	function test_clean() {
		$post = array (
			'name_new' => 'new',
			'vat_new' => '5.5',
			'category' => 
			array (
				1 => 
				array (
				  'name' => 'Salaire',
				  'vat' => '0.00',
				),
				2 => 
				array (
				  'name' => 'Transport',
				  'vat' => '5.50',
				  'vat_category' => '1',
				),
			)
		);
		$category = new Category();
		$cleaned = $category->clean($post);
		$this->assertEqual($cleaned, array (
				0 => array (
					'name' => 'new',
					'vat' => '5.5',
					'vat_category' => 0
				),
				1 => 
					array (
					'name' => 'Salaire',
					'vat' => '0.00',
					'vat_category' => 0
				),
				2 => 
					array (
					'name' => 'Transport',
					'vat' => '5.50',
					'vat_category' => '1',
				),
			)
		);
		
		$post = array (
			'name_new' => 'test',
			'vat_new' => '',
			'vat_category' => '1',
			'category' => 
			array (
				2 => 
				array (
				  'name' => 'Transport',
				  'vat' => '5.50'
				),
			)
		);
		$cleaned = $category->clean($post);
		$this->assertEqual($cleaned, array (
				0 => 
					array (
					'name' => 'test',
					'vat' => '',
					'vat_category' => '1',
				),
				2 => 
					array (
					'name' => 'Transport',
					'vat' => '5.50',
					'vat_category' => '0',
				),
			)
		);
		
		$post = array (
			'name_new' => 'new',
			'vat_new' => '5.5',
			'vat_category' => '1',
			'category' => 
			array (
				1 => 
				array (
				  'name' => 'Salaire',
				  'vat' => '0.00',
				),
				2 => 
				array (
				  'name' => 'Transport',
				  'vat' => '5.50',
				  'vat_category' => '1',
				),
			)
		);
		$cleaned = $category->clean($post);
		$this->assertEqual($cleaned, false);
	}
}
