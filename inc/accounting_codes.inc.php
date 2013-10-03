<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Accounting_Codes extends Collector  {
	public $filters = null;
	public $fullname = "";
	
	function __construct($class = null, $table = null, $db = null) {
		if ($class === null) {
			$class = substr(__CLASS__, 0, -1);
		}
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_accountingcodes'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
	
	function get_where() {
		$where = parent::get_where();
		
		if (isset($this->id) and !empty($this->id)) {
			if (!is_array($this->id)) {
				$this->id = array((int)$this->id);
			}
			$where[] = $this->db->config['table_accountingcodes'].".id IN ".array_2_list($this->id);
		}
		if (isset($this->fullname) and !empty($this->fullname)) {
			if(is_numeric($this->fullname)) {
				$where[] = "(".$this->db->config['table_accountingcodes'].".number LIKE ".$this->db->quote($this->fullname."%").")";
			} else {
				$where[] = "(".$this->db->config['table_accountingcodes'].".number LIKE ".$this->db->quote($this->fullname."%").
				" OR ".$this->db->config['table_accountingcodes'].".name LIKE ".$this->db->quote("%".$this->fullname."%").
				" OR SOUNDEX(".$this->db->config['table_accountingcodes'].".name) LIKE SOUNDEX(".$this->db->quote($this->fullname)."))";
			}
		}
		
		return $where;
	}
	
	function fullnames() {
		$numbers = array();
		foreach ($this as $code) {
			$numbers[$code->id] = $code->number." - ".$code->name;
		}
		return $numbers;
	}
	
	function numbers() {
		$numbers = array();
		foreach ($this as $code) {
			$numbers[$code->id] = $code->number;
		}
		return $numbers;
	}
	
	function grid_body() {
		$numbers = $this->numbers();
		$grid = array();
		foreach ($this as $accountingcode) {
			$number = $accountingcode->number;
			$matches = preg_grep ('/^'.$number.'/', $numbers);
			if (count($matches) > 1) {
				$class = substr($number, 0, -1)." accounting_codes_shift_".(strlen($number) - 1)." accounting_codes_parent";
			} else {
				$class = substr($number, 0, -1)." accounting_codes_shift_".(strlen($number) - 1);
			}
			$grid[$number] =  array(
				'class' => $class,
				'id' => $number,
				'cells' => array(
					array(
						'type' => "td",
						'value' => "<span class=\"accounting_codes_numbers\">".$number.".</span><span class=\"accounting_codes_names\">".$accountingcode->name."</span>",
					)
				)
			);
		}
		ksort($grid, SORT_STRING);
		return $grid;
	}
	
	function show() {
		$html_table = new Html_table(array('lines' => $this->grid_body()));
		return $html_table->show();
	}
	
	function display() {
		return "<div id=\"accounting_codes\">".
				$this->show()."</div>";
	}
}
