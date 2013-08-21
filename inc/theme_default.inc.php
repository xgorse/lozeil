<?php
/*
	lozeil
	$Author: adrien $
	$URL:  $
	$Revision:  $

	Copyright (C) No Parking 2013 - 2013
*/

class Theme_Default {
	function html_top() {
		return "<!DOCTYPE HTML>
			<html>";
	}
	
	function head() {
		return "<head>
			<title>".($GLOBALS['config']['title'] == '' ? '' : $GLOBALS['config']['title']." : ").$GLOBALS['config']['name']."</title>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />".
			$this->css_files().
			$this->js_files()."
		</head>";
	}
	
	function body_top($location) {
		return "<body class=\"".Format::body_class($location)."\">";
	}
	
	function css_files() {
		$css_files[] = "medias/css/styles.css";
		
		return show_css_files($css_files);
	}
	
	function js_files() {
		$js_files[] = "medias/js/jquery-1.9.1.js";
		$js_files[] = "medias/js/jquery-drag_drop.js";
		$js_files[] = "medias/js/drag_drop.js";
		
		return show_js_files($js_files);
	}
	
	function content_top() {
		return "<div class=\"content\">";
	}

	function content_bottom() {
		return "</div>";
	}
	
	function body_bottom() {
		return "</body>";
	}
	
	function html_bottom() {
		return "</html>";
	}
}
