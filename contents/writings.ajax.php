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
	
	case 'edit':
		if (isset($_POST['id']) and $_POST['id'] > 0) {
			$writing = new Writing();
			$writing->load($_POST['id']);
			$writing->fill($_POST);
			$writing->save();
		}
		break;
			
	case "form_edit":
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
		
	case 'duplicate':
			if (isset($_POST['table_writings_duplicate_id']) and isset($_POST['table_writings_duplicate_amount'])) {
				$writing = new Writing();
				$writing->load((int)$_POST['table_writings_duplicate_id']);
				$writing->duplicate($_POST['table_writings_duplicate_amount']);
			}
			break;
					
	case 'forward':
		if (isset($_POST['table_writings_forward_id']) and isset($_POST['table_writings_forward_amount'])) {
			$writing = new Writing();
			$writing->load((int)$_POST['table_writings_forward_id']);
			$writing->forward($_POST['table_writings_forward_amount']);
		}
		break;
			
	default :
		break;
		
}
if (isset($_SESSION['filter_value_*']) and !empty($_SESSION['filter_value_*'])) {
	$writings->filter_with(array('*' => $_SESSION['filter_value_*']));
}
$writings->add_order($_SESSION['order_col_name']." ".$_SESSION['order_direction']);
$writings->add_order("amount_inc_vat DESC");
$writings->filter_with(array('start' => $start, 'stop' => $stop));
$writings->select();
echo $writings->show();
exit(0);
