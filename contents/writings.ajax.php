<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$writings = new Writings();

if (!empty($_FILES)) {
	$file = new File();
	$file->save_attachment($_FILES);
	echo json_encode(array('status' => show_status()));
	exit(0);
}

switch ($_REQUEST['action']) {
	
	case "delete_attachment" :
		$file = new File();
		$file->load((int)$_REQUEST['id']);
		$file->delete_attachment();
		$writing = new Writing();
		$writing->load($file->writings_id);
		echo json_encode(array('status' => show_status(), 'link' => $writing->link_to_file_attached()));
		exit(0);
		break;
	
	case "search":
		$accounting_codes = new Accounting_Codes();
		$accounting_codes->fullname = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
		$accounting_codes->set_limit(10, 0);
		$output = "";
		if ($accounting_codes->fullname) {
			$accounting_codes->select();
			$output = json_encode($accounting_codes->fullnames());
		}
		echo $output;
		exit(0);
		break;	
	
	case "merge":
		$writing_from = new Writing();
		$writing_from->load((int)$_REQUEST['writing_from']);
		$writing_into = new Writing();
		$writing_into->load((int)$_REQUEST['writing_into']);
		$writing_into->merge_from($writing_from);
		break;
	
	case "edit":
		if (isset($_POST['writings_id']) and $_POST['writings_id'] > 0) {
			$writing = new Writing();
			$writing->load((int)$_POST['writings_id']);
			$writing_before = clone $writing;
			$cleaned = $writing->clean($_POST);
			$writing->fill($cleaned);
			$writing->save();

			$bayesianelements = new Bayesian_Elements();
			$bayesianelements->increment_decrement($writing_before, $writing);
		}
		break;
			
	case "form_edit":
		$writing = new Writing();
		$writing->load((int)$_REQUEST['id']);
		echo $writing->form_in_table();
		
		exit(0);
		break;
	
	case "filter":
		if (is_datepicker_valid($_POST['filter_day_start']) and is_datepicker_valid($_POST['filter_day_stop'])) {
			$cleaned = $writings->clean_filter_from_ajax($_POST);
			$_SESSION['filter'] = $cleaned;
		}
		break;
	
	case "sort":
		if ($_SESSION['order']['name'] == $_REQUEST['order_col_name']) {
			$_SESSION['order']['direction'] = $_SESSION['order']['direction'] == "ASC" ? "DESC" : "ASC";
		} else {
			$_SESSION['order']['name'] = $_REQUEST['order_col_name'];
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
		
	case 'insert':
		$writing = new Writing();
		$cleaned = $writing->clean($_POST);
		$writing->fill($cleaned);
		$bayesianelements = new Bayesian_Elements();
		$bayesianelements->stuff_with($writing);
		$bayesianelements->increment();
		$writing->save();
		break;
	
	case 'reload_insert_form':
		$writing = new Writing();
		echo $writing->display();
		
		exit(0);
		break;
	
	case 'reload_select_modify_writings':
		$writing = new Writings();
		echo $writing->modify_options();
		
		exit(0);
		break;
	
	case 'delete':
		if (isset($_POST['table_writings_delete_id'])) {
			$writing = new Writing($_POST['table_writings_delete_id']);
			$writing->delete();
		}
		break;
	
	case 'form_options' :
		if ($_POST['option'] == 'delete') {
			$writings->delete_from_ids(json_decode($_POST['ids']));
		} else {
			echo $writings->determine_show_form_modify($_POST['option']);
			exit(0);
		}
		break;
		
	case 'writings_modify' :
		$writings_to_modify = new Writings();
		$parameters = $writings_to_modify->clean_from_ajax($_POST);
		if (isset($parameters['id'])) {
			$writings_to_modify->id = $parameters['id'];
			$writings_to_modify->select();
			$writings_to_modify->apply($parameters['operation'], $parameters['value']);
		}
		break;
	
	default :
		break;
		
}
$writings->set_limit($GLOBALS['param']['nb_max_writings']);
$writings->filter_with($_SESSION['filter']);

$writings->add_order($_SESSION['order']['name']." ".$_SESSION['order']['direction']);
$writings->add_order("number DESC, amount_inc_vat DESC");
$writings->select();

echo json_encode(array('status' => show_status(), 'table' => $writings->show()));
exit(0);
