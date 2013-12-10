<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_User extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"users"
		);
	}
	
	function test_save_load() {
		$user = new User();
		$user->name = "user";
		$user->username = "admin";
		$user->password = "pass";
		$user->email = "admin@noparking.net";
		$user->save();
		$user_loaded = new User();
		$user_loaded->load(array('id' => 1));
		$this->assertEqual($user_loaded->name, $user->name);
		$this->assertEqual($user_loaded->username, $user->username);
		$this->assertEqual($user_loaded->password, "*196BDEDE2AE4F84CA44C47D54D78478C7E2BD7B7");
		$this->assertEqual($user_loaded->email, $user->email);
		$this->truncateTable("users");
	}
	
	function test_update() {
		$user = new User();
		$user->name = "user";
		$user->username = "admin";
		$user->password = "pass";
		$user->email = "admin@noparking.net";
		$user->save();
		$user_loaded = new User();
		$user_loaded->id = 1;
		$user_loaded->name = "autre user";
		$user_loaded->username = "autre admin";
		$user_loaded->password = "autrepass";
		$user_loaded->email = "autreadmin@noparking.net";
		$user_loaded->update();
		$user_loaded2 = new User();
		$this->assertTrue($user_loaded2->load(array('id' => 1)));
		$this->assertNotEqual($user_loaded2->name, $user->name);
		$this->assertNotEqual($user_loaded2->username, $user->username);
		$this->assertNotEqual($user_loaded2->password, $user->password);
		$this->assertNotEqual($user_loaded2->email, $user->email);
		$this->truncateTable("users");
	}
	
	function test_delete() {
		$user = new User();
		$user->name = "premier user";
		$user->save();
		$user_loaded = new User();
		$this->assertTrue($user_loaded->load(array('id' => 1 )));
		$user->delete();
		$this->assertFalse($user_loaded->load(array('id' => 1 )));
	}
}
