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
	
	function change_param_value($value = "", Param_file $file_fallback = null) {
		if ($this->exists()) {
			$default_value = $this->find_default_value($value);
		}
		
		if (!isset($default_value) or !$default_value) {
			if ($file_fallback->exists()) {
				$default_value = $file_fallback->find_default_value($value);
			}
		}
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
	
	function overwrite_file(Param_file $dist_config_file = null) {
		if ($this->exists()) {
			echo utf8_ucfirst(__('param file already exists, do you want to overwrite? (y/n)'))."\n";
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
					return true;
				} catch (exception $exception) {
					die($exception->getMessage());
				}
			}
		}
		return false;
	}
	
	private function input($message) {
	  fwrite(STDOUT, "$message: ");
	  $input = trim(fgets(STDIN));
	  return $input;
	}
}