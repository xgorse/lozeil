<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Categories extends Collector  {
	public $filters = null;
	
	function __construct($class = null, $table = null, $db = null) {
		$class = "Category";
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_categories'];
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
			$where[] = $this->db->config['table_categories'].".id IN ".array_2_list($this->id);
		}
		if (isset($this->filters['vat_category'])) {
			$where[] = $this->db->config['table_categories'].".vat_category = ".(int)$this->filters['vat_category'];
		}
		
		return $where;
	}
	
	function filter_with() {
		$elements = func_get_args();
		foreach ($elements as $element) {
			foreach ($element as $key => $value) {
				$this->filters[$key] = $value;
			}
		}
	}
	
	function names() {
		$names = array();
		$names[0] = "--";
		foreach ($this as $category) {
			$names[$category->id] = $category->name;
		}
		return $names;
	}
	
	function grid_header() {
		$grid = array(
			'header' => array(
				'cells' => array(
					array(
						'type' => "th",
						'value' => __('name')
					),
					array(
						'type' => "th",
						'value' => __('default VAT')
					),
					array(
						'type' => "th",
						'value' => __('VAT category')
					),
				)
			)
		);
		return $grid;
	}
		
	
	function grid_body() {
		$input = new Html_Input("name_new");
		$input_vat = new Html_Input("vat_new");
		$checkbox_category_vat = new Html_Checkbox("vat_category", 1);
		$grid[0] =  array(
			'id' => 0,
			'cells' => array(
				array(
					'type' => "td",
					'value' => $input->item(""),
				),
				array(
					'type' => "td",
					'value' => $input_vat->item(""),
				),
				array(
					'type' => "td",
					'value' => $checkbox_category_vat->item(""),
				),
			)
		);
		
		foreach ($this as $category) {
			$input = new Html_Input("category[".$category->id."][name]", $category->name);
			$input_vat = new Html_Input("category[".$category->id."][vat]", $category->vat);
			$checkbox_category_vat = new Html_Checkbox("category[".$category->id."][vat_category]", 1, $category->vat_category);
			$grid[$category->id] =  array(
				'cells' => array(
					array(
						'type' => "td",
						'value' => $input->item(""),
					),
					array(
						'type' => "td",
						'value' => $input_vat->item(""),
					),
					array(
						'type' => "td",
						'value' => $checkbox_category_vat->item(""),
					),
				)
			);
		}
		
		$submit = new Html_Input("submit", __('save'), "submit");
		$grid[] =  array(
			'cells' => array(
				array(
					'type' => "td",
					'colspan' => "2",
					'value' => $submit->item(""),
				),
			)
		);
		return $grid;
	}
	
	function grid() {
		return $this->grid_header() + $this->grid_body();
	}
	
	function show() {
		$html_table = new Html_table(array('lines' => $this->grid()));
		return $html_table->show();
	}
	
	function show_form() {
		return "<div id=\"edit_categories\"><form method=\"post\" name=\"categories_id\" action=\"\" enctype=\"multipart/form-data\">".
			$this->show()."</form></div>";
	}
}
