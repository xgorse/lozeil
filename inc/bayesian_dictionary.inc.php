<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Bayesian_Dictionary extends Record {
	public $id = 0;
	public $word = "";
	public $field = "";
	public $categories_id = 0;
	public $occurrences = 0;
	
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
			return parent::load($this->db->config['table_bayesiandictionaries'], array('id' => (int)$id));
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
			INSERT INTO ".$this->db->config['table_bayesiandictionaries']." 
			SET word = ".$this->db->quote($this->word).",
			field = ".$this->db->quote($this->field).",
			categories_id = ".(int)$this->categories_id.",
			occurrences = ".(int)$this->occurrences
		);
		$this->id = $result[2];
		$this->db->status($result[1], "u", __('bayesian dictionary'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_bayesiandictionaries'].
			" SET word = ".$this->db->quote($this->word).", 
			field = ".$this->db->quote($this->field).", 
			categories_id = ".(int)$this->categories_id.", 
			occurrences = ".(int)$this->occurrences." 
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('bayesian dictionary'));

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_bayesiandictionaries'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "u", __('bayesian dictionary'));

		return $this->id;
	}
	
	function truncateTable() {
		$result = $this->db->query("TRUNCATE TABLE ".$this->db->config['table_bayesiandictionaries']);
		$this->db->status($result[1], "u", __('bayesian dictionary'));
	}


	function exists() {
		$result = $this->db->value_exists("SELECT count(1) FROM ".$this->db->config['table_bayesiandictionaries'].
			" WHERE word = ".$this->db->quote($this->word)." AND categories_id = ".$this->db->quote($this->categories_id)." AND field = ".$this->db->quote($this->field)
		);
		return $result;
	}
	
	function getId() {
		$result = $this->db->query("SELECT id FROM ".$this->db->config['table_bayesiandictionaries'].
			" WHERE (word = ".$this->db->quote($this->word)." AND categories_id = ".$this->categories_id." AND field = ".$this->db->quote($this->field).")"
		);
		$this->db->status($result[1], "u", __('bayesian dictionary'));
		
		return $this->db->fetchArray($result[0]);
	}
	
	function getData(Writing $writing) {
		preg_match_all('(\w{3,})u', $writing->comment, $matches['comment']);
		$matches['comment'] = $matches['comment'][0];
		
		$matches['amount_inc_vat'] = $writing->amount_inc_vat;
		$matches['categories_id'] = $writing->categories_id;
		
		return $matches;
	}
	
	function addData(Writing $writing) {
		if ($writing->categories_id > 0) {
			$data = $this->getData($writing);
			foreach ($data['comment'] as $word) {
				$bayesiandictionary = new Bayesian_Dictionary();
				$bayesiandictionary->word = $word;
				$bayesiandictionary->categories_id = $writing->categories_id;
				$bayesiandictionary->field = "comment";
				if (!$bayesiandictionary->exists()) {
					$bayesiandictionary->occurrences = 1;
				} else {
					$id = $bayesiandictionary->getId();
					$bayesiandictionary->load((int)$id['id']);
					$bayesiandictionary->occurrences += 1;
				}
				$bayesiandictionary->save();
			}
			$bayesiandictionary = new Bayesian_Dictionary();
			$bayesiandictionary->word = $writing->amount_inc_vat;
			$bayesiandictionary->categories_id = $writing->categories_id;
			$bayesiandictionary->field = "amount_inc_vat";
			if (!$bayesiandictionary->exists()) {
				$bayesiandictionary->occurrences = 1;
			} else {
				$id = $bayesiandictionary->getId();
				$bayesiandictionary->load((int)$id['id']);
				$bayesiandictionary->occurrences += 1;
			}
			$bayesiandictionary->save();
		}
	}
}
