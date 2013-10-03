<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Accounting_Codes extends Collector  {
	public $filters = null;
	
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
	
	function numbers() {
		$numbers = array();
		foreach ($this as $code) {
			$numbers[$code->id] = $code->number();
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
