<?php
/*
	opentime
	$Author: jeff $
	$URL: svn://svn.noparking.net/var/repos/opentime/inc/html_input_ajax.inc.php $
	$Revision: 5533 $

	Copyright (C) No Parking 2011 - 2011
*/

class Html_Input_Ajax extends Html_Input {
	public $id;
	public $url = "";
	public $element = array();
	public $properties = array();

	function __construct($id, $url, $element = array()) {
		$this->id = $id;
		$this->url = $url;
		$this->element = $element;
	}

	function input() {
		$search = new Html_Input(md5($this->id), "");
		$classes = "input-ajax";
		if (isset($this->properties['class'])) {
			$classes .= " ".$this->properties['class'];
		}
		$search->properties = array(
			'class' => $classes,
			'autocomplete' => "off",
			'data-url' => $this->url,
			'data-name' => $this->id,
			'data-format' => isset($this->properties['format']) ? $this->properties['format'] : "name",
		);
		if (count($this->element) == 1) {
			$search->properties['class'] .= " mask";
		}
		if (isset($this->properties['size'])) {
			$search->size = $this->size;
		}
		
		$html = "<div class=\"input-ajax-content\">";
		$html .= $search->input();
		$html .= "<div class=\"input-ajax-dynamic\" id=\"".md5($this->id)."-dynamic\"></div>";
		$html .= "<div class=\"input-ajax-static\" id=\"".md5($this->id)."-static\">";
		if (count($this->element) == 1) {
			foreach ($this->element as $id => $value) {
				$element = new Html_Checkbox($this->id, $id, true);
				$element->properties = array(
					'class' => "input-ajax-checkbox",
				);
				$html .= "<div>".$element->input().$value."</div>";
			}
		}
		$html .= "</div>";
		$html .= "</div>";
		
		return $html;
	}
}
