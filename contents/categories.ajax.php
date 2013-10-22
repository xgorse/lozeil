<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_POST)) {
	
	if (isset($_POST['action']) and $_POST['action'] == "fill_vat") {
		$category = new Category();
		$category->load((int)$_REQUEST['value']);
		echo $category->vat;
		exit(0);
	}
	
	$category = new Category();
	$cleaned = $category->clean($_POST);
	
	if ($cleaned) {
		foreach($cleaned as $id => $values) {
			$category->load($id);
			if (!empty($values['name'])) {
				if ($category->name != $values['name'] or $category->vat != $values['vat'] or $category->vat_category != $values['vat_category']) {
					$category->name = $values['name'];
					$category->vat = $values['vat'];
					$category->vat_category = $values['vat_category'];
					$category->save();
				}
			} elseif ($category->is_deletable()) {
				$category->delete();
			}
		}
	}
	
	$categories = new Categories();
	$categories->select();
	echo json_encode(array('status' => show_status(), 'table' => $categories->show_form()));
}

exit(0);
