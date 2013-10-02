<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST['submit'])) {
	unset($_POST['submit']);
		
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
			if (!empty($values['name'])) {
				$bank->name = $values['name'];
				if (isset($values['selected'])) {
					$bank->selected = 1;
				}
				$bank->save();
			} elseif (empty($values['name']) and $bank->is_deletable()) {
				$bank->delete();
			}
		}
	}
}


$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the banks')));
echo $heading->show();

$banks = new Banks();
$banks->select();
echo $banks->show_form();