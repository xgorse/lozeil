<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Application {
	function boot() {
		ob_start();
		Plugins::call_hook("boot", array());
	}
	
	function mount() {
		Plugins::call_hook("mount", array());
	}
	
	function load() {
		Plugins::call_hook("load", array());
	}
	
	function shutdown() {
		Plugins::call_hook("shutdown", array());
	}
}
