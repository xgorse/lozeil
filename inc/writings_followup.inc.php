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
		foreach ($categories as $category) {
			$writings->month = determine_first_day_of_month($timestamp);
			$writings->select_columns('amount_inc_vat', 'day');
			$writings->filter_with(array("categories_id" => $category->id));
			$writings->select();
			if ($writings->count() > 0) {
				$cubismchart->start = $writings->month;
				$cubismchart->data = $writings->balance_per_day_in_a_year_in_array(mktime(0, 0, 0, 1, 1, date('Y',$writings->month)));
				$cubismchart->title = $category->name;
				$charts .= $cubismchart->display();
			}
		}
		$cubismchart->start = $writings->month;
		return "<div class=\"timeseries\">".$charts.$cubismchart->prepare_navigation()."</div>";
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
		$nb_day = is_leap(date('Y',$timestamp_max) + 1) ? 366 : 365;
		$values = array();
		for ($i = 0; $i <= $nb_day; $i++) {
			$timestamp_max = strtotime('+1 day', $timestamp_max);
			$value = $this->show_balance_at($timestamp_max);
			$values[] = $value;
			$values[] = $value;
			$values[] = $value;
		}
		return $values;
	}
}
