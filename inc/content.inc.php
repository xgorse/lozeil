<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Content {
	const not_found = "404.php";

	private $filename = "";
	public $user = null;
	public $context;

	function __construct($content="") {
		if (empty($content)) {
			$this->filename_login();
		} else {
			$this->filename = $this->clean($content);
		}
	}

	function filename_login() {
		if (isset($GLOBALS['param']['user_logincontent']) and !empty($GLOBALS['param']['user_logincontent'])) {
			$content = $GLOBALS['param']['user_logincontent'];
		} else {
			$content = "login.php";
		}
		$this->filename = $this->clean($content);
		
		return $this->filename;
	}

	function filename($content="") {
		if (!empty($content)) {
			$this->filename = $this->clean($content);
		}

		return $this->filename;
	}

	function pathname() {
		if (strpos($this->filename, "_") !== false) {
			return $this->pathname_from_plugin();
		} else {
			$context = "";
			$contents_directory = realpath(dirname(__FILE__)."/../contents");

			if (!empty($this->context)) {
				$context = $this->context."/";
			}
			if (file_exists($contents_directory."/".$context.$this->filename)) {
				return $contents_directory."/".$context.$this->filename;
			} else {
				return $contents_directory."/".$context.self::not_found;
			}
		}
	}

	function pathname_from_plugin() {
		list($plugin, $file) = explode("_", $this->filename);
		$plugin_contents_directory = realpath(directory_for_plugins().$plugin."/contents");
		switch (true) {
			case file_exists($plugin_contents_directory."/".$this->filename):
				return $plugin_contents_directory."/".$this->filename;
			case file_exists(dirname(__FILE__)."/../".$this->filename):
				return dirname(__FILE__)."/../".$this->filename;
			default:
				return dirname(__FILE__)."/../contents/".self::not_found;
		}
	}

	function user(User_Authenticated $user) {
		$this->user = $user;
		return $this;
	}

	private function clean($content) {
		if (empty($content)) {
			$user = new User($_SESSION['userid']);
			$content = $user->defaultpage();
		}

		if (!preg_match("/^[\._a-zA-Z0-9]*\.php$/", $content)) {
			$content = self::not_found;
		}

		return $content;
	}
}
