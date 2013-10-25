<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Param_File extends Config_File {
	function __construct($path) {
		parent::__construct($path, "param");
	}

	function update($values) {
		$return = true;
		return $return && parent::update($values);
	}
	
	
	function find_default_value($var = "") {
		if (!$this->is_readable()) {
			return false;
		} else {
			foreach (file($this->path) as $line) {
				if (preg_match('|^\\$([^[]+)\\[\'([^\']+)\'\\]\s*=\s*"([^"]*)";.*$|u', $line, $parameters)) {
					if ($parameters[1] == $this->type and $parameters[2] == $var) {
						return $parameters[3]; 
					}
				}
			}

			return false;
		}
	}
	
	function change_config_value($value = "", Param_file $file_fallback = null) {
		if ($this->exists()) {
			$default_value = $this->find_default_value($value);
		}
		
		if (!isset($default_value) or !$default_value) {
			if ($file_fallback->exists()) {
				$default_value = $file_fallback->find_default_value($value);
			}
		}
		$bot = new Lozeil_Bot();
		if (!isset($default_value) or !$default_value) {
			echo $value." : ".__('No default value').$default_value."\n";
			$final_value = $this->input('');
			return $final_value;
		} else {
			echo $value." : ".__('Default value :').$default_value."\n".__('Change ? (y/n)');
			while(empty($answer)) {
				$answer = $this->input('');
			};
			if ($answer == "y") {
				while(empty($answer_yes)) {
					$answer_yes = $this->input('');
				};
			} else {
				$answer_yes = $default_value;
			}
			return $answer_yes;
		}			
	}
	
	function overwrite(Param_file $dist_config_file = null) {
		if ($this->exists()) {
			echo utf8_ucfirst(__('config file already exists, do you want to overwrite? (y/n)'))."\n";
			while(empty($config_answer)) {
				$config_answer = $this->input('');
			};
		} else {
			$config_answer = "y";
		}
		
		if ($config_answer == "y") {
			if (!$dist_config_file->exists()) {
				die("Configuration file '".$dist_config_file."' does not exist");
			} else {
				try {
					$this->copy($dist_config_file);
				} catch (exception $exception) {
					die($exception->getMessage());
				}
			}
		}
	}
	
	private function input($message) {
	  fwrite(STDOUT, "$message: ");
	  $input = trim(fgets(STDIN));
	  return $input;
	}
}