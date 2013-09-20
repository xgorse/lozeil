<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Html_Cubismchart {
	public $name = "";
	public $data = array();
	public $width = 1095;
	public $height = 55;
	public $start = 0;
	public $title = '';
	
	function __construct($name = "") {
		$this->name = $name;
	}
	
	function prepare_data() {
		$data = "<ul class=\"cubism_data\">";
		$data .="<li class=\"cubism_data_title\">".$this->title."</li>";
		$data .="<li class=\"cubism_data_positive_average\">".$this->average_of_positive_values()."</li>";
		$data .="<li class=\"cubism_data_negative_average\">".$this->average_of_negative_values()."</li>";
		foreach ($this->data as $value) {
			$data .="<li class=\"cubism_data_row\">".$value."</li>";
		}
		$data .= "</ul>";
		$data .= "<ul class=\"cubism_option\">
					<li id=\"cubism_width\">".$this->width."</li>
					<li id=\"cubism_height\">".$this->height."</li>
					<li id=\"cubism_start_year\">".date('Y', $this->start)."</li>
					<li id=\"cubism_isleap_year\">".is_leap(date('Y',$this->start))."</li>";
		$start = mktime(0, 0, 0, 1, 1, date ('Y',$this->start));
		for ($i = 0; $i <12; $i++) {
			$data .= "<li class=\"cubism_link\">".link_content("content=".$this->name.".php&amp;timestamp=").$start."</li>";
			$start = strtotime("+1 month", $start);
		}
			$data .= "</ul>";
		return $data;
	}
	
	function prepare_navigation() {
		$previous_year = mktime(0, 0, 0, 1, 1, date ('Y',$this->start) - 1);
		$next_year = mktime(0, 0, 0, 1, 1, date ('Y',$this->start) + 1);
		return "<span id=\"cubismtimeline_back\">".Html_Tag::a(link_content("content=".$this->name.".php&amp;timestamp=".$previous_year),"<<")."</span>
			<span id=\"cubismtimeline_next\">".Html_Tag::a(link_content("content=".$this->name.".php&amp;timestamp=".$next_year),">>")."</span>";
	}
	
	function show() {
		return "<div id=\"cubismtimeline\" style=\"width : ".$this->width."px\"></div>"
				.$this->prepare_data().$this->prepare_navigation();
	}
	
	function display() {
		return "<div id=\"cubismtimeline\" style=\"width : ".$this->width."px\"></div>"
				.$this->prepare_data();
	}
	
	function average_of_positive_values() {
		$sum = 0;
		$nb = 0;
		foreach ($this->data as $value) {
			if ($value > 0) {
				$sum = $sum + $value;
				$nb++;
			}
		}
		if ($nb == 0) {
			return 0;
		}
		return $sum/$nb;
	}
	
	function average_of_negative_values() {
		$sum = 0;
		$nb = 0;
		foreach ($this->data as $value) {
			if ($value < 0) {
				$sum = $sum + $value;
				$nb++;
			}
		}
		if ($nb == 0) {
			return 0;
		}
		return $sum/$nb;
	}
}
