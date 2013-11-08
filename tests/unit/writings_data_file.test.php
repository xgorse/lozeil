<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require_once dirname(__FILE__)."/../inc/require.inc.php";

class tests_Writings_Data_File extends TableTestCase {
	function __construct() {
		parent::__construct();
		$this->initializeTables(
			"banks",
			"categories",
			"bayesianelements",
			"writings",
			"writingsimported"
		);
	}
	
	function test_is_qif() {
		$import = new Writings_Data_File("temp.123", "temp.QIF", "");
		$this->assertTrue($import->is_qif());
		$import = new Writings_Data_File("temp.123", "temp.csv", "");
		$this->assertFalse($import->is_qif());
	}
	
	function test_is_ofx() {
		$import = new Writings_Data_File("temp.123", "temp.OFX", "");
		$this->assertTrue($import->is_ofx());
		$import = new Writings_Data_File("temp.123", "temp.csv", "");
		$this->assertFalse($import->is_ofx());
	}
	
	function test_is_csv() {
		$import = new Writings_Data_File("temp.123", "temp.CSV", "");
		$this->assertTrue($import->is_csv());
		$import = new Writings_Data_File("temp.123", "temp.ofx", "");
		$this->assertFalse($import->is_csv());
	}
	
	function test_is_line_paybox() {
		$import = new Writings_Data_File();
		$mydata = array (
				0 => '967003686',
				1 => '261556',
				2 => '5135830',
				3 => '001',
				4 => 'OPENTIME.FR',
				5 => '507355493',
				6 => '13/08/2013',
				7 => '966899879',
				8 => '1024997136',
				9 => '12/08/2013',
				10 => '0917',
				11 => '12/08/2013',
				12 => 'opentime.fr',
				13 => 'PPPS->AutoDebitAbonne',
				14 => 'Autorisation',
				15 => 'Paybox Direct Plus',
				16 => '652256', 
				17 => '26910',
				18 => '978',
				19 => '',
				20 => '',
				21 => 'FRA',
				22 => '',
				23 => 'CB-Visa',
				24 => '',
				25 => '',
				26 => '',
				27 => 'aaaaaa',
				28 => 'T�l�collect�',
				29 => '',
				30 => '', 
				31 => ''
			);
		$mydata2 = array (
				0 => '967003686',
				1 => '261556',
				2 => '5135830',
				3 => '001',
				4 => 'OPENTIME.FR',
				5 => '507355493',
				6 => '13.08.2013',
				7 => '966899879',
				8 => '1024997136',
				9 => '12/08/2013',
				10 => '0917',
				11 => '12/08/2013',
				12 => 'opentime.fr',
				13 => 'PPPS->AutoDebitAbonne',
				14 => 'Autorisation',
				15 => 'Paybox Direct Plus',
				16 => '652256', 
				17 => '26910',
				18 => '978',
				19 => '',
				20 => '',
				21 => 'FRA',
				22 => '',
				23 => 'CB-Visa',
				24 => '',
				25 => '',
				26 => '',
				27 => 'aaaaaa',
				28 => 'T�l�collect�',
				29 => '',
				30 => '', 
				31 => ''
			);
		$this->assertTrue($import->is_line_paybox($mydata));
		$this->assertFalse($import->is_line_paybox($mydata2));
	}
	
	function test_is_line_cic() {
		$import = new Writings_Data_File();
		$mydata = array("01/07/2013", "01/07/2013", "", "152,20", "test de libellé", "123456");
		$mydata2 = array("01/07/2013", "01/07/2013", "-251", "152,20", "test de libellé", "123456");
		$mydata3 = array("01/07/2013", "01/07/2013", "", "", "test de libellé", "123456");
		$mydata4 = array("01/07/2013", "01/07/2013", "-25", "", "test de libellé", "123456");
		$this->assertTrue($import->is_line_cic($mydata));
		$this->assertFalse($import->is_line_cic($mydata2));
		$this->assertFalse($import->is_line_cic($mydata3));
		$this->assertTrue($import->is_line_cic($mydata4));
	}
	
