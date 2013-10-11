<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (!isset($_SESSION['order_col_name']) or !isset($_SESSION['order_direction'])) {
	$_SESSION['order_col_name'] = 'day';
	$_SESSION['order_direction'] = 'ASC';
}

if (isset($_POST['action']) and $_POST['action'] == "update_bayesian_element") {
	$bayesianelements = new Bayesian_Elements();
	$bayesianelements->train();
}

if (isset($_REQUEST['action'])) {
	switch ($_REQUEST['action']) {
	case "open_attachment" :
		$file = new File();
		$file->load((int)$_REQUEST['id']);
		$file->open_attachment();
		exit();
		break;
	default:
		break;
	}
}

$start = determine_integer_from_post_get_session(null, "start");
$stop = determine_integer_from_post_get_session(null, "stop");

if (($start > 0 and strlen($start) <= 12) and ($stop > 0 and strlen($stop) <= 12)) {
	$_SESSION['start'] = $start;
	$_SESSION['stop'] = $stop;
} else {
	list($_SESSION['start'], $_SESSION['stop']) = determine_month(mktime(0, 0, 0, date("m"), 1, date("Y")));
}

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$writings = new Writings();
$writings->add_order($_SESSION['order_col_name']." ".$_SESSION['order_direction']);
$writings->add_order("amount_inc_vat DESC");

$writings_filter_value = "";
if (isset($_SESSION['filter_value_*']) and !empty($_SESSION['filter_value_*'])) {
	$writings_filter_value = $_SESSION['filter_value_*'];
	$writings->filter_with(array('*' => $writings_filter_value));
}
$writings->filter_with(array('start' => $_SESSION['start'], 'stop' => $_SESSION['stop']));
$writings->select();

$heading = new Heading_Area(utf8_ucfirst(__('consult balance sheet')), $writings->display_timeline_at($_SESSION['start']), $writings->form_filter($start, $stop, $writings_filter_value).$writings->form_cancel_last_operation());
echo $heading->show();

echo $writings->display();
echo $writings->modify_options();
echo $writings->form_update_bayesian_code();

$writing = new Writing();
echo $writing->form();
