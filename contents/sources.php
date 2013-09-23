<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST['submit'])) {
	unset($_POST['submit']);
	
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


$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the sources')));
echo $heading->show();

$sources = new Sources();
$sources->select();
echo $sources->show_form();