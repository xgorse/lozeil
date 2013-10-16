<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST) and !empty($_POST)) {
	
	if(!empty($_POST['name_new'])) {
		$source = new Source();
		$source->name = $_POST['name_new'];
		$source->save();
	}
	unset($_POST['name_new']);
	
	foreach ($_POST as $id => $name) {
		$source = new Source();
		$source->load($id);
		if ($source->name != $name and !empty($name)) {
			$source->name = $name;
			$source->save();
		} elseif (empty($name) and $source->is_deletable()) {
			$source->delete();
		}
	}
}
$sources = new Sources();
$sources->select();
echo json_encode(array('status' => show_status(), 'table' => $sources->show_form()));
