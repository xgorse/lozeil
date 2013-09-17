<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Html_Cubismchart {
	public $data = array();
	public $width = 1095;
	
	function prepare_data() {
		$data = "<ul class=\"cubism_data\">";
		foreach ($this->data as $value) {
			$data .="<li>".$value."</li>";
		}
		$data .= "</ul>";
		$data .= "<ul class=\"cubism_width\"><li>".$this->width."</li></ul>";
		return $data;
	}
	
	function show() {
		return "<div id=\"cubismtimeline\" style=\"width : ".$this->width."px\"></div>".$this->prepare_data();
	}
}
