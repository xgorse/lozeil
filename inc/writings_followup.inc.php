<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Followup  {
	public $amounts = array();
	public $month = "";
	
	function show_timeseries_at($timestamp) {
		$charts = "";
		
		$writings = new Writings();
		$writings->select_columns('amount_inc_vat', 'day', 'categories_id');
		$writings->filter_with(array('start' => determine_first_day_of_year($timestamp), 'stop' => determine_last_day_of_year($timestamp) ));
		$writings->select();
		
		$cubismchart = new Html_Cubismchart("followupwritings");
		$cubismchart->start = determine_first_day_of_month($timestamp);
		$cubismchart->data_per_category = $writings->get_balance_per_category($timestamp);
		if (!empty($cubismchart->data_per_category)) {
			foreach ($cubismchart->data_per_category as $categories_id => $values) {
				$cubismchart->data = $values;
				$category = new Category();
				$cubismchart->title = $category->load($categories_id) ? $category->name : __('&#60none&#62');
				$charts .= $cubismchart->display();
			}
		}
		return "<div class=\"timeseries\">".$charts.$cubismchart->prepare_navigation()."</div>";
	}
}
