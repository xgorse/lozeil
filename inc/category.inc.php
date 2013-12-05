<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Category extends Record {
	public $id = 0;
	public $name = "";
	public $vat = 0;
	public $vat_category = 0;
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load(array $key = array(), $table = "categories", $columns = null) {
		if (empty($key) or $key->id == 0) {
			if ($this->id === 0) {
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
			INSERT INTO ".$this->db->config['table_categories']."
			SET name = ".$this->db->quote($this->name).",
				vat = ".(float)$this->vat.",
				vat_category = ".(float)$this->vat_category
		);
		$this->id = $result[2];
		$this->db->status($result[1], "i", __('category'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_categories']." 
			SET name = ".$this->db->quote($this->name).", 
				vat = ".(float)$this->vat.",
				vat_category = ".(float)$this->vat_category."
				WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('category'));

		return $this->id;
	}


	function delete() {
		if (is_numeric($this->id) and $this->id != 0) {
			$result = $this->db->query("DELETE FROM ".$this->db->config['table_categories'].
				" WHERE id = '".$this->id."'"
			);
			$this->db->status($result[1], "d", __('category'));
		}
		
		return $this->id;
	}
	
	function is_deletable() {
		$result = $this->db->value_exists("SELECT count(1) FROM ".$this->db->config['table_writings'].
			" WHERE categories_id = '".$this->id."'"
		);
		return !$result;
	}
	
	function is_in_use() {
		$result = $this->db->value_exists("SELECT count(1) FROM ".$this->db->config['table_writings'].
			" WHERE categories_id = '".$this->id."'"
		);
		return $result;
	}
	
	function clean($post) {
		$vat_category = 0;
		$cleaned = array();
		if (!empty($post['name_new'])) {
			$cleaned[0] = array (
				'name' => $post['name_new'],
				'vat' => str_replace(",", ".", $post['vat_new']),
				'vat_category' => 0
			);
			if (isset($post['vat_category'])) {
				$cleaned[0]['vat_category'] = 1;
				$vat_category++;
			}
		}
		
		if (isset($post['category'])) {
			foreach ($post['category'] as $id => $values) {
				$cleaned[$id] = array (
					'name' => $values['name'],
					'vat' => str_replace(",", ".", $values['vat']),
					'vat_category' => 0
				);
				if (isset($values['vat_category'])) {
					$cleaned[$id]['vat_category'] = 1;
					$vat_category++;
				}
			}
		}
		if ($vat_category > 1) {
			return false;
		} else {
			return $cleaned;
		}
	}
}
