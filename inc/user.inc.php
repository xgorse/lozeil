<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class User extends Record  {
	public $id = 0;
	public $name = "";
	public $username = "";
	public $password = "";
	public $email = "";

	protected $db = null;

	function __construct($user_id = 0, $db = null) {
		$this->id = (int)$user_id;

		if ($db === null) {
			$db = new db();
		}

		$this->db = $db;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load($id = null) {
		if (($id === null or $id == 0) and ($this->id === null or $this->id == 0)) {
			return false;

		} else {
			if ($id === null) {
				$id = $this->id;
			}
			return parent::load($this->db->config['table_users'], array('id' => (int)$id));
		}
	}
	
	function save() {
		if (is_numeric($this->id) and $this->id != 0) {
			$this->id = $this->update();

		} else {
			$this->id = $this->insert();
		}

		return $this->id;
	}
	
	function insert() {
		$result = $this->db->id("
			INSERT INTO ".$this->db->config['table_users']."
			SET name = ".$this->db->quote($this->name).", 
			username = ".$this->db->quote($this->username).",
			password = ".$this->db->quote($this->password).",
			email = ".$this->db->quote($this->email)
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __('user'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_users'].
			" SET name = ".$this->db->quote($this->name).",
			username = ".$this->db->quote($this->username).",
			password = ".$this->db->quote($this->password).",
			email = ".$this->db->quote($this->email)."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('user'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_users'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "d", __('user'));

		return $this->id;
	}
}
