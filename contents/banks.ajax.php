<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST) and !empty($_POST)) {
		
	if(!empty($_POST['name_new'])) {
		$bank = new Bank();
		$bank->name = $_POST['name_new'];
		if(isset($_POST['selected_new'])) {
			$bank->selected = 1;
		}
		$bank->save();
	}
	
	if (isset($_POST['bank'])) {
		foreach ($_POST['bank'] as $id => $values) {
			$bank = new Bank();
			$bank->load($id);
			$selected = isset($values['selected']) ? 1 : 0;
			if (($bank->name != $values['name'] and !empty($values['name'])) or $selected != $bank->selected) {
				$bank->name = $values['name'];
				$bank->selected = $selected;
				$bank->save();
			} elseif (empty($values['name']) and $bank->is_deletable()) {
				$bank->delete();
			}
		}
	}
}

$banks = new Banks();
$banks->select();
echo json_encode(array('status' => show_status(), 'table' => $banks->show_form()));
