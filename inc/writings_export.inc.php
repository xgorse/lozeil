<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writings_Export  {
	public $from;
	public $to;
	
	function __construct(db $db = null) {
		if ($db === null) {
			$db = new db();
		}
		$this->db = $db;
	}
	
	function clean_and_set($post) {
		if (is_datepicker_valid($post['date_picker_from'])) {
			$this->from = timestamp_from_datepicker($post['date_picker_from']);
		}
		if (is_datepicker_valid($post['date_picker_to'])) {
			$this->to = timestamp_from_datepicker($post['date_picker_to']);
		}
	}
	
	function export() {
		$querywhere = "";

		if (isset($this->from)) {
			$querywhere .= " AND ".$this->db->config['table_writings'].".day >= ".$this->from;
		}
		if (isset($this->to)) {
			$querywhere .= " AND ".$this->db->config['table_writings'].".day <= ".$this->to;
		}
		$result_export = $this->db->query("SELECT ".
			$this->db->config['table_writings'].".day as '0', ".
			$this->db->config['table_accountingcodes'].".number as '2', ".
			$this->db->config['table_writings'].".number as '3', ".
			$this->db->config['table_writings'].".comment as '4', ".
			$this->db->config['table_writings'].".information, ".
			$this->db->config['table_writings'].".amount_inc_vat as '5' 
			 FROM ".$this->db->config['table_writings'].
			" LEFT JOIN ".$this->db->config['table_accountingcodes'].
			" ON ".$this->db->config['table_accountingcodes'].".id = ".$this->db->config['table_writings'].".accountingcodes_id".
			" WHERE (1=1)".
			$querywhere.
			" ORDER BY day ASC"
		);
		if ($result_export[1] > 0) {
			while ($row_export = $this->db->fetchArray($result_export[0])) {
				$value[] = $row_export;
			}
			
			for ($i = 0; $i < count($value); $i++) {
				$value[$i][0] = date("d/m/Y", $value[$i][0]);
				
				$value[$i][1] = "BQC";
				
				$value[$i][4] .= " ".$value[$i]["information"];
				unset($value[$i]["information"]);
				
				if ($value[$i][5] >= 0) {
					$value[$i][5] = (float)$value[$i][5];
					$value[$i][6] = 0;
				} else {
					$value[$i][6] = -(float)$value[$i][5];
					$value[$i][5] = 0;
				}
				
				$value[$i][7] = "E";
				
				ksort($value[$i]);
			}
			export_excel("", $value);
		}
	}
}
