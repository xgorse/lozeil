<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST['submit'])) {
	if(!empty($_POST['name_new'])) {
		$category = new Category();
		$category->name = $_POST['name_new'];
		if(isset($_POST['vat_new'])) {
			$category->vat = str_replace(",", ".", $_POST['vat_new']);
		}
		$category->save();
	}
	
	if (isset($_POST['category'])) {
		foreach ($_POST['category'] as $id => $values) {
			$category = new Category();
			$category->load($id);
			if (!empty($values['name'])) {
				$category->name = $values['name'];
				$values['vat'] = str_replace(",", ".", $values['vat']);
				if (!empty($values['vat']) and is_numeric($values['vat'])) {
					$category->vat = $values['vat'];
				}
				$category->save();
			} elseif (empty($values['name']) and $category->is_deletable()) {
				$category->delete();
			}
		}
	}
}

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the categories')));
echo $heading->show();

$categories = new Categories();
$categories->select();
echo $categories->show_form();