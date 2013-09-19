<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Html_Cubismchart {
	public $data = array();
	public $width = 1095;
	public $height = 55;
	public $start = 0;
	
	function prepare_data() {
		$data = "<ul>";
		foreach ($this->data as $value) {
			$data .="<li class=\"cubism_data\">".$value."</li>";
		}
		$data .= "</ul>";
		$data .= "<ul class=\"cubism_option\">
					<li id=\"cubism_width\">".$this->width."</li>
					<li id=\"cubism_height\">".$this->height."</li>
					<li id=\"cubism_start_year\">".date('Y', $this->start)."</li>
					<li id=\"cubism_isleap_year\">".is_leap(date('Y',$this->start))."</li>";
		$start = mktime(0, 0, 0, 1, 1, date ('Y',$this->start));
		for ($i = 0; $i <12; $i++) {
			$data .= "<li class=\"cubism_link\">".link_content("content=writings.php&amp;timestamp=").$start."</li>";
			$start = strtotime("+1 month", $start);
		}
			$data .= "</ul>";
		return $data;
	}
	
	function prepare_navigation() {
		$previous_year = mktime(0, 0, 0, 1, 1, date ('Y',$this->start) - 1);
		$next_year = mktime(0, 0, 0, 1, 1, date ('Y',$this->start) + 1);
		return "<span id=\"cubismtimeline_back\">".Html_Tag::a(link_content("content=writings.php&amp;timestamp=".$previous_year),"<<")."</span>
			<span id=\"cubismtimeline_next\">".Html_Tag::a(link_content("content=writings.php&amp;timestamp=".$next_year),">>")."</span>";
	}
	
	function show() {
		return "<div id=\"cubismtimeline\" style=\"width : ".$this->width."px\"></div>"
				.$this->prepare_data().$this->prepare_navigation();
	}
}
