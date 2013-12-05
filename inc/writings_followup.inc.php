<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Followup  {
	public $amounts = array();
	public $scale = 'daily';
	public $month = "";
	public $filter = "categories";
	
	function show_timeseries_per_category_at($timestamp) {
		$charts = "";
		
		$writings = new Writings();
		$writings->select_columns('amount_inc_vat', 'day', 'categories_id');
		$writings->filter_with(array('start' => determine_first_day_of_year($timestamp), 'stop' => determine_last_day_of_year($timestamp) ));
		$writings->select();
		
		$cubismchart = new Html_Cubismchart("followupwritings");
		$cubismchart->start = determine_first_day_of_month($timestamp);
		if ($this->scale == 'daily') {
			$data_per_category = $writings->get_balance_per_day_per_category($timestamp);
		}
		if ($this->scale == 'monthly') {
			$data_per_category = $writings->get_amount_monthly_per_category($timestamp);
		}
		if ($this->scale == 'weekly') {
			$data_per_category = $writings->get_amount_weekly_per_category($timestamp);
		}
		if (!empty($data_per_category)) {
			foreach ($data_per_category as $categories_id => $values) {
				$cubismchart->data = $values;
				$category = new Category();
				$cubismchart->title = $category->load(array('id' => $categories_id)) ? $category->name : __('&#60none&#62');
				$charts .= $cubismchart->display();
			}
		}
		return "<div class=\"timeseries\">".$this->form_scale_timeseries().$charts.$cubismchart->prepare_navigation($this->filter, $this->scale)."</div>";
	}
	
	function show_timeseries_per_bank_at($timestamp) {
		$charts = "";
		
		$writings = new Writings();
		$writings->select_columns('amount_inc_vat', 'day', 'banks_id');
		$writings->filter_with(array('start' => determine_first_day_of_year($timestamp), 'stop' => determine_last_day_of_year($timestamp) ));
		$writings->select();
		
		$cubismchart = new Html_Cubismchart("followupwritings");
		$cubismchart->start = determine_first_day_of_month($timestamp);
		if ($this->scale == 'daily') {
			$data_per_bank = $writings->get_balance_per_day_per_bank($timestamp);
		}
		if ($this->scale == 'monthly') {
			$data_per_bank = $writings->get_amount_monthly_per_bank($timestamp);
		}
		if ($this->scale == 'weekly') {
			$data_per_bank = $writings->get_amount_weekly_per_bank($timestamp);
		}
		if (!empty($data_per_bank)) {
			foreach ($data_per_bank as $banks_id => $values) {
				$cubismchart->data = $values;
				$bank = new Bank();
				$cubismchart->title = $bank->load(array('id' => $banks_id)) ? $bank->name : __('&#60none&#62');
				$charts .= $cubismchart->display();
			}
		}
		return "<div class=\"timeseries\">".$this->form_scale_timeseries().$charts.$cubismchart->prepare_navigation($this->filter, $this->scale)."</div>";
	}
	
	function form_scale_timeseries() {
		$form = "<form method=\"post\" name=\"scale_timeseries_form\" action=\"\" enctype=\"multipart/form-data\">";
		
		$input_hidden = new Html_Input("action", "scale_timeseries");
		$form .= $input_hidden->input_hidden();
		
		$select_scale = new Html_Select("scale_timeseries_select", array(
			'daily' => __('daily cumulated'),
			'monthly' => __('monthly'),
			'weekly' => __('weekly')
			),
			$this->scale
		);
		
		$select_filter = new Html_Select("filter_timeseries_select", array(
			'categories' => __('categories'),
			'banks' => __('banks')
			),
			$this->filter
		);
		$submit = new Html_Input("submit_timeseries_select", __('ok'), "submit");
		
		$form .= $select_scale->item(utf8_ucfirst(__('scale'))).$select_filter->item(utf8_ucfirst(__('filter'))).$submit->item("")."</form>";
		return $form;
	}
}
