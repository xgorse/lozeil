<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Data_File {
	public $file_name = "";
	public $tmp_name = "";
	public $type = "";
	public $banks_id = 0;
	public $csv_data = array();
	public $unique_keys = array();
	
	function __construct($tmp_name ="", $file_name = "", $type = "") {
		$this->tmp_name = $tmp_name;
		$this->file_name = $file_name;
		$this->type = $type;
		$this->csv_data = array();
	}
	
	function import() {
		if ($this->is_csv()) {
			$this->prepare_csv_data();
			if ($this->is_cic($this->csv_data)) {
				$this->import_as_cic();
			} elseif ($this->is_coop($this->csv_data)) {
				$this->import_as_coop();
			} else {
				log_status(__(('file %s is not in supported format'),  $this->file_name));
			}
		} elseif ($this->is_ofx()) {
			$this->import_as_ofx();
		}
	}
	
	function prepare_csv_data() {
		if ($file_opened = fopen( $this->tmp_name , 'r') ) {
			$row = 0;

            while(($data = fgetcsv($file_opened, 1000, ';')) !== FALSE) {
				foreach ($data as $key => $value) {
					$this->csv_data[$row][$key] = trim($value);
				}
	              $row++;
            }
			fclose($file_opened);
		} else {
			log_status(__('can not open file')." : ".$this->file_name);
		}
	}
	
	function is_cic($data) {
		switch (true) {
			case $data[0][1] != "Date de valeur":
			case $data[0][5] != "Solde":
				return false;
			default :
				return true;
		}
	}
	
	function is_coop($data) {
		switch (true) {
			case $data[0][0] != "Date" :
			case $data[0][3] != "Montant" :
			case $data[0][4] != "Sens" :
				return false;
			default :
				return true;
		}
	}
	
	function import_as_cic() {
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$writings_imported = new Writings_Imported();
		$writings_imported->filter_with(array("banks_id" => $this->banks_id));
		$writings_imported->select();
		foreach ($writings_imported as $writing_imported) {
			$this->unique_keys[] = $writing_imported->hash;
		}
		
		$nb_records = 0;
		unset($this->csv_data[0]);
		foreach ($this->csv_data as $line) {
			if ($this->is_line_cic($line)) {
				$writing = new Writing();
				$time = explode("/", $line[1]);
				$writing->day = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
				$writing->comment = $line[4];
				$writing->banks_id = $this->banks_id;
				if (!empty($line[2])) {
					$writing->amount_inc_vat = (float)str_replace(",", ".", $line[2]);
				} else {
					$writing->amount_inc_vat = (float)str_replace(",", ".", $line[3]);
				}
				$writing->paid = 1;
				$hash = hash('md5', $writing->day.$writing->comment.$writing->banks_id.$writing->amount_inc_vat);
				if (!in_array($hash, $this->unique_keys)) {
					$writing_imported = new Writing_Imported();
					$writing_imported->hash = $hash;
					$writing_imported->banks_id = $this->banks_id;
					$writing_imported->save();
					$writing->categories_id = $bayesianelements->categories_id_estimated($writing);
					$writing->save();
					$nb_records++;
				} else {
					//log_status(__('line %s of file %s already exists', array(implode(' - ', $line), $this->file_name)));
				}
			} else {
				log_status(__('line %s of file %s is not in cic format', array(implode(' - ', $line), $this->file_name)));
			}
		}
		log_status(__(('%s new records for %s'), array(strval($nb_records), $this->file_name)));
	}
	
	function import_as_coop() {
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$writings_imported = new Writings_Imported();
		$writings_imported->filter_with(array("banks_id" => $this->banks_id));
		$writings_imported->select();
		foreach ($writings_imported as $writing_imported) {
			$this->unique_keys[] = $writing_imported->hash;
		}
		
		$nb_records = 0;
		$row_names = $this->csv_data[0];
		unset($this->csv_data[0]);

		foreach ($this->csv_data as $line) {
			if ($this->is_line_coop($line)) {
				$information = "";
				for ($i = 0; $i < count($line); $i++) {
					if (!empty($line[$i]) and $i != 0 and $i != 1 and $i != 3 and $i != 4) {
						$information .= $row_names[$i]." : ".$line[$i]."\n";
					}
				}
				$writing = new Writing();
				$time = explode("/", $line[0]);
				$writing->day = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
				$writing->comment = $line[1];
				$writing->banks_id = $this->banks_id;
				if (!empty($information)) {
					$writing->information = utf8_encode($information);
				}
				if ($line[4] == "DEBIT") {
					$line[3] = "-".$line[3];
				}
				$writing->amount_inc_vat = (float)str_replace(",", ".", $line[3]);
				$writing->paid = 1;
				$hash = hash('md5', $writing->day.$writing->comment.$writing->banks_id.$writing->amount_inc_vat);
				
				if (!in_array($hash, $this->unique_keys)) {
					$writing_imported = new Writing_Imported();
					$writing_imported->hash = $hash;
					$writing_imported->banks_id = $this->banks_id;
					$writing_imported->save();
					$writing->categories_id = $bayesianelements->categories_id_estimated($writing);
					$writing->save();
					$nb_records++;
				} else {
					//log_status(__('line %s of file %s already exists', array(implode(' - ', $line), $this->file_name)));
				}
			} else {
				log_status(__('line %s of file %s is not in coop format', array(implode(' - ', $line), $this->file_name)));
			}
		}
		log_status(__(('%s new records for %s'), array(strval($nb_records), $this->file_name)));
	}
	
	function import_as_ofx() {
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->prepare();
		$writings_imported = new Writings_Imported();
		$writings_imported->filter_with(array("banks_id" => $this->banks_id));
		$writings_imported->select();
		foreach ($writings_imported as $writing_imported) {
			$this->unique_keys[] = $writing_imported->hash;
		}
		
		$nb_records = 0;
		$blocks = preg_split("/<STMTTRN>/", file_get_contents($this->tmp_name));
		
		foreach($blocks as $block) {
			$block = strstr($block, "</STMTTRN>", true);
			
			$amount_inc_vat = 0;
			$day = 0;
			$comment = "";
			$information = "";
			
			if ($block) {
				$lines = explode("\n", $block);
				foreach($lines as $line) {
					if (strstr($line, "<TRNAMT>") !== false) {
						$amount_inc_vat = (float)str_replace("<TRNAMT>", "", $line);
					}
					if (strstr($line, "<DTPOSTED>") !== false) {
						$day = (int)strtotime(str_replace("<DTPOSTED>", "", $line));
					}
					if (strstr($line, "<NAME>") !== false) {
						$comment = trim(preg_replace('/\t+/', '', str_replace("<NAME>", "", $line)));
					}
					if (strstr($line, "<MEMO>") !== false) {
						$information = trim(preg_replace('/\t+/', '', str_replace("<MEMO>", "", $line)));
					}
				}
				$writing = new Writing();
				$writing->amount_inc_vat = $amount_inc_vat;
				$writing->day = $day;
				$writing->comment = $comment;
				$writing->information = $information;
				$writing->banks_id = $this->banks_id;
				$writing->paid = 1;
				$hash = hash('md5', $writing->day.$writing->comment.$writing->banks_id.$writing->amount_inc_vat);

				if (!in_array($hash, $this->unique_keys)) {
					$writing_imported = new Writing_Imported();
					$writing_imported->hash = $hash;
					$writing_imported->banks_id = $this->banks_id;
					$writing_imported->save();
					$writing->categories_id = $bayesianelements->categories_id_estimated($writing);
					$writing->save();
					$nb_records++;
				} else {
					//log_status(__('line %s of file %s already exists', array(implode(' - ', $lines), $this->file_name)));
				}
			}
		}
		log_status(__(('%s new records for %s'), array(strval($nb_records), $this->file_name)));
	}
	
	function is_line_cic($line) {
		$time = explode("/", $line[1]);
		
		switch (true) {
			case (!isset($time[1]) OR !isset($time[2])) :
			case !(empty($line[2]) XOR empty($line[3])) :
				return false;
			default :
				return true;
		}
	}
	
	function is_line_coop($line) {
		$day = str_replace("/", "", $line[0]);
		
		switch (true) {
		case strlen($day) != 8 :
		case empty($line[3]) :
		case ($line[4] != "DEBIT" AND $line[4] != "CREDIT") :
			return false;
		default :
			return true;
		}
	}
	
	function form_import() {
		$banks = new Banks();
		$banks->select();
		$form = "<div id=\"menu_actions_import\"><form method=\"post\" name=\"menu_actions_import_form\" action=\"".link_content("content=writingsimport.php")."\" enctype=\"multipart/form-data\">";
		$import_file = new Html_Input("menu_actions_import_file", "", "file");
		$bank_select = new Html_Select("menu_actions_import_bank", $banks->names_of_selected_banks());
		$submit = new Html_Input("menu_actions_import_submit", "Ok", "submit");
		$form .= "<a id=\"menu_actions_import_label\" href=\"\">".utf8_ucfirst(__("import bank statement"))."</a>".$import_file->item("").$bank_select->item("").$submit->input();
		$form .= "</form></div>";
		return $form;
	}
	
	function form_export() {
		$date_picker_from = new Html_Input_Date('date_picker_from');
		$date_picker_to = new Html_Input_Date('date_picker_to');
		$date_picker_from->img_src = "medias/images/link_calendar_white.png";
		$date_picker_to->img_src = "medias/images/link_calendar_white.png";
		$form = "<div id=\"menu_actions_export\"><form method=\"post\" name=\"menu_actions_export_form\" action=\"".link_content("content=writingsexport.php")."\" enctype=\"multipart/form-data\">";
		$submit = new Html_Input("menu_actions_export_submit", "Ok", "submit");
		$form .= $date_picker_from->input().$date_picker_to->input().$submit->input();
		$form .= "</form></div>";
		return $form;
	}
	
	function is_csv() {
		if (pathinfo($this->file_name, PATHINFO_EXTENSION) == "csv") {
			return true;
		} else {
			return false;
		}
	}
	
	function is_ofx() {
		if (strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION)) == "ofx") {
			return true;
		} else {
			return false;
		}
	}
}
