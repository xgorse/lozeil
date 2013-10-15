<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_FILES) and $_FILES['menu_actions_import_file']['error'] == 0) {
	if (isset($_POST['menu_actions_import_bank']) and ($_POST['menu_actions_import_bank']) > 0) {
		$data = new Writings_Data_File($_FILES['menu_actions_import_file']['tmp_name'], $_FILES['menu_actions_import_file']['name'], $_FILES['menu_actions_import_file']['type']);
		$data->banks_id = $_POST['menu_actions_import_bank'];
		$data->import();
		$_SESSION['filter'] = $data->filters_after_import();
	} elseif (isset($_POST['menu_actions_import_source']) and ($_POST['menu_actions_import_source']) > 0) {
		$data = new Writings_Data_File($_FILES['menu_actions_import_file']['tmp_name'], $_FILES['menu_actions_import_file']['name'], $_FILES['menu_actions_import_file']['type']);
		$data->sources_id = $_POST['menu_actions_import_source'];
		$data->import();
		$_SESSION['filter'] = $data->filters_after_import();
	}
}

header("Location: ".link_content("content=writings.php"));
exit(0);