	function test_is_line_coop() {
		$import = new Writings_Data_File();
		$mydata = array("27/08/2013", "" , "", "12.52","DEBIT");
		$mydata2 = array("1275212000", "" , "", "12.52","DEBIT");
		$mydata3 = array("27/08/2013", "" , "", "12.52","");
		$this->assertTrue($import->is_line_coop($mydata));
		$this->assertFalse($import->is_line_coop($mydata2));
		$this->assertFalse($import->is_line_coop($mydata3));
	}
	
	function test_is_paybox() {
		$mydata = array (
			0 => array (
				0 => 'RemittancePaybox',
				1 => 'Bank',
				2 => 'Site',
				3 => 'Rank',
				4 => 'ShopName',
				5 => 'IdPaybox',
				6 => 'Date',
				7 => 'TransactionId',
				8 => 'IdAppel',
				9 => 'DateOfIssue',
				10 => 'HourOfIssue',
				11 => 'DateOfExpiry',
				12 => 'Reference',
				13 => 'Origin',
				14 => 'Type',
				15 => 'Canal',
				16 => 'NumberOfAuthorization',
				17 => 'Amount',
				18 => 'Currency',
				19 => 'Entity',
				20 => 'Operator',
				21 => 'Country',
				22 => 'CountryIP',
				23 => 'Payment',
				24 => 'ThreeDSecureStatus',
				25 => 'ThreeDSecureInscription',
				26 => 'ThreeDSecureWarranted',
				27 => 'RefArchive',
				28 => 'Status',
				29 => 'PAN',
				30 => 'IP',
				31 => 'ErrorCode'
			),
			1 => array (
				0 => '967003686',
				1 => '261556',
				2 => '5135830',
				3 => '001',
				4 => 'OPENTIME.FR',
				5 => '507355493',
				6 => '13/08/2013',
				7 => '966899879',
				8 => '1024997136',
				9 => '12/08/2013',
				10 => '0917',
				11 => '12/08/2013',
				12 => 'opentime.fr',
				13 => 'PPPS->AutoDebitAbonne',
				14 => 'Autorisation',
				15 => 'Paybox Direct Plus',
				16 => '652256', 
				17 => '26910',
				18 => '978',
				19 => '',
				20 => '',
				21 => 'FRA',
				22 => '',
				23 => 'CB-Visa',
				24 => '',
				25 => '',
				26 => '',
				27 => 'aaaaaa',
				28 => 'T�l�collect�',
				29 => '',
				30 => '', 
				31 => ''
			)
		);
		$data = new Writings_Data_File();
		$row = 0;
		foreach ($mydata as $line) {
			foreach ($line as $key => $value) {
				$data->csv_data[$row][$key] = trim($value);
			}
			$row++;
		}
		$this->assertTrue($data->is_paybox($data->csv_data));
		$data->csv_data[0][6] = "Autre champ";
		$this->assertFalse($data->is_paybox($data->csv_data));
	}
	
	function test_is_cic() {
		$mydata = array(
			array("Date d'opération","Date de valeur","Débit","Crédit","Libellé","Solde"),
			array("02/07/2013", "01/07/2013", "", "152,20", "test de libellé", "1252,20"),
			array("05/07/2013", "04/07/2013", "-120,50", "", "test de libellé 2", "1300,20"),
			array("05/07/2013", "04/07/2013", "-120,50", "", "", "1300,20")
			);
		$data = new Writings_Data_File();
		$row = 0;
		foreach ($mydata as $line) {
			foreach ($line as $key => $value) {
				$data->csv_data[$row][$key] = trim($value);
			}
			$row++;
		}
		$this->assertTrue($data->is_cic($data->csv_data));
		$data->csv_data[0][1] = "Autre champ";
		$this->assertFalse($data->is_cic($data->csv_data));
	}
	
