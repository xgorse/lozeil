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
			$this->db->config['table_writings'].".day as ".__('date').", ".
			$this->db->config['table_writings'].".number as '".__('piece number')."', ".
			$this->db->config['table_writings'].".amount_excl_vat as ".__('amount excluding vat').", ".
			$this->db->config['table_writings'].".vat as ".__('VAT').", ".
			$this->db->config['table_writings'].".amount_inc_vat as ".__('amount including vat').", ".
			$this->db->config['table_banks'].".name as ".__('bank').", ".
			$this->db->config['table_categories'].".name as ".__('category').", ".
			$this->db->config['table_sources'].".name as ".__('source').", ".
			$this->db->config['table_accountingcodes'].".number as '".__('accounting code')."', ".
			$this->db->config['table_writings'].".comment as ".__('comment').", ".
			$this->db->config['table_writings'].".information as ".__('information')." ".
			"FROM ".$this->db->config['table_writings'].
			" LEFT JOIN ".$this->db->config['table_banks'].
			" ON ".$this->db->config['table_banks'].".id = ".$this->db->config['table_writings'].".banks_id".
			" LEFT JOIN ".$this->db->config['table_categories'].
			" ON ".$this->db->config['table_categories'].".id = ".$this->db->config['table_writings'].".categories_id".
			" LEFT JOIN ".$this->db->config['table_sources'].
			" ON ".$this->db->config['table_sources'].".id = ".$this->db->config['table_writings'].".sources_id".
			" LEFT JOIN ".$this->db->config['table_accountingcodes'].
			" ON ".$this->db->config['table_accountingcodes'].".id = ".$this->db->config['table_writings'].".accountingcodes_id".
			" WHERE (1=1)".
			$querywhere.
			" ORDER BY date ASC"
		);
		if ($result_export[1] > 0) {
			while ($row_export = $this->db->fetchArray($result_export[0])) {
				if (!isset($title)) {
					$title = array_keys($row_export);
				}
				$value[] = $row_export;
			}
			export_excel($title, $value);
		}
	}
}
