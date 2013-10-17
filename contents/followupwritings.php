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
if (isset($_POST) and !empty($_POST)) {
	$followupwritings->scale = $_POST['scale_timeseries_select'];
}
echo $followupwritings->show_timeseries_at($_GET['start']);
