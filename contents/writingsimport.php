<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_FILES) and $_FILES['menu_actions_import_file']['error'] == 0) {
	if (($_POST['menu_actions_import_bank']) > 0) {
		$data = new Writings_Data_File($_FILES['menu_actions_import_file']['tmp_name'], $_FILES['menu_actions_import_file']['name'], $_FILES['menu_actions_import_file']['type']);
		$data->banks_id = $_POST['menu_actions_import_bank'];
		$data->import();
	}
}
header("Location: ".link_content("content=writings.php"));
exit(0);
