<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Source extends Record {
	public $id = 0;
	public $name = "";
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load(array $key = array(), $table = "sources", $columns = null) {
		if (empty($key) or $key->id == 0) {
			if ($this->id == 0) {
				return false;
			} else {
				$key = array ("id" => $this->id);
			}
		}
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
			INSERT INTO ".$this->db->config['table_sources']."
			SET name = ".$this->db->quote($this->name)
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __('source'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_sources'].
			" SET name = ".$this->db->quote($this->name)."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('source'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_sources'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "d", __('source'));

		return $this->id;
	}
	
	function is_deletable() {
		$result = $this->db->value_exists("SELECT count(1) FROM ".$this->db->config['table_writings'].
			" WHERE sources_id = '".$this->id."'"
		);
		return !$result;
	}
}
