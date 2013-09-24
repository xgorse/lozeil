<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writing_Imported extends Record {
	public $id = 0;
	public $hash = "";
	public $banks_id = 0;
	public $sources_id = 0;
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
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
			return parent::load($this->db->config['table_writingsimported'], array('id' => (int)$id));
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
			INSERT INTO ".$this->db->config['table_writingsimported']."
			SET hash = ".$this->db->quote($this->hash).",
				banks_id = ".(int)$this->banks_id.",
				sources_id = ".(int)$this->sources_id
		);
		$this->id = $result[2];
		$this->db->status($result[1], "u", __('writings imported'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_writingsimported'].
			" SET hash = ".$this->db->quote($this->hash).",
				banks_id = ".(int)$this->banks_id.",
				sources_id = ".(int)$this->sources_id."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('writings imported'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_writingsimported'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "u", __('writings imported'));

		return $this->id;
	}
}
