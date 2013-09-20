<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$writings = new Writings();
list($start, $stop) = determine_month($_SESSION['timestamp']);

switch ($_REQUEST['action']) {
	
	case "merge":
		$writing_from = new Writing();
		$writing_from->load((int)$_REQUEST['writing_from']);
		$writing_into = new Writing();
		$writing_into->load((int)$_REQUEST['writing_into']);
		$writing_into->merge_from($writing_from);
		break;
	
	case "edit":
		$writing = new Writing();
		$writing->load((int)$_REQUEST['id']);
		echo $writing->form_in_table();
		exit(0);
		break;
	
	case "filter":
		$_SESSION['filter_value_*'] = $_REQUEST['value'];
		break;
	
	case "sort":
		if ($_SESSION['order_col_name'] == $_REQUEST['order_col_name']) {
			$_SESSION['order_direction'] = $_SESSION['order_direction'] == "ASC" ? "DESC" : "ASC";
		} else {
			$_SESSION['order_col_name'] = $_REQUEST['order_col_name'];
		}
		break;
			
	case 'split':
		if (isset($_REQUEST['table_writings_split_amount'])) {
			$amount = str_replace(",", ".", $_REQUEST['table_writings_split_amount']);
			if (is_numeric($amount)) {
				$writing = new Writing();
				$writing->load((int)$_REQUEST['table_writings_split_id']);
				$writing->split($amount);
			}
		}
		break;
		
	default :
		break;
		
}
if (isset($_SESSION['filter_value_*']) and !empty($_SESSION['filter_value_*'])) {
	$writings->filter_with(array('*' => $_SESSION['filter_value_*']));
}
$writings->add_order($_SESSION['order_col_name']." ".$_SESSION['order_direction']);
$writings->filter_with(array('start' => $start, 'stop' => $stop));
$writings->select();
echo $writings->show();
exit(0);
