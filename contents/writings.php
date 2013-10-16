<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (!isset($_SESSION['order']['name']) or !isset($_SESSION['order']['direction'])) {
	$_SESSION['order']['name'] = 'day';
	$_SESSION['order']['direction'] = 'ASC';
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

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$writings = new Writings();
$writings->add_order($_SESSION['order']['name']." ".$_SESSION['order']['direction']);
$writings->add_order("number DESC, amount_inc_vat DESC");

if (isset($_GET['start']) and isset($_GET['stop'])) {
	$_SESSION['filter'] = array('start' => $_GET['start'], 'stop' => $_GET['stop']);
} elseif (!isset($_SESSION['filter']) or empty($_SESSION['filter'])) {
	list($start, $stop) = determine_month(time());
	$_SESSION['filter'] = array('start' => $start, 'stop' => $stop);
}

$writings->filter_with($_SESSION['filter']);
$writings->select();

$heading = new Heading_Area(utf8_ucfirst(__('consult balance sheet')), $writings->display_timeline_at($_SESSION['filter']['start']), $writings->form_filter($_SESSION['filter']['start'], $_SESSION['filter']['stop']));
echo $heading->show();

echo $writings->display();
echo $writings->modify_options();
echo $writings->form_update_bayesian_code();

$writing = new Writing();
echo $writing->form();
