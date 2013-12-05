<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Accounting_Code extends Record {
	public $id = 0;
	public $name = "";
	public $number = "";
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load($id = null, $table = "accountingcodes", $columns = null) {
		if (($id === null or $id == 0) and ($this->id === null or $this->id == 0)) {
			return false;

		} else {
			if ($id === null) {
				$id = $this->id;
			}
			return parent::load($id, $table, $columns);
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
			INSERT INTO ".$this->db->config['table_accountingcodes']."
			SET name = ".$this->db->quote($this->name)." ,
				number = ".$this->db->quote($this->number)
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __('accounting plan'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_accountingcodes'].
			" SET name = ".$this->db->quote($this->name)." ,
				number = ".$this->db->quote($this->number)."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('accounting plan'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_accountingcodes'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "d", __('accounting plan'));

		return $this->id;
	}
	
	function fullname() {
		return $this->number." - ".$this->name;
	}
}
