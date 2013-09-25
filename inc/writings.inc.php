<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings extends Collector {
	public $filters = null;
	public $amounts = array();
	
	function __construct($class = null, $table = null, $db = null) {
		if ($class === null) {
			$class = substr(__CLASS__, 0, -1);
		}
		if ($table === null) {
			$table = $GLOBALS['dbconfig']['table_writings'];
		}
		if ($db === null) {
			$db = new db();
		}
		parent::__construct($class, $table, $db);
	}
	
	function get_join() {
		$join = parent::get_join();
		$join[] = "
			LEFT JOIN ".$this->db->config['table_categories']."
			ON ".$this->db->config['table_categories'].".id = ".$this->db->config['table_writings'].".categories_id
		";
		$join[] = "
			LEFT JOIN ".$this->db->config['table_sources']."
			ON ".$this->db->config['table_sources'].".id = ".$this->db->config['table_writings'].".sources_id
		";
		$join[] = "
			LEFT JOIN ".$this->db->config['table_banks']."
			ON ".$this->db->config['table_banks'].".id = ".$this->db->config['table_writings'].".banks_id
		";
		
		return $join;
	}
	
	function get_columns() {
		$columns = parent::get_columns();
		$columns[] = $this->db->config['table_categories'].".name as category_name, ".$this->db->config['table_sources'].".name as source_name, ".$this->db->config['table_banks'].".name as bank_name";

		return $columns;
	}
	
	function get_where() {
		$query_where = parent::get_where();
		
		if (isset($this->filters['timestamp_start'])) {
			$query_where[] = $this->db->config['table_writings'].".timestamp >= ".(int)$this->filters['timestamp_start'];
		}
		if (isset($this->filters['timestamp_stop'])) {
			$query_where[] = $this->db->config['table_writings'].".timestamp <= ".(int)$this->filters['timestamp_stop'];
		}
		if (isset($this->filters['start'])) {
			$query_where[] = $this->db->config['table_writings'].".day >= ".(int)$this->filters['start'];
		}
		if (isset($this->filters['stop'])) {
			$query_where[] = $this->db->config['table_writings'].".day <= ".(int)$this->filters['stop'];
		}
		if (isset($this->filters['*']) and !empty($this->filters['*'])) {
			$query_where[] = $this->db->config['table_writings'].".search_index LIKE ".$this->db->quote("%".$this->filters['*']."%");
		}
		if (isset($this->filters['categories_id'])) {
			$query_where[] = $this->db->config['table_writings'].".categories_id = ".(int)$this->filters['categories_id'];
		}
		
		return $query_where;
	}
	
	function grid_header() {
		$checkbox = new Html_Checkbox("checkbox_all_up", "check");
		$grid = array(
			'header' => array(
				'class' => "table_header",
				'cells' => array(
					array(
						'type' => "th",
						'id' => "checkbox",
						'value' => $checkbox->input()
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("day"),
						'id' => "day",
						'value' => utf8_ucfirst(__("date")),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("number"),
						'id' => "number",
						'value' => utf8_ucfirst(__('piece nb')),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("category_name"),
						'id' => "category_name",
						'value' => utf8_ucfirst(__("category")),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("source_name"),
						'id' => "source_name",
						'value' => utf8_ucfirst(__("source")),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("bank_name"),
						'id' => "bank_name",
						'value' => utf8_ucfirst(__("bank")),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("comment"),
						'id' => "comment",
						'value' => utf8_ucfirst(__("comment")),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("amount_excl_vat"),
						'id' => "amount_excl_vat",
						'value' => utf8_ucfirst(__("amount excluding vat")." (".__("VAT").")"),
					),
					array(
						'type' => "th",
						'class' => $this->determine_table_header_class("amount_inc_vat"),
						'id' => "amount_inc_vat",
						'value' => utf8_ucfirst(__("debit")),
					),
					array(
						'type' => "th",
						'class' => "sort",
						'id' => "amount_inc_vat",
						'value' => utf8_ucfirst(__("credit")),
					),
					array(
						'type' => "th",
						'id' => "operations",
						'value' => utf8_ucfirst(__("operations")),
					),
				),
			),
		);		
		return $grid;
	}
	
	function determine_table_header_class($header_column_name) {
		$class = "sort";
		if ($_SESSION['order_col_name'] == $header_column_name) {
			if ($_SESSION['order_direction'] == "ASC") {
				$class .= " sortedup";
			} else {
				$class .= " sorteddown";
			}
		}
		return $class;
	}
	
	function grid_body() {
		$categories = new Categories();
		$categories->select();
		$categories_names = $categories->names();
		$sources = new Sources();
		$sources->select();
		$sources_name = $sources->names();
		$banks = new Banks();
		$banks->select();
		$banks_name = $banks->names();
		
		$grid = array();
		foreach ($this as $writing) {
			$informations = $writing->show_further_information();
			$checkbox = new Html_Checkbox("checkbox_".$writing->id, $writing->id);
			$checkbox->properties = array("class" => "table_checkbox");
			$grid[] =  array(
				'class' => $writing->is_recently_modified() ? "draggable modified" : "draggable",
				'id' => "table_".$writing->id,
				'cells' => array(
					array(
						'type' => "td",
						'value' => $checkbox->input(),
					),
					array(
						'type' => "td",
						'value' => date("d/m/Y", $writing->day),
					),
					array(
						'type' => "td",
						'value' => $writing->number,
					),
					array(
						'type' => "td",
						'value' => isset($categories_names[$writing->categories_id]) ? $categories_names[$writing->categories_id] : "",
					),
					array(
						'type' => "td",
						'value' => isset($sources_name[$writing->sources_id]) ? $sources_name[$writing->sources_id] : "",
					),
					array(
						'type' => "td",
						'value' => isset($banks_name[$writing->banks_id]) ? $banks_name[$writing->banks_id] : "",
					),
					array(
						'type' => "td",
						'class' => empty($informations) ? "" : "table_writings_comment",
						'value' => $writing->comment.$informations,
					),
					array(
						'type' => "td",
						'value' => round($writing->amount_excl_vat, 2).(($writing->vat != 0) ? "&nbsp;(".$writing->vat.")" : ""),
					),
					array(
						'type' => "td",
						'value' => $writing->amount_inc_vat < 0 ? round($writing->amount_inc_vat, 2) : "",
					),
					array(
						'type' => "td",
						'value' => $writing->amount_inc_vat >= 0 ? round($writing->amount_inc_vat, 2) : "",
					),
					array(
						'type' => "td",
						'class' => "operations",
						'value' => $writing->show_operations(),
					),
				),
			);
		}
		
		return $grid;
	}

	function grid() {
		return $this->grid_header() + $this->grid_body();
	}
	
	function show() {
		$html_table = new Html_table(array('lines' => $this->grid()));
		
		return $html_table->show();
	}
	
	function display() {
		return "<div id=\"table_writings\">".$this->show()."</div>";
	}
	
	function show_timeline_at($timestamp) {
		$writings = new Writings();
		$writings->month = determine_first_day_of_month($timestamp);
		$writings->select_columns('amount_inc_vat', 'day');
		$writings->select();
		
		$cubismchart = new Html_Cubismchart("writings");
		$cubismchart->data = $writings->balance_per_day_in_a_year_in_array(mktime(0, 0, 0, 1, 1, date('Y',$writings->month)));
		$cubismchart->start = $writings->month;
		
		return $cubismchart->show();
	}
	
	function display_timeline_at($timestamp) {
		return "<div id=\"heading_timeline\">".$this->show_timeline_at($timestamp)."</div>";
	}
	
	function display_balance_on_current_date() {
		list($start, $stop) = determine_month(time());
		
		return Html_Tag::a(link_content("content=writings.php&start=".$start."&stop=".$stop),utf8_ucfirst(__("accounting on"))." ".get_time("d/m/Y")." : ".$this->show_balance_at(time())." ".__("€"));
	}
	
	function show_balance_at($timestamp_max) {
		$amount = 0;
		foreach ($this->instances as $writing) {
			if($writing->day < $timestamp_max) {
				$amount += $writing->amount_inc_vat;
			}
		}
		
		return round($amount, 2);
	}
	
	function balance_per_day_in_a_year_in_array($timestamp_max) {
		$values[] = 0;
		$nb_day = is_leap(date('Y',$timestamp_max) + 1) ? 366 : 365;
		
		for ($i = 0; $i <= $nb_day; $i++) {
			$timestamp_max = strtotime('+1 day', $timestamp_max);
			$value = $this->show_balance_at($timestamp_max);
			$values[] = $value;
			$values[] = $value;
			$values[] = $value;
		}
		
		return $values;
	}
	
	function form_filter($start, $stop, $value = "") {
		$form = "<div class=\"extra_filter_writings\"><form method=\"post\" name=\"extra_filter_writings_form\" action=\"\" enctype=\"multipart/form-data\">";
		$input_hidden_action = new Html_Input("action", "filter");
		$date_start = new Html_Input_Date("filter_day_start", $start);
		$date_stop = new Html_Input_Date("filter_day_stop", $stop);
		$input = new Html_Input("extra_filter_writings_value",$value);
		$form .= $input_hidden_action->input_hidden().$input->item(utf8_ucfirst(__('filter')." : "))."<span id =\"extra_filter_writings_toggle\"> + </span><span class=\"extra_filter_writings_days\">".$date_start->input().$date_stop->input()."</span>";
		$form .= "</form></div>";
		
		return $form;
	}
	
	function filter_with() {
		$elements = func_get_args();
		foreach ($elements as  $element) {
			foreach ($element as $key => $value) {
				$this->filters[$key] = $value;
			}
		}
	}
	
	function form_cancel_last_operation() {
		$form = "<div class=\"extra_cancel_writings\"><form method=\"post\" name=\"extra_cancel_writings_form\" action=\"\" enctype=\"multipart/form-data\">";
		$input_hidden_action = new Html_Input("action", "cancel");
		$submit = new Html_Input("extra_cancel_writings_value",__('cancel operation'), "submit");
		$submit->properties = array(
				'onclick' => "javascript:return confirm('".utf8_ucfirst(__('are you sure that you want to cancel the operation? It will be irreversible'))."')"
			);
		$form .= $input_hidden_action->input_hidden().$submit->input();
		$form .= "</form></div>";
		
		return $form;
	}
	
	function cancel_last_operation() {
		$writings = new Writings();
		$max = $writings->db->Value("SELECT MAX(timestamp) FROM ".$this->db->config['table_writings']);
		$writings->filter_with(array('timestamp_start' => ($max - 1), 'timestamp_stop' => ($max)));
		$writings->select();
		foreach ($writings as $instance) {
			$instance->delete();
		}
	}
	
	function modify_options() {
		$options = array(
			"null" => "--",
			"category" => __('change category to')." ...",
			"source" => __('change source to')." ...",
			"amount_inc_vat" => __('change amount including vat to')." ...",
			"vat" => __('change vat to')." ...",
			"day" => __('change date to')." ...",
			"delete" => __('delete')
		);
		$select = new Html_Select("options_modify_writings", $options);
		$select->properties = array(
				'onchange' => "confirm_option('".utf8_ucfirst(__('are you sure?'))."')"
			);
		$checkbox = new Html_Checkbox("checkbox_all_down", "check");
		$form = "<div id=\"select_modify_writings\">".$checkbox->input().$select->item("")."<div id=\"form_modify_writings\"></div></div>";
		
		return $form;
	}
	
	function determine_show_form_modify($target) {
		$form = "<form method=\"post\" name=\"writings_modify_form\" action=\"\" enctype=\"multipart/form-data\" onsubmit=\"return confirm_modify('".utf8_ucfirst(__('are you sure?'))."')\">";
		$submit = new Html_Input("submit_writings_modify_form", "", "submit");
		switch($target) {
			case 'category':
				$categories = new Categories();
				$categories->select();
				$category = new Html_Select("categories_id", $categories->names());
				$category->properties = array(
					'onchange' => "confirm_modify('".utf8_ucfirst(__('are you sure?'))."')"
				);
				$form .= $category->item("");
				break;
			case 'source':
				$sources = new Sources();
				$sources->select();
				$source = new Html_Select("sources_id", $sources->names());
				$source->properties = array(
					'onchange' => "confirm_modify('".utf8_ucfirst(__('are you sure?'))."')"
				);
				$form .= $source->item("");
				break;
			case 'amount_inc_vat':
				$amount_inc_vat = new Html_Input("amount_inc_vat");
				$form .= $amount_inc_vat->input();
				$form .= $submit->input();
				break;
			case 'vat':
				$vat = new Html_Input("vat");
				$form .= $vat->input();
				$form .= $submit->input();
				break;
			case 'day':
				$datepicker = new Html_Input_Date("datepicker");
				$datepicker->properties = array(
					'onsubmit' => "confirm_modify('".utf8_ucfirst(__('are you sure?'))."')"
				);
				$form .= $datepicker->item("");
				$form .= $submit->input();
				break;
			default :
				break;
		}
		$form .= "</form>";
		
		return $form;
	}
	
	function modify_from_form($post) {
		switch ($post['modify']) {
			case 'category':
				$this->change_category_to_from_ids($post['categories_id'], json_decode($post['ids']));
				break;
			case 'source':
				$this->change_source_to_from_ids($post['sources_id'], json_decode($post['ids']));
				break;
			case 'vat':
				$this->change_vat_to_from_ids($post['vat'], json_decode($post['ids']));
				break;
			case 'amount_inc_vat':
				$this->change_amount_inc_vat_to_from_ids($post['amount_inc_vat'], json_decode($post['ids']));
				break;
			case 'day':
				$this->change_day_to_from_ids($post['datepicker'], json_decode($post['ids']));
				break;
			default :
				break;
		}
	}
	
	function delete_from_ids($ids) {
		foreach($ids as $id) {
			$writing = new Writing();
			$writing->id = $id;
			$writing->delete();
		}
	}
	
	function change_category_to_from_ids($category_id, $ids) {
		foreach($ids as $id) {
			$writing = new Writing();
			$writing->load($id);
			$writing->categories_id = $category_id;
			$writing->save();
		}
	}
	
	function change_source_to_from_ids($source_id, $ids) {
		foreach($ids as $id) {
			$writing = new Writing();
			$writing->load($id);
			$writing->sources_id = $source_id;
			$writing->save();
		}
	}
	
	function change_vat_to_from_ids($vat, $ids) {
		foreach($ids as $id) {
			$writing = new Writing();
			$writing->load($id);
			$writing->vat = $vat;
			$writing->save();
		}
	}
	
	function change_amount_inc_vat_to_from_ids($amount_inc_vat, $ids) {
		foreach($ids as $id) {
			$writing = new Writing();
			$writing->load($id);
			$writing->amount_inc_vat = $amount_inc_vat;
			$writing->save();
		}
	}
	
	function change_day_to_from_ids($time, $ids) {
		if(is_datepicker_valid($time)) {
			$day = mktime(0, 0, 0, $time['m'], $time['d'], $time['Y']);
			foreach($ids as $id) {
				$writing = new Writing();
				$writing->load($id);
				$writing->day = $day;
				$writing->save();
			}
		}
	}
}
