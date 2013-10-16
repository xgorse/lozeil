<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST)) {
	if (isset($_POST['action']) and $_POST['action'] == "fill_vat") {
		$category = new Category();
		$category->load((int)$_REQUEST['value']);
		echo $category->vat;
		exit(0);
	}
	
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
				$values['vat'] = str_replace(",", ".", $values['vat']);
				if ($category->name != $values['name'] or $category->vat != $values['vat']) {
					$category->name = $values['name'];
					if (!empty($values['vat']) and is_numeric($values['vat'])) {
						$category->vat = $values['vat'];
					}
					$category->save();
				}
			} elseif (empty($values['name']) and $category->is_deletable()) {
				$category->delete();
			}
		}
	}
	$categories = new Categories();
	$categories->select();
	echo json_encode(array('status' => show_status(), 'table' => $categories->show_form()));
}

exit(0);
