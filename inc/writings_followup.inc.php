<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Followup  {
	public $amounts = array();
	public $scale = 'daily';
	public $month = "";
	
	function show_timeseries_at($timestamp) {
		$charts = "";
		
		$writings = new Writings();
		$writings->select_columns('amount_inc_vat', 'day', 'categories_id');
		$writings->filter_with(array('start' => determine_first_day_of_year($timestamp), 'stop' => determine_last_day_of_year($timestamp) ));
		$writings->select();
		
		$cubismchart = new Html_Cubismchart("followupwritings");
		$cubismchart->start = determine_first_day_of_month($timestamp);
		if ($this->scale == 'daily') {
			$cubismchart->data_per_category = $writings->get_balance_per_day_per_category($timestamp);
		}
		if ($this->scale == 'monthly') {
			$cubismchart->data_per_category = $writings->get_amount_monthly_per_category($timestamp);
		}
		if ($this->scale == 'weekly') {
			$cubismchart->data_per_category = $writings->get_amount_weekly_per_category($timestamp);
		}
		if (!empty($cubismchart->data_per_category)) {
			foreach ($cubismchart->data_per_category as $categories_id => $values) {
				$cubismchart->data = $values;
				$category = new Category();
				$cubismchart->title = $category->load($categories_id) ? $category->name : __('&#60none&#62');
				$charts .= $cubismchart->display();
			}
		}
		return "<div class=\"timeseries\">".$this->form_scale_timeseries().$charts.$cubismchart->prepare_navigation()."</div>";
	}
	
	function form_scale_timeseries() {
		$form = "<form method=\"post\" name=\"scale_timeseries_form\" action=\"\" enctype=\"multipart/form-data\">";
		
		$input_hidden = new Html_Input("action", "scale_timeseries");
		$form .= $input_hidden->input_hidden();
		
		$select = new Html_Select("scale_timeseries_select", array(
			'daily' => __('daily cumulated'),
			'monthly' => __('monthly'),
			'weekly' => __('weekly')
			),
			$this->scale
		);
		$submit = new Html_Input("submit_timeseries_select", __('ok'), "submit");
		
		$form .= $select->item(utf8_ucfirst(__('scale'))).$submit->input()."</form>";
		return $form;
	}
}
