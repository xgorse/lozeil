<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Followup  {
	public $amounts = array();
	public $month = "";
	
	function show_timeseries_at($timestamp) {
		$charts = "";
		$categories = new Categories();
		$categories->select();
		foreach ($categories as $category) {
			$writings = new Writings();
			$writings->month = determine_first_day_of_month($timestamp);
			$writings->select_columns('amount_inc_vat', 'day');
			$writings->filter_with(array("categories_id" => $category->id));
			$writings->select();
			$cubismchart = new Html_Cubismchart("followupwritings");
			if ($writings->count() > 0) {
				$cubismchart->start = $writings->month;
				$cubismchart->data = $writings->balance_per_day_in_a_year_in_array(mktime(0, 0, 0, 1, 0, date('Y',$writings->month)));
				$cubismchart->title = $category->name;
				$charts .= $cubismchart->display();
			}
		}
		$cubismchart->start = $writings->month;
		return "<div class=\"timeseries\">".$charts.$cubismchart->prepare_navigation()."</div>";
	}
	
	function show_balance_to($timestamp_max) {
		$amount = 0;
		foreach ($this->instances as $writing) {
			if($writing->day < $timestamp_max) {
				$amount += $writing->amount_inc_vat;
			}
		}
		return round($amount, 2);
	}
	
	function balance_per_day_in_a_year_in_array($timestamp_max) {
		if (is_leap(date('Y',$timestamp_max) + 1)) {
			$nb_day = 366;
		} else {
			$nb_day = 365;
		}
		$values = array();
		$previous = 0;
		for ($i = 0; $i <= $nb_day; $i++) {
			$timestamp_max = strtotime('+1 day', $timestamp_max);
			$values[] = $previous + $this->show_balance_to($timestamp_max);
			$values[] = $previous + $this->show_balance_to($timestamp_max + 8 * 3600);
			$values[] = $previous + $this->show_balance_to($timestamp_max + 16 * 3600);
		}
		return $values;
	}
}
