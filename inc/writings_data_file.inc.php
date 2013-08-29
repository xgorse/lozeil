<?php
/*
	lozeil
	$Author:  $
	$URL: $
	$Revision: $

	Copyright (C) No Parking 2013 - 2013
*/

class Writings_Data_File {
	public $file_name = "";
	public $bank_id = 0;
	public $csv_data = array();
	
	function __construct($file_name ="", $bank_id = null) {
		$this->file_name = $file_name;
		$this->bank_id = $bank_id;
		$this->csv_data = array();
	}
	function import_as_cic() {
		if ($file_opened = fopen($this->file_name , 'r') ) {
			$row = 0;

            while(($data = fgetcsv($file_opened, 1000, ';')) !== FALSE) {

                $this->csv_data[$row]['delay'] = trim($data[1]);
                $this->csv_data[$row]['debit'] = trim($data[2]);
                $this->csv_data[$row]['credit'] = trim($data[3]);
                $this->csv_data[$row]['comment'] = trim($data[4]);

                $row++;
            }
			fclose($file_opened);
			
			if ($this->is_cic($this->csv_data)) {
				unset($this->csv_data[0]);
				
				$writings = new Writings();
				$writings_key = $writings->get_unique_key_in_array();
				
				foreach ($this->csv_data as $line) {
					if ($this->is_line_cic($line)) {
						$writing = new Writing();
						$time = explode("/", $line['delay']);
						$writing->delay = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
						$writing->comment = $line['comment'];
						$writing->bank_id = $this->bank_id;
						if (!empty($line['debit'])) {
							$writing->amount_inc_vat = (float)str_replace(",", ".", $line['debit']);
							$writing->amount_excl_vat = (float)str_replace(",", ".", $line['debit']);
						} else {
							$writing->amount_inc_vat = (float)str_replace(",", ".", $line['credit']);
							$writing->amount_excl_vat = (float)str_replace(",", ".", $line['credit']);
						}
						$writing->unique_key = hash('md5', $writing->delay.$writing->comment.$writing->bank_id.$writing->amount_inc_vat);
						if (!in_array($writing->unique_key, $writings_key)) {
							$writing->save();
						}
					} 
				}
			}
		}
	}
	
	function import_as_coop() {
		if ($file_opened = fopen( $this->file_name , 'r') ) {
			$row = 0;

            while(($data = fgetcsv($file_opened, 1000, ';')) !== FALSE) {
				foreach ($data as $key => $value) {
					$this->csv_data[$row][$key] = trim($value);
				}
	              $row++;
            }
			fclose($file_opened);
			
			if ($this->is_coop($this->csv_data)) {
				$row_names = $this->csv_data[0];
				unset($this->csv_data[0]);
				
				$writings = new Writings();
				$writings->select();
				$writings_key = $writings->get_unique_key_in_array();
				
				foreach ($this->csv_data as $line) {
					
					if ($this->is_line_coop($line)) {
						$information = "";
						for ($i = 0; $i < count($line); $i++) {
							if (!empty($line[$i]) && $i != 0 && $i != 1 && $i != 3 && $i != 4) {
								$information .= $row_names[$i]." : ".$line[$i]."\n";
							}
						}
						
						$writing = new Writing();
						$time = explode("/", $line[0]);
						$writing->delay = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
						$writing->comment = $line[1];
						$writing->bank_id = $this->bank_id;
						if (!empty($information)) {
							$writing->information = utf8_encode($information);
						}
						if ($line[4] == "DEBIT") {
							$line[3] = "-".$line[3];
						}
						$writing->amount_inc_vat = (float)str_replace(",", ".", $line[3]);
						$writing->amount_excl_vat = (float)str_replace(",", ".", $line[3]);
						$writing->unique_key = hash('md5', $writing->delay.$writing->comment.$writing->bank_id.$writing->amount_inc_vat);
						if (!in_array($writing->unique_key, $writings_key)) {
							$writing->save();
						}
					}
				}
			}
		}
	}
	
	function form_import() {
		$banks = new Banks();
		$banks->select();
		$banks_name = $banks->names();
		$form = "<div class=\"import\"><form method=\"post\" name=\"import_writings\" id=\"import_writings\" action=\"".link_content("content=writingsimport.php")."\" enctype=\"multipart/form-data\">";
		$input_hidden_action = new Html_Input("action", "import");
		$input_file = new Html_Input("input_file", "", "file");
		$bank = new Html_Select("bank_id", $banks_name);
		$submit = new Html_Input("import_submit", "Ok", "submit");
		$form .= $input_hidden_action->input_hidden().$input_file->item(utf8_ucfirst(__("importer le journal de banque"))).$bank->item(__('bank')).$submit->input();
		$form .= "</form></div>";
		return $form;
	}
	
	function import() {
		$bank = new Bank();
		$bank->load($this->bank_id);
		if (preg_match("/cic/", $bank->name)) {
			$this->import_as_cic();
		} elseif (preg_match("/coop/", $bank->name)) {
			$this->import_as_coop();
		}
	}
	
	function is_cic($data) {
		switch (true) {
			case $data[0]['delay'] != "Date de valeur":
			case $data[0]['debit'] != "D�bit":
			case $data[0]['credit'] != "Cr�dit":
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
	
	function is_line_cic($line) {
		$time = explode("/", $line['delay']);
		
		switch (true) {
			case (!isset($time[1]) OR !isset($time[2])) :
			case !(empty($line['debit']) XOR empty($line['credit'])) :
				return false;
			default :
				return true;
		}
	}
	
	function is_line_coop($line) {
		$delay = str_replace("/", "", $line[0]);
		switch (true) {
		case strlen($delay) != 8 :
		case empty($line[3]) :
		case ($line[4] != "DEBIT" AND $line[4] != "CREDIT") :
			return false;
		default :
			return true;
		}
	}
}