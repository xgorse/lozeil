<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Menu_Area {
	public $header = "";
	public $actions = array();
	public $handle = "";
	public $img_width = 65.875;
	public $img_height = 17;
	public $img_src = "medias/images/logo.png";

	function __construct() {
		$this->img_src = $GLOBALS['config']['layout_mediaserver'].$this->img_src;
		
		$writings = new Writings();
		$writings->filter_with(array('stop' => time()));
		$writings->select_columns('amount_inc_vat');
		$writings->select();
		$this->header = $writings->display_balance_on_current_date();
		
		$this->handle = "<span id=\"menu_handle_hide\">".utf8_ucfirst(__("more"))."</span><span id=\"menu_handle_show\">".utf8_ucfirst(__('less'))."</span>";
	}

	function show() {
		$content = "<div class=\"menu\"><div class=\"menu_header\">";
		if (!empty($this->header)) {
			$content .= "<div id=\"menu_header_balance\">".$this->header."</div>";
		}
		if (!empty($this->img_src)) {
			$content .= "<div id=\"menu_header_logo\"><img ".(!$this->img_width ? "" : " width=\"".$this->img_width."\"").(!$this->img_height ? "" : " height=\"".$this->img_height."\"")." src=\"".$this->img_src."\"></div>";
		}
		$content .= "<div id=\"menu_header_logout\">".Html_Tag::a(link_content("content=logout.php"),__('logout'))."</div>";
		$content .= "</div><div class=\"menu_actions\">";
		if (!empty($this->actions)) {
			$content .= $this->actions;
		}
		$content .= "</div>";
		if (!empty($this->handle)) {
			$content .= "<div class=\"menu_handle hide\">".$this->handle."</div>";
		}
		$content .= "</div>";
		return $content;
	}
	
	function grid_other_actions() {
		$grid = array(
			'leaves' => array(
				'calculate_vat' => array(
					'value' => utf8_ucfirst(__("automatically calculate vat to"))." ".$this->form_calculate_vat()
				),
				'change_view' => array(
					'value' => $this->form_change_view(),
				),
			)
		);				
		return $grid;
	}
	
	function show_grid_other_actions() {
		$list = new Html_List($this->grid_other_actions());
		return $list->show();
	}
	
	function grid_navigation() {
		return array(
			'leaves' => array (
				array(
					'value' => Html_tag::a(link_content("content=writings.php"), utf8_ucfirst(__("consult balance sheet")))
				),
				array(
					'value' => Html_tag::a(link_content("content=followupwritings.php"), utf8_ucfirst(__("consult statistics")))
				),
				array(
					'value' => Html_tag::a(link_content("content=writingssimulations.php"), utf8_ucfirst(__("make a simulation")))
				),
				array(
					'value' => utf8_ucfirst(__("manage the"))." ".
							   Html_tag::a(link_content("content=categories.php"), __("categories")).", ".
							   Html_tag::a(link_content("content=sources.php"), __("sources")).", ".
							   Html_tag::a(link_content("content=banks.php"), __("banks")).", ".
							   Html_tag::a(link_content("content=accountingplan.php"), __("accounting plan"))
				),
				array(
					'value' => $this->form_import_bank()
				),
				array(
					'value' => $this->form_import_source()
				),
				array(
					'value' =>  "<a id=\"menu_actions_export_label\" href=\"\">".utf8_ucfirst(__("export writings"))."</a>".$this->form_export()
				),
				array(
					'value' =>  "<a id=\"menu_actions_other\" href=\"\">".utf8_ucfirst(__("other actions"))."</a>".
						$this->show_grid_other_actions()
				)
			)
		);	
	}
	
	function prepare_navigation() {
		$list = new Html_List($this->grid_navigation());
		$this->actions = $list->show();
	}
	
	function form_change_view() {
		if (!$_SESSION['accountant_view']) {
			$label = utf8_ucfirst(__("change to accountant's view"));
		} else {
			$label = utf8_ucfirst(__("change to normal view"));
		}
		$submit_view = new Html_Input("submit_change_view", $label, "submit");
		$submit_view->properties = array('class' => 'submit_as_link');
		
		$form = "<form method=\"post\" name=\"menu_actions_other_change_view\" action=\"".link_content("content=writings.php")."\" enctype=\"multipart/form-data\">".
					$submit_view->input()
				."</form>";
		return $form;
	}
	
	function form_calculate_vat() {
		$date = new Html_Input_Date("vat_date", determine_vat_date());
		$date->img_src = "medias/images/link_calendar_white.png";
		$submit = new Html_Input("submit_calculate_vat", __('ok'), "submit");
		$form = "<form method=\"post\" name=\"menu_actions_other\" action=\"".link_content("content=writings.php")."\" enctype=\"multipart/form-data\">".
					$date->item("")." ".$submit->input()
				."</form>";
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
	
	function form_import_bank() {
		$banks = new Banks();
		$banks->select();
		$form = "<div id=\"menu_actions_import_bank\"><form method=\"post\" name=\"menu_actions_import_bank_form\" action=\"".link_content("content=writingsimport.php")."\" enctype=\"multipart/form-data\">";
		$import_file = new Html_Input("menu_actions_import_file", "", "file");
		$bank_select = new Html_Select("menu_actions_import_bank", $banks->names_of_selected_banks());
		$submit = new Html_Input("menu_actions_import_submit", "Ok", "submit");
		$form .= "<a class=\"menu_actions_import_label\" href=\"\">".utf8_ucfirst(__("import bank statement"))."</a>".$import_file->item("").$bank_select->item("").$submit->input();
		$form .= "</form></div>";
		return $form;
	}
	
	function form_import_source() {
		$sources = new Sources();
		$sources->select();
		$form = "<div id=\"menu_actions_import_source\"><form method=\"post\" name=\"menu_actions_import_source_form\" action=\"".link_content("content=writingsimport.php")."\" enctype=\"multipart/form-data\">";
		$import_file = new Html_Input("menu_actions_import_file", "", "file");
		$bank_select = new Html_Select("menu_actions_import_source", $sources->names());
		$submit = new Html_Input("menu_actions_import_submit", "Ok", "submit");
		$form .= "<a class=\"menu_actions_import_label\" href=\"\">".utf8_ucfirst(__("import writings from source"))."</a>".$import_file->item("").$bank_select->item("").$submit->input();
		$form .= "</form></div>";
		return $form;
	}
}
