<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Writing extends Record {
	public $id = 0;
	public $accountingcodes_id = 0;
	public $amount_excl_vat = 0;
	public $amount_inc_vat = 0;
	public $banks_id = 0;
	public $categories_id = 0;
	public $comment = "";
	public $day = 0;
	public $information = "";
	public $number = "";
	public $paid = 0;
	public $search_index = "";
	public $sources_id = 0;
	public $timestamp = 0;
	public $vat = 0;
	
	function __construct($id = 0, db $db = null) {
		parent::__construct($db);
		$this->id = $id;
	}

	function db($db) {
		if ($db instanceof db) {
			$this->db = $db;
		}
	}
	
	function load($id = null) {
		if (($id === null or $id == 0) and ($this->id === null or $this->id == 0)) {
			return false;

		} else {
			if ($id === null) {
				$id = $this->id;
			}
			return parent::load($this->db->config['table_writings'], array('id' => (int)$id));
		}
	}
	
	function save() {
		if (is_numeric($this->id) and $this->id != 0) {
			$this->id = $this->update();

		} else {
			$this->id = $this->insert();
		}

		return $this->id;
	}

	function delete() {
		$result = $this->db->query("DELETE FROM ".$this->db->config['table_writings'].
			" WHERE id = '".$this->id."'"
		);
		$this->db->status($result[1], "u", __('writing'));

		return $this->id;
	}
	
	function update() {
		$result = $this->db->query("UPDATE ".$this->db->config['table_writings'].
			" SET categories_id = ".(int)$this->categories_id.",
			banks_id = ".(int)$this->banks_id.",
			sources_id = ".(int)$this->sources_id.",
			amount_inc_vat = ".(float)$this->amount_inc_vat.",
			number  = ".$this->db->quote($this->number).",
			vat = ".(float)$this->vat.",
			amount_excl_vat = ".$this->calculate_amount_excl_vat().",
			comment = ".$this->db->quote($this->comment).",
			information = ".$this->db->quote($this->information).",
			paid = ".(int)$this->paid.",
			day = ".(int)$this->day.",	
			search_index = ".$this->db->quote($this->search_index()).",
			accountingcodes_id = ".(int)$this->accountingcodes_id.",
			timestamp = ".time()."
			WHERE id = ".(int)$this->id
		);
		$this->db->status($result[1], "u", __('writing'));

		return $this->id;
	}
	
	function insert() {
		$result = $this->db->id("
			INSERT INTO ".$this->db->config['table_writings']."
			SET categories_id = ".(int)$this->categories_id.",
			banks_id = ".(int)$this->banks_id.",
			sources_id = ".(int)$this->sources_id.",
			amount_inc_vat = ".(float)$this->amount_inc_vat.",
			number  = ".$this->db->quote($this->number).",
			vat = ".(float)$this->vat.",
			amount_excl_vat = ".$this->calculate_amount_excl_vat().",
			comment = ".$this->db->quote($this->comment).",
			information = ".$this->db->quote($this->information).",
			day = ".(int)$this->day.",
			search_index = ".$this->db->quote($this->search_index()).",
			accountingcodes_id = ".(int)$this->accountingcodes_id.",
			paid = ".(int)$this->paid.",
			timestamp = ".time()
		);
		$this->id = $result[2];
		$this->db->status($result[1], "u", __('writing'));

		return $this->id;
	}
	
	function clean($post) {
		$cleaned = array();
		if (isset($post['datepicker'])) {
			$cleaned['day'] = timestamp_from_datepicker($post['datepicker']);
		}
		$cleaned['accountingcodes_id'] = (int)$post[md5('accountingcodes_id')];
		$cleaned['categories_id'] = (int)$post['categories_id'];
		$cleaned['sources_id'] = (int)$post['sources_id'];
		$cleaned['paid'] = (int)$post['paid'];
		$cleaned['comment'] = $post['comment'];
		$cleaned['amount_excl_vat'] = str_replace(",", ".", $post['amount_excl_vat']);
		$cleaned['amount_inc_vat'] = str_replace(",", ".", $post['amount_inc_vat']);
		$cleaned['vat'] = str_replace(",", ".", $post['vat']);
		return $cleaned;
	}
	
	function search_index() {
		$bank = new Bank();
		$bank->load($this->banks_id);
		$source = new Source();
		$source->load($this->sources_id);
		$category = new Category();
		$category->load($this->categories_id);
		
		return date("d/m/Y",$this->day)." ".$this->vat." ".$this->amount_excl_vat." ".$this->amount_inc_vat." ".$bank->name." ".$this->comment." ".$this->information." ".$this->number." ".$source->name." ".$category->name;
	}
	
	function calculate_amount_excl_vat() {
		if ($this->vat != -100) {
			return (float)round($this->amount_inc_vat/(($this->vat/100) + 1), 6);
		}
		return 0;
	}
	
	function merge_from(Writing $to_merge) {
		if ($this->banks_id == 0 or $to_merge->banks_id == 0) {
			if ($this->banks_id != 0) {
				$this->categories_id = $this->categories_id > 0 ? (int)$this->categories_id : $to_merge->categories_id;
				$this->banks_id = $this->banks_id > 0 ? (int)$this->banks_id : $to_merge->banks_id;
				$this->sources_id = $this->sources_id > 0 ? (int)$this->sources_id : $to_merge->sources_id;
				$this->vat = $to_merge->vat > 0 ? $to_merge->vat : $this->vat;
				$this->amount_excl_vat =  $this->calculate_amount_excl_vat();
				$this->comment = !empty($this->comment) ? $this->comment : $to_merge->comment;
				$this->information = !empty($this->information) ? $this->information : $to_merge->information;
				$this->number = !empty($this->number) ? $this->number : $to_merge->number;
				$this->accountingcodes_id = $this->accountingcodes_id > 0 ? (int)$this->accountingcodes_id : $to_merge->accountingcodes_id;
				$this->search_index = $this->search_index();
				$this->save();
				$to_merge->delete();
			} else {
				$this->categories_id = $to_merge->categories_id > 0 ? (int)$to_merge->categories_id : $this->categories_id;
				$this->banks_id = $to_merge->banks_id > 0 ? (int)$to_merge->banks_id : $this->banks_id;
				$this->sources_id = $to_merge->sources_id > 0 ? (int)$to_merge->sources_id : $this->sources_id;
				$this->vat = $to_merge->vat > 0 ? $to_merge->vat : $this->vat;
				$this->amount_inc_vat = $to_merge->amount_inc_vat;
				$this->amount_excl_vat = $this->calculate_amount_excl_vat();
				$this->comment = !empty($to_merge->comment) ? $to_merge->comment : $this->comment;
				$this->day = $to_merge->day;
				$this->information = !empty($to_merge->information) ? $to_merge->information : $this->information;
				$this->number = !empty($to_merge->number) ? $to_merge->number : $this->number;
				$this->accountingcodes_id = $to_merge->accountingcodes_id > 0 ? (int)$to_merge->accountingcodes_id : $this->accountingcodes_id;
				$this->paid = $to_merge->paid;
				$this->search_index = $this->search_index();
				$this->save();
				$to_merge->delete();
			}
		} else {
			return false;
		}
	}
	
	function split($amount = 0) {
		$this->amount_inc_vat = ($this->amount_inc_vat - $amount);
		$this->amount_excl_vat = $this->calculate_amount_excl_vat();
		$this->search_index = $this->search_index();
		$this->save();
		
		$writing = new Writing();
		$writing->load($this->id);
		$writing->id = 0;
		$writing->amount_inc_vat = $amount;
		$writing->amount_excl_vat = $writing->calculate_amount_excl_vat();
		$writing->search_index = $this->search_index();
		
		return $writing->save();
	}
	
	function form() {
		return "<div id=\"insert_writings\">".$this->display()."</div>";
	}
	
	function display() {
		$form = "<span class=\"button\" id=\"insert_writings_show\">".utf8_ucfirst(__('show form'))."</span>
			<span class=\"button\" id=\"insert_writings_hide\">".utf8_ucfirst(__('hide form'))."</span>
			<span class=\"button\" id=\"insert_writings_cancel\">".Html_Tag::a(link_content("content=writings.php&timestamp=".$_SESSION['start']),utf8_ucfirst(__('cancel record')))."</span>
			<div class=\"insert_writings_form\">
			<form method=\"post\" name=\"insert_writings_form\" action=\"\" enctype=\"multipart/form-data\">";
		
		$input_hidden = new Html_Input("action", "insert");
		$form .= $input_hidden->input_hidden();
		
		$categories = new Categories();
		$categories->select();
		$sources = new Sources();
		$sources->select();
		$accountingcodes = new Accounting_Codes();
		$accountingcodes->select();
		
		$datepicker = new Html_Input_Date("datepicker", $_SESSION['start']);
		$category = new Html_Select("categories_id", $categories->names());
		$source = new Html_Select("sources_id", $sources->names());
		$accountingcode = new Html_Input_Ajax("accountingcodes_id", link_content("content=writings.ajax.php"), $accountingcodes->numbers());
		$number = new Html_Input("number");
		$amount_excl_vat = new Html_Input("amount_excl_vat");
		$vat = new Html_Input("vat");
		$amount_inc_vat = new Html_Input("amount_inc_vat");
		$comment = new Html_Textarea("comment");
		$paid = new Html_Radio("paid", array(__("no"),__("yes")));
		$submit = new Html_Input("submit", __('save'), "submit");
		
		$grid = array(
			'class' => "itemsform",
			'leaves' => array(
				'date' => array(
					'value' => $datepicker->item(__('date')),
				),
				'category' => array(
					'value' => $category->item(__('category')),
				),
				'source' => array(
					'value' => $source->item(__('source')),
				),
				'accountingcode' => array(
					'value' => $accountingcode->item(__('accounting code')),
				),
				'number' => array(
					'value' => $number->item(__('piece nb')),
				),
				'amount_excl_vat' => array(
					'value' => $amount_excl_vat->item(__('amount excluding vat')),
				),
				'vat' => array(
					'value' => $vat->item(__('VAT')),
				),
				'amount_inc_vat' => array(
					'value' => $amount_inc_vat->item(__('amount including vat')),
				),
				'comment' => array(
					'value' => $comment->item(__('comment')),
				),
				'paid' => array(
					'value' => $paid->item(__('paid')),
				),
				'submit' => array(
					'value' => $submit->item(""),
				)
			)
		);				
		$list = new Html_List($grid);
		$form .= $list->show();
		
		$form .= "</form></div>";

		return $form;
	}
	
	function form_in_table() {
		$form = "<tr class=\"table_writings_form_modify\"><td colspan=\"10\" ><div id=\"table_edit_writings\">
			<span class=\"button\" id=\"table_edit_writings_cancel\">".Html_Tag::a(link_content("content=writings.php&start=".$_SESSION['start']),utf8_ucfirst(__('cancel record')))."</span>
			<div class=\"table_edit_writings_form\">
			<form method=\"post\" name=\"table_edit_writings_form\" action=\"\" enctype=\"multipart/form-data\">";
		
		$input_hidden = new Html_Input("action", "edit", "submit");
		$input_hidden_id = new Html_Input("writings_id", $this->id);
		$form .= $input_hidden->input_hidden().$input_hidden_id->input_hidden();
		
		$categories = new Categories();
		$categories->select();
		$sources = new Sources();
		$sources->select();
		$currentcode = array();
		$accountingcode = new Accounting_Code();
		if($accountingcode->load($this->accountingcodes_id)) {
			$currentcode[] = $accountingcode->number." - ".$accountingcode->name;
		}
		
		$datepicker = new Html_Input_Date("datepicker", $this->day);
		$category = new Html_Select("categories_id", $categories->names(), $this->categories_id);
		$source = new Html_Select("sources_id", $sources->names(), $this->sources_id);
		$accountingcode_input = new Html_Input_Ajax("accountingcodes_id", link_content("content=writings.ajax.php"), $currentcode);
		$number = new Html_Input("number", $this->number);
		$amount_excl_vat = new Html_Input("amount_excl_vat", $this->amount_excl_vat);
		$vat = new Html_Input("vat", $this->vat);
		$amount_inc_vat = new Html_Input("amount_inc_vat", $this->amount_inc_vat);
		$comment = new Html_Textarea("comment", $this->comment);
		$paid = new Html_Radio("paid", array(__("no"),__("yes")), $this->paid);
		$submit = new Html_Input("submit", __('save'), "submit");
		
		$grid = array(
			'class' => "itemsform",
			'leaves' => array(
				'date' => array(
					'value' => $datepicker->item(__('date')),
				),
				'category' => array(
					'value' => $category->item(__('category')),
				),
				'source' => array(
					'value' => $source->item(__('source')),
				),
				'accountingcode' => array(
					'value' => $accountingcode_input->item(__('accounting code')),
				),
				'number' => array(
					'value' => $number->item(__('piece nb')),
				),
				'amount_excl_vat' => array(
					'value' => $amount_excl_vat->item(__('amount excluding vat')),
				),
				'vat' => array(
					'value' => $vat->item(__('VAT')),
				),
				'amount_inc_vat' => array(
					'value' => $amount_inc_vat->item(__('amount including vat')),
				),
				'comment' => array(
					'value' => $comment->item(__('comment')),
				),
				'paid' => array(
					'value' => $paid->item(__('paid')),
				),
				'submit' => array(
					'value' => $submit->item(""),
				)
			)
		);				
		$list = new Html_List($grid);
		$form .= $list->show();
		
		$form .= "</form></div></div></td></tr>";

		return $form;
	}
	
	function form_duplicate() {
		$form = "<div class=\"duplicate\"><form method=\"post\" name=\"table_writings_duplicate\" action=\"\" enctype=\"multipart/form-data\">";
		$input_hidden_id = new Html_Input("table_writings_duplicate_id", $this->id);
		$input_hidden_action = new Html_Input("action", "duplicate");
		$submit = new Html_Input("table_writings_duplicate_submit", "", "submit");
		$input_hidden_value = new Html_Input("table_writings_duplicate_amount", "");
		$form .= $input_hidden_action->input_hidden().$input_hidden_id->input_hidden().$submit->input().$input_hidden_value->input_hidden();
		$form .= "</form></div>";
		
		return $form;
	}
	
	function form_delete() {
		if ($this->banks_id == 0) {
			$form = "<div class=\"delete\"><form method=\"post\" name=\"table_writings_delete\" action=\"\" enctype=\"multipart/form-data\">";
			$input_hidden_id = new Html_Input("table_writings_delete_id", $this->id);
			$input_hidden_action = new Html_Input("action", "delete");
			$submit = new Html_Input("table_writings_delete_submit", "", "submit");
			$submit->properties = array(
				'onclick' => "javascript:return confirm('".utf8_ucfirst(__('are you sure?'))."')"
			);
			$form .= $input_hidden_action->input_hidden().$input_hidden_id->input_hidden().$submit->input();
			$form .= "</form></div>";
			
			return $form;
		}
	}
	
	function form_split() {
		$form = "<div class=\"split\"><form method=\"post\" name=\"table_writings_split\" action=\"\" enctype=\"multipart/form-data\">";
		$input_hidden_id = new Html_Input("table_writings_split_id", $this->id);
		$input_hidden_action = new Html_Input("action", "split");
		$submit = new Html_Input("table_writings_split_submit", "", "submit");
		$input_hidden_value = new Html_Input("table_writings_split_amount", "");
		$form .= $input_hidden_action->input_hidden().$input_hidden_id->input_hidden().$submit->input().$input_hidden_value->input_hidden();
		$form .= "</form></div>";
		
		return $form;
	}
	
	function form_forward() {
		if ($this->banks_id == 0) {
			$form = "<div class=\"forward\"><form method=\"post\" name=\"table_writings_forward\" action=\"\" enctype=\"multipart/form-data\">";
			$input_hidden_id = new Html_Input("table_writings_forward_id", $this->id);
			$input_hidden_action = new Html_Input("action", "forward");
			$submit = new Html_Input("table_writings_forward_submit", "", "submit");
			$input_hidden_value = new Html_Input("table_writings_forward_amount", "");
			$form .= $input_hidden_action->input_hidden().$input_hidden_id->input_hidden().$submit->input().$input_hidden_value->input_hidden();
			$form .= "</form></div>";
		
			return $form;
		}
	}
	
	function form_modify() {
		return "<div class=\"modify\">".Html_Tag::a(link_content("content=writings.php&startd=".$_SESSION['start']."&writings_id=".$this->id)," ")."</div>";
	}
	
	function fill($hash) {
		$writing = parent::fill($hash);
		
		if($writing->banks_id > 0) {
			$writing->amount_excl_vat = $writing->calculate_amount_excl_vat();
		}
		
		return $writing;
	}
	
	function duplicate($amount) {
		if (is_numeric($amount) and $amount > 0) {
			for ($i=1; $i<=$amount; $i++) {
				$new_writing = $this;
				$new_writing->id = 0;
				$new_writing->day = strtotime('+1 months', $new_writing->day);
				$new_writing->banks_id = 0;
				$new_writing->save();
			}
		} else {
			$split = preg_split("/(q)|(y)|(a)|(t)|(m)/i", $amount, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			if (count($split) == 2 and is_numeric($split[0])) {
				if(preg_match("/(m)/i", $split[1])) {
					for ($i=1; $i<=$split[0]; $i++) {
						$new_writing = $this;
						$new_writing->id = 0;
						$new_writing->day = strtotime('+1 months', $new_writing->day);
						$new_writing->banks_id = 0;
						$new_writing->save();
					}
				} elseif(preg_match("/(q)|(t)/i", $split[1])) {
					for ($i=1; $i<=$split[0]; $i++) {
						$new_writing = $this;
						$new_writing->id = 0;
						$new_writing->day = strtotime('+3 months', $new_writing->day);
						$new_writing->banks_id = 0;
						$new_writing->save();
					}
				} elseif(preg_match("/(a)|(y)/i", $split[1])) {
					for ($i=1; $i<=$split[0]; $i++) {
						$new_writing = $this;
						$new_writing->id = 0;
						$new_writing->day = strtotime('+1 year', $new_writing->day);
						$new_writing->banks_id = 0;
						$new_writing->save();
					}
				}
			}
		}
	}
	
	function forward($amount) {
		if (is_numeric($amount) and $amount > 0) {
			$this->day = strtotime('+'.$amount.' months', $this->day);
			$this->save();
		} else {
			$split = preg_split("/(q)|(y)|(a)|(t)|(m)|(d)|(j)/i", $amount, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			if (count($split) == 2 and is_numeric($split[0])) {
				if(preg_match("/(m)/i", $split[1])) {
					$this->day = strtotime('+'.$split[0].' months', $this->day);
					$this->save();
				} elseif(preg_match("/(d)|(j)/i", $split[1])) {
					$this->day = strtotime('+'.($split[0]).' days', $this->day);
					$this->save();
				} elseif(preg_match("/(q)|(t)/i", $split[1])) {
					$this->day = strtotime('+'.($split[0] * 3).' months', $this->day);
					$this->save();
				} elseif(preg_match("/(a)|(y)/i", $split[1])) {
					$this->day = strtotime('+'.$split[0].' year', $this->day);
					$this->save();
				}
			}
		}
	}
	
	function show_further_information() {
		if (!empty($this->information)) {
			return "<div class=\"table_writings_comment_further_information\">".nl2br($this->information)."</div>";
		}
		return "";
	}
	
	function show_operations() {
		return $this->form_split().$this->form_modify().$this->form_duplicate().$this->form_forward().$this->form_delete();
	}
	
	function is_recently_modified(){
		if($this->timestamp > (time() - 10)) {
			return true;
		}
		return false;
	}
	
	function get_data() {
		$datas = array();
		
		$datas['classification_target'] = array(
			$this->db->config['table_categories'] => $this->categories_id,
			$this->db->config['table_accountingcodes'] => $this->accountingcodes_id
		);
		
		preg_match_all('(\w{3,})u', $this->comment, $matches['comment']);
		
		$datas['classification_data'] = array(
			'comment' => $matches['comment'][0],
			'amount_inc_vat' => array($this->amount_inc_vat)
		);
		
		return $datas;
	}
	
	function different_from(Writing $writing) {
		switch (true) {
			case $this->accountingcodes_id != $writing->accountingcodes_id:
			case $this->categories_id != $writing->categories_id:
			case $this->amount_inc_vat != $writing->amount_inc_vat:
			case $this->comment != $writing->comment:
			case $this->sources_id != $writing->sources_id:
				return true;
			default :
				return false;
		}
	}
}
