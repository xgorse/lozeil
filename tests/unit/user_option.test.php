<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_User_Option extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"useroptions"
		);
	}
	function test_insert() {
		$this->truncateTable('useroptions');

		$option = new User_Option();
		$option->id = 0;
		$name = uniqid();
		$option->name = $name;
		$value = uniqid();
		$option->value = $value;

		$this->assertTrue($option->insert());

		$this->assertEqual($option->id, 1);
		$this->assertEqual($option->name, $name);
		$this->assertEqual($option->value, $value);
		foreach ($option as $column => $value) {
			$query = "SELECT ".$column." FROM ".$this->db->config['table_useroptions'];
			$this->assertEqual($this->db->value($query), $value);
		}

		$this->truncateTable('useroptions');
	}

	function test_update() {
		$this->truncateTable('useroptions');

		$option = new User_Option();
		$option->id = 0;
		$name = uniqid();
		$option->name = $name;
		$value = uniqid();
		$option->value = $value;

		$option->insert();

		$this->assertEqual($option->id, 1);
		$this->assertEqual($option->name, $name);

		$value = uniqid();
		$option->value = $value;

		$this->assertTrue($option->update());

		$this->assertEqual($option->id, 1);
		$this->assertEqual($option->name, $name);
		$this->assertEqual($option->name, $name);
		$this->assertEqual($option->value, $value);

		foreach ($option as $column => $value) {
			$query = "SELECT ".$column." FROM ".$this->db->config['table_useroptions'];
			$this->assertEqual($this->db->value($query), $value);
		}

		$option->id = 0;
		$this->assertFalse($option->update());
		$this->truncateTable('useroptions');

		$option->id = 1;
		$this->assertFalse($option->update());
		$this->truncateTable('useroptions');
	}

	function test_delete() {
		$this->truncateTable('useroptions');

		$option = new User_Option();
		$option->id = 0;
		$name = uniqid();
		$option->name = $name;

		$option->insert();

		$this->assertEqual($option->id, 1);
		$this->assertEqual($option->name, $name);

		$this->assertTrue($option->delete());

		$this->assertEqual($option->id, 0);
		$this->assertEqual($option->name, $name);

		$this->assertFalse($option->delete());

		$this->truncateTable('useroptions');
	}

	function test_save() {
		$this->truncateTable('useroptions');

		$option = new User_Option();
		$option->id = 0;
		$name = uniqid();
		$option->name = $name;
		$value = uniqid();
		$option->value = $value;

		$option->save();

		$this->assertEqual($option->id, 1);
		$this->assertEqual($option->name, $name);
		$this->assertEqual($option->value, $value);

		$value = uniqid();
		$option->value = $value;

		$this->assertTrue($option->save());

		$this->assertEqual($option->id, 1);
		$this->assertEqual($option->name, $name);
		$this->assertEqual($option->value, $value);

		foreach ($option as $column => $value) {
			$query = "SELECT ".$column." FROM ".$this->db->config['table_useroptions'];
			$this->assertEqual($this->db->value($query), $value);
		}

		$this->truncateTable('useroptions');
	}

	function test_match_existing() {
		$option = new User_Option();
		$this->assertFalse($option->match_existing());

		$name = uniqid();
		$option->name = $name;
		$value = uniqid();
		$option->value = $value;
		$option->insert();
		$id = $option->id;

		$option = new User_Option();
		$option->name = $name;
		$this->assertTrue($option->match_existing(array('name')));
		$this->assertEqual($option->id, $id);

		$option = new User_Option();
		$this->assertTrue($option->match_existing(array('name' => $name)));
		$this->assertEqual($option->id, $id);

		$option = new User_Option();
		$this->assertFalse($option->match_existing(array('name' => uniqid())));
		$this->assertNull($option->id, $id);

		$this->truncateTable('useroptions');
	}

	private static function get_db() {
		static $db = null;

		if ($db === null) {
			$db = new db();
		}

		return $db;
	}
}
