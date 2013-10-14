<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Followup  {
	public $amounts = array();
	public $month = "";
	
	function show_timeseries_at($timestamp) {
		$charts = "";
		$categories = new Categories();
		$categories->select();
		$writings = new Writings();
		$cubismchart = new Html_Cubismchart("followupwritings");
		$writings->month = determine_first_day_of_month($timestamp);
		$cubismchart->start = $writings->month;
		$writings->select_columns('amount_inc_vat', 'day', 'categories_id');
		$writings->filter_with(array('start' => determine_first_day_of_year($timestamp), 'stop' => determine_last_day_of_year($timestamp) ));
		$writings->select();
		if ($writings->count() > 0) {
			foreach ($categories as $category) {
				$writings->categories_id = $category->id;
				$cubismchart->data = $writings->balance_per_day_in_a_year_in_array(mktime(0, 0, 0, 1, 1, date('Y',$writings->month)));
				$cubismchart->title = $category->name;
				$charts .= $cubismchart->display();
			}
				$writings->categories_id = 0;
				$cubismchart->data = $writings->balance_per_day_in_a_year_in_array(mktime(0, 0, 0, 1, 1, date('Y',$writings->month)));
				$cubismchart->title = __('none');
				$charts .= $cubismchart->display();
		}
		return "<div class=\"timeseries\">".$charts.$cubismchart->prepare_navigation()."</div>";
	}
}
