<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (!isset($_GET['start'])) {
	$_GET['start'] = time();
}


$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('consult statistics')));
echo $heading->show();

$followupwritings = new Writings_Followup();

if (isset($_GET['filter'])) {
	$followupwritings->filter = $_GET['filter'];
}

if (isset($_GET['scale'])) {
	$followupwritings->scale = $_GET['scale'];
}

if (isset($_POST) and !empty($_POST)) {
	$followupwritings->scale = $_POST['scale_timeseries_select'];
	$followupwritings->filter = $_POST['filter_timeseries_select'];
}

switch ($followupwritings->filter) {
	case 'categories':
		echo $followupwritings->show_timeseries_per_category_at($_GET['start']);
		break;
	
	case 'banks':
		echo $followupwritings->show_timeseries_per_bank_at($_GET['start']);
		break;

	default:
		break;
}
