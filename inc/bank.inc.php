<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Bank extends Record {
	public $id = 0;
	public $name = "";
	public $selected = 0;
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load(array $key = array(), $table = "banks", $columns = null) {
		return parent::load($key, $table, $columns);
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
			INSERT INTO ".$this->db->config['table_banks']."
			SET name = ".$this->db->quote($this->name).", ".
			"selected = ".$this->selected
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __('bank'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_banks'].
			" SET name = ".$this->db->quote($this->name).", ".
			"selected = ".$this->selected."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('bank'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_banks'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "d", __('bank'));

		return $this->id;
	}
	
	function is_deletable() {
		$result = $this->db->value_exists("SELECT count(1) FROM ".$this->db->config['table_writings'].
			" WHERE banks_id = '".$this->id."'"
		);
		return !$result;
	}
}
