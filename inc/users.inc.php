<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Users extends Collector {
	public $filters = null;
	
	function __construct($db = null, $class = null, $table = null) {
		if ($class === null) {
			$class = substr(__CLASS__, 0, -1);
		}
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_users'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
	
	function get_where() {
		$query_where = parent::get_where();
		
		if (isset($this->id) and !empty($this->id)) {
			if (!is_array($this->id)) {
				$this->id = array((int)$this->id);
			}
			$query_where[] = $this->db->config['table_users'].".id IN ".array_2_list($this->id);
		}
		if (isset($this->filters['username'])) {
			$query_where[] = $this->db->config['table_users'].".username = ".$this->db->quote($this->filters['username']);
		}
		if (isset($this->filters['email'])) {
			$query_where[] = $this->db->config['table_users'].".email = ".$this->db->quote($this->filters['email']);
		}
		
		return $query_where;
	}
	
	function filter_with() {
		$elements = func_get_args();
		foreach ($elements as $element) {
			foreach ($element as $key => $value) {
				$this->filters[$key] = $value;
			}
		}
	}
}