	function test_is_coop() {
		$mydata = array(
			array("Date","Libellé","Libellé complémentaire","Montant","Sens","Numéro de chèque","Référence Interne de l'Opération",
				"Nom de l'émetteur","Identifiant de l'émetteur","Nom du destinataire","Identifiant du destinataire",
				"Nom du tiers débiteur","Identifiant du tiers débiteur","Nom du tiers créancier","Identifiant du tiers créancier",
				"Libellé de Client à Client - Motif","Référence de Client à Client","Référence de la Remise","Référence de la Transaction",
				"Référence Unique du Mandat","Séquence de Présentation"),
			array("02/07/2013", "libellé 1", " libellé complémentaire 1", "152,20", "DEBIT", "Numéro de chèque 1","Référence Interne de l'Opération 1",
				"Nom de l'émetteur 1","Identifiant de l'émetteur 1","Nom du destinataire 1","Identifiant du destinataire 1",
				"Nom du tiers débiteur 1","Identifiant du tiers débiteur 1","Nom du tiers créancier 1","Identifiant du tiers créancier 1",
				"Libellé de Client à Client - Motif 1","Référence de Client à Client 1","Référence de la Remise 1","Référence de la Transaction 1",
				"Référence Unique du Mandat 1","Séquence de Présentation 1"),
			array("03/07/2013", "", "", "152,20", "CREDIT")
			);
		$data = new Writings_Data_File();
		$row = 0;
		foreach ($mydata as $line) {
			foreach ($line as $key => $value) {
				if ($key == 0) {
					$time = explode("/", $value);
					if (isset($time[1]) and $time[2]) {
						$value = mktime(0, 0, 0, $time[1], $time[0], $time[2]);
					}
				}
				if ($key == 3 and $value!= "Montant") {
					$value = (float)str_replace(",", ".", $value);
				}
				$data->csv_data[$row][$key] = trim($value);
			}
		  $row++;
		}
		$this->assertTrue($data->is_coop($data->csv_data));
		$data->csv_data[0][0] = "Autre champ";
		$this->assertFalse($data->is_coop($data->csv_data));
	}
	
	function test_import_as_paybox() {
		$name = tempnam('/tmp', 'csv');
		
		$mydata = array (
			0 => array (
				0 => 'RemittancePaybox',
				1 => 'Bank',
				2 => 'Site',
				3 => 'Rank',
				4 => 'ShopName',
				5 => 'IdPaybox',
				6 => 'Date',
				7 => 'TransactionId',
				8 => 'IdAppel',
				9 => 'DateOfIssue',
				10 => 'HourOfIssue',
				11 => 'DateOfExpiry',
				12 => 'Reference',
				13 => 'Origin',
				14 => 'Type',
				15 => 'Canal',
				16 => 'NumberOfAuthorization',
				17 => 'Amount',
				18 => 'Currency',
				19 => 'Entity',
				20 => 'Operator',
				21 => 'Country',
				22 => 'CountryIP',
				23 => 'Payment',
				24 => 'ThreeDSecureStatus',
				25 => 'ThreeDSecureInscription',
				26 => 'ThreeDSecureWarranted',
				27 => 'RefArchive',
				28 => 'Status',
				29 => 'PAN',
				30 => 'IP',
				31 => 'ErrorCode'
			),
			1 => array (
				0 => '967003686',
				1 => '261556',
				2 => '5135830',
				3 => '001',
				4 => 'OPENTIME.FR',
				5 => '507355493',
				6 => '13/08/2013',
				7 => '966899879',
				8 => '1024997136',
				9 => '12/08/2013',
				10 => '0917',
				11 => '12/08/2013',
				12 => 'opentime.fr',
				13 => 'PPPS->AutoDebitAbonne',
				14 => 'Autorisation',
				15 => 'Paybox Direct Plus',
				16 => '652256', 
				17 => '26910',
				18 => '978',
				19 => '',
				20 => '',
				21 => 'FRA',
				22 => '',
				23 => 'CB-Visa',
				24 => '',
				25 => '',
				26 => '',
				27 => 'aaaaaa',
				28 => 'Telecollecte',
				29 => '',
				30 => '109.190.127.105',
				31 => ''
			),
			2 => array (
				0 => '967003686',
				1 => '261556',
				2 => '5135830',
				3 => '001',
				4 => 'LOZEIL',
				5 => '507355493',
				6 => '13/08/2013',
				7 => '966899879',
				8 => '1024997136',
				9 => '12/08/2013',
				10 => '0917',
				11 => '12/08/2013',
				12 => 'lozeil',
				13 => 'adrien.delannoy@noparking.net',
				14 => 'Autorisation',
				15 => 'Paybox Direct Plus',
				16 => '652256', 
				17 => '24000',
				18 => '978',
				19 => '',
				20 => '',
				21 => 'FRA',
				22 => '',
				23 => 'CB-Mastercard',
				24 => '',
				25 => '',
				26 => '',
				27 => 'aaaaaa',
				28 => 'Telecollecte',
				29 => '',
				30 => '', 
				31 => ''
			)
		);
		
		$handle = fopen($name, 'w+');
		
		foreach($mydata as $data) {
			fputcsv($handle, $data, ";");
		}
		
		$data = new Writings_Data_File($name);
		$data->sources_id = 1;
		$data->prepare_csv_data();
		$data->import_as_paybox();
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 1,
				'amount_inc_vat' => "269.1000000",
				'sources_id' => 1,
				'comment' => "opentime.fr PPPS->AutoDebitAbonne",
				'information' => "ShopName : OPENTIME.FR
TransactionId : 966899879
Canal : Paybox Direct Plus
Country : FRA
Payment : CB-Visa
Status : Telecollecte
IP : 109.190.127.105
"
			)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 2,
				'amount_inc_vat' => "240.000000",
				'sources_id' => 1,
				'comment' => "lozeil adrien.delannoy@noparking.net",
				'information' => "ShopName : LOZEIL
TransactionId : 966899879
Canal : Paybox Direct Plus
Country : FRA
Payment : CB-Mastercard
Status : Telecollecte
"
			)
		);
		$data = new Writings_Data_File($name);
		$data->sources_id = 1;
		$data->prepare_csv_data();
		$data->import_as_cic();
		
		fclose($handle);
		unlink($name);
		$writing = new Writing();
		$this->assertFalse($writing->load(3));
		$this->truncateTable("writings");
		$this->truncateTable("writingsimported");
	}
	
	function test_import_as_cic() {
		$name = tempnam('/tmp', 'csv');
		
		$mydata = array(
			array("Date d'opération","Date de valeur","Débit","Crédit","Libellé","Solde"),
			array("02/07/2013", "01/07/2013", "", "152,20", "test de libellé", "1252,20"),
			array("05/07/2013", "04/07/2013", "-120,50", "", "test de libellé 2", "1300,20"),
			array("05/07/2013", "04/07/2013", "-120,50", "", "", "1300,20"),
			array("05/07/2013", "04/07/2013", "-120,50", "", "", "1300,20")
			);
		
		$handle = fopen($name, 'w+');
		
		foreach($mydata as $data) {
			fputcsv($handle, $data, ";");
		}
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 1;
		$data->prepare_csv_data();
		$data->import_as_cic();
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 1,
				'amount_inc_vat' => "152.2000000",
				'banks_id' => 1,
				'comment' => "test de libellé",
				'day' => mktime(0, 0, 0, 7, 1, 2013)
			)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 2,
				'amount_inc_vat' => "-120.5000000",
				'banks_id' => 1,
				'comment' => "test de libellé 2",
				'day' => mktime(0, 0, 0, 7, 4, 2013)
			)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 3,
				'amount_inc_vat' => "-120.5000000",
				'banks_id' => 1,
				'comment' => "",
				'day' => mktime(0, 0, 0, 7, 4, 2013)
			)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 4,
				'amount_inc_vat' => "-120.5000000",
				'banks_id' => 1,
				'comment' => "",
				'day' => mktime(0, 0, 0, 7, 4, 2013)
			)
		);
		$data = new Writings_Data_File($name);
		$data->banks_id = 1;
		$data->prepare_csv_data();
		$data->import_as_cic();
		
		fclose($handle);
		unlink($name);
		$writing = new Writing();
		$this->assertFalse($writing->load(5));
		$this->truncateTable("writings");
		$this->truncateTable("writingsimported");
	}
	
	function test_import_as_coop() {
		$name = tempnam('/tmp', 'csv');
		
		$mydata = array(
			array("Date","Libellé","Libellé complémentaire","Montant","Sens","Numéro de chèque","Référence Interne de l'Opération",
				"Nom de l'émetteur","Identifiant de l'émetteur","Nom du destinataire","Identifiant du destinataire",
				"Nom du tiers débiteur","Identifiant du tiers débiteur","Nom du tiers créancier","Identifiant du tiers créancier",
				"Libellé de Client à Client - Motif","Référence de Client à Client","Référence de la Remise","Référence de la Transaction",
				"Référence Unique du Mandat","Séquence de Présentation"),
			array("02/07/2013", "libellé 1", " libellé complémentaire 1", "152,20", "DEBIT", "Numéro de chèque 1","Référence Interne de l'Opération 1",
				"Nom de l'émetteur 1","Identifiant de l'émetteur 1","Nom du destinataire 1","Identifiant du destinataire 1",
				"Nom du tiers débiteur 1","Identifiant du tiers débiteur 1","Nom du tiers créancier 1","Identifiant du tiers créancier 1",
				"Libellé de Client à Client - Motif 1","Référence de Client à Client 1","Référence de la Remise 1","Référence de la Transaction 1",
				"Référence Unique du Mandat 1","Séquence de Présentation 1"),
			array("03/07/2013", "", "", "152,20", "CREDIT"),
			array("03/07/2013", "", "", "152,20", "CREDIT")
			);
		
		$handle = fopen($name, 'w+');
		
		foreach($mydata as $data) {
			fputcsv($handle, $data, ";");
		}
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 2;
		$data->prepare_csv_data();
		$data->import_as_coop();
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 1,
				'amount_inc_vat' => "-152.200000",
				'banks_id' => 2,
				'comment' => "libellé 1",
				'day' => mktime(0, 0, 0, 7, 2, 2013),
				'information' => "LibellÃ© complÃ©mentaire : libellÃ© complÃ©mentaire 1
NumÃ©ro de chÃ¨que : NumÃ©ro de chÃ¨que 1
RÃ©fÃ©rence Interne de l'OpÃ©ration : RÃ©fÃ©rence Interne de l'OpÃ©ration 1
Nom de l'Ã©metteur : Nom de l'Ã©metteur 1
Identifiant de l'Ã©metteur : Identifiant de l'Ã©metteur 1
Nom du destinataire : Nom du destinataire 1
Identifiant du destinataire : Identifiant du destinataire 1
Nom du tiers dÃ©biteur : Nom du tiers dÃ©biteur 1
Identifiant du tiers dÃ©biteur : Identifiant du tiers dÃ©biteur 1
Nom du tiers crÃ©ancier : Nom du tiers crÃ©ancier 1
Identifiant du tiers crÃ©ancier : Identifiant du tiers crÃ©ancier 1
LibellÃ© de Client Ã  Client - Motif : LibellÃ© de Client Ã  Client - Motif 1
RÃ©fÃ©rence de Client Ã  Client : RÃ©fÃ©rence de Client Ã  Client 1
RÃ©fÃ©rence de la Remise : RÃ©fÃ©rence de la Remise 1
RÃ©fÃ©rence de la Transaction : RÃ©fÃ©rence de la Transaction 1
RÃ©fÃ©rence Unique du Mandat : RÃ©fÃ©rence Unique du Mandat 1
SÃ©quence de PrÃ©sentation : SÃ©quence de PrÃ©sentation 1
"
				)
		);
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 2,
				'amount_inc_vat' => "152.200000",
				'banks_id' => 2,
				'day' => mktime(0, 0, 0, 7, 3, 2013),
				)
		);
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 3,
				'amount_inc_vat' => "152.200000",
				'banks_id' => 2,
				'day' => mktime(0, 0, 0, 7, 3, 2013),
				)
		);
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 2;
		$data->prepare_csv_data();
		$data->import_as_coop();
		fclose($handle);
		unlink($name);
		$writing = new Writing();
		$this->assertFalse($writing->load(4));
		$this->truncateTable("writings");
		$this->truncateTable("writingsimported");
	}
	
	function test_form_import_bank() {
		$data = new Writings_Data_File();
		$form_import = $data->form_import_bank("label");
		$this->assertPattern("/id=\"menu_actions_import_bank\"/", $form_import);
		$this->assertPattern("/label/", $form_import);
		$this->assertPattern("/menu_actions_import_file/", $form_import);
		$this->assertPattern("/menu_actions_import_submit/", $form_import);
		
		$this->truncateTable("banks");
	}
	
	function test_import_as_ofx() {
		$name = tempnam('/tmp', 'ofx');
		
		$content = "OFXHEADER:100
					DATA:OFXSGML
					VERSION:102
					SECURITY:NONE
					ENCODING:USASCII
					CHARSET:1252
					COMPRESSION:NONE
					OLDFILEUID:NONE
					NEWFILEUID:NONE
					<OFX>
						<SIGNONMSGSRSV1>
							<SONRS>
								<STATUS>
									<CODE>0
									<SEVERITY>INFO
								</STATUS>
								<DTSERVER>20131001151232
								<LANGUAGE>FRA
								<DTPROFUP>20131001151232
								<DTACCTUP>20131001151232
							</SONRS>
						</SIGNONMSGSRSV1>
					<BANKMSGSRSV1>
						<STMTTRNRS>
							<TRNUID>00
							<STATUS>
								<CODE>0
								<SEVERITY>INFO
							</STATUS>
							<STMTRS>
								<CURDEF>EUR
								<BANKACCTFROM>
									<BANKID>42
									<BRANCHID>000203215
									<ACCTID>33265666
									<ACCTTYPE>CHECKING
								</BANKACCTFROM>
								<BANKTRANLIST>
									<DTSTART>20130930010000
									<DTEND>20130930010000
									<STMTTRN>
										<TRNTYPE>DEBIT
										<DTPOSTED>20131004
										<DTUSER>20131004
										<TRNAMT>-35.00
										<FITID>2013093000001
										<NAME>CARTE TEST
										<MEMO>MEMO TEST
									</STMTTRN>
									<STMTTRN>
										<TRNTYPE>DEBIT
										<DTPOSTED>20131204
										<TRNAMT>-10.50
										<FITID>2013093000002
										<NAME>ABONNEMENT TEST
									</STMTTRN>
									<STMTTRN>
										<TRNTYPE>CREDIT
										<DTPOSTED>20131004
										<TRNAMT>+7.50
										<FITID>2013093000003
									</STMTTRN>
									<STMTTRN>
										<TRNAMT>+5
										<DTPOSTED>20131104
									</STMTTRN>
								</BANKTRANLIST>
								<LEDGERBAL>
									<BALAMT>+395.68
									<DTASOF>20130930010000
								</LEDGERBAL>
								<AVAILBAL>
									<BALAMT>+395.68
									<DTASOF>20130930010000
								</AVAILBAL>
								</STMTRS>
							</STMTTRNRS>
						</BANKMSGSRSV1>
					</OFX>";
		$handle = fopen($name, 'w+');
		fwrite($handle, $content);
		fclose($handle);
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 1;
		$data->import_as_ofx();
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 1,
				'amount_inc_vat' => "-35.000000",
				'amount_excl_vat' => "-35.000000",
				'vat' => "0",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 10, 4, 2013),
				'comment' => "CARTE TEST",
				'information' => "MEMO TEST"
				)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 2,
				'amount_inc_vat' => "-10.500000",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 12, 4, 2013),
				'comment' => "ABONNEMENT TEST",
				'information' => ""
				)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 3,
				'amount_inc_vat' => "7.500000",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 10, 4, 2013),
				'comment' => "",
				'information' => ""
				)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 4,
				'amount_inc_vat' => "5.00000",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 11, 4, 2013),
				'comment' => "",
				'information' => ""
				)
		);
		$writing = new Writing();
		$this->assertFalse($writing->load(5));
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 1;
		$data->import_as_ofx();
		
		$this->assertTrue($writing->load(4));
		$this->assertFalse($writing->load(5));
		
		$this->truncateTable("writings");
		$this->truncateTable("writingsimported");
	}
	
	function test_import_as_qif() {
		$name = tempnam('/tmp', 'qif');
		
		$content = "!Type:Bank
D29/10/13
T500.00
PVIR NO PARKING REFERENCE NON TRANSMISE
^
D05/11/13
T-10.60
PSNCF CARTE 21542154 PAIEMENT CB 0411 AMIENS
^
D06/11/13
T-24.00
PFLIB TRAVEL INTE CARTE 21542154 PAIEMENT CB 0511 LU BASCHARAGE
^
D06/11/13
T-20.00
PRETRAIT DAB 0411 BEAUVAIS AERO CRCA BRIE PICARD CARTE 21542154
MAUTRE CHAMPS INCONNU
LENCORE UN CHAMP INCONNU
^
";
		$handle = fopen($name, 'w+');
		fwrite($handle, $content);
		fclose($handle);
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 1;
		$data->import_as_qif();
		
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 1,
				'amount_inc_vat' => "500.000000",
				'amount_excl_vat' => "500.000000",
				'vat' => "0",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 10, 29, 2013),
				'comment' => "VIR NO PARKING REFERENCE NON TRANSMISE",
				)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 2,
				'amount_inc_vat' => "-10.600000",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 11, 5, 2013),
				'comment' => "SNCF CARTE 21542154 PAIEMENT CB 0411 AMIENS",
				)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 3,
				'amount_inc_vat' => "-24.00000",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 11, 6, 2013),
				'comment' => "FLIB TRAVEL INTE CARTE 21542154 PAIEMENT CB 0511 LU BASCHARAGE",
				)
		);
		$this->assertRecordExists(
			"writings",
			array(
				'id' => 4,
				'amount_inc_vat' => "-20.00000",
				'banks_id' => 1,
				'day' => mktime(0, 0, 0, 11, 6, 2013),
				'comment' => "RETRAIT DAB 0411 BEAUVAIS AERO CRCA BRIE PICARD CARTE 21542154",
				'information' => "AUTRE CHAMPS INCONNU
ENCORE UN CHAMP INCONNU
"
				)
		);
		$writing = new Writing();
		$this->assertFalse($writing->load(5));
		
		$data = new Writings_Data_File($name);
		$data->banks_id = 1;
		$data->import_as_qif();
		
		$this->assertTrue($writing->load(4));
		$this->assertFalse($writing->load(5));
		
		$this->truncateTable("writings");
		$this->truncateTable("writingsimported");
	}
}
