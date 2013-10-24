<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

class Update {
	public $config;
	public $param;
	
	function __construct(db $db = null) {
		if ($db === null) {
			$db = new db();
		}
		$this->db = $db;
		$this->config = new Config_File(dirname(__FILE__)."/../cfg/config.inc.php", "config");
		$this->param = new Param_File(dirname(__FILE__)."/../cfg/param.inc.php", "param");
		$this->dbconfig = new Config_File(dirname(__FILE__)."/../cfg/config.inc.php", "dbconfig");
	}
	
	function to_23() {
		$this->db->query("ALTER TABLE `users` ADD `name` varchar(250) AFTER `id`;");
		$this->db->query("ALTER TABLE `users` ADD `email` varchar(250) AFTER `password`;");
	}
	
	function to_22() {
		$this->param->add("locale_timezone", "Europe/Paris");
		$this->param->add("email_from", "lozeil@noparking.net");
		$this->param->add("email_wrap", "50");
	}
	
	function to_21() {
		$this->db->query("ALTER TABLE `categories` ADD `vat_category` TINYINT(1)  NOT NULL  DEFAULT 0  AFTER `vat`;");
	}
	
	function to_20() {
		$this->param->add("fisher_threshold", "0.4");
		$this->param->update(array(
				'param' => array(
					'param' => array(
						"amount_inc_vat_weight" => "0.3"
					)
				)
		));
	}
	
	function to_19() {
		$this->dbconfig->add("table_files", "files");
		$this->db->query("ALTER TABLE `writings` ADD `attachment` TINYINT(1)  NOT NULL  DEFAULT 0  AFTER `accountingcodes_id`;");
		$this->db->query("CREATE TABLE `files` (
			`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
			`writings_id` INT(11),
			`hash` VARCHAR(100),
			`value` VARCHAR(255),
			PRIMARY KEY (`id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	}
	
	function to_18() {
		$this->param->add("threshold", 3);
	}
	
	function to_17() {
		$this->param->add("comment_weight", 1);
		$this->param->add("amount_inc_vat_weight", 3);
		$this->dbconfig->add("table_bayesianelements", "bayesianelements");
		$this->db->query("CREATE TABLE `bayesianelements` (
			`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
			`element` VARCHAR(100),
			`field` VARCHAR(100),
			`table_name` VARCHAR(100),
			`table_id` INT(11),
			`occurrences` INT(11),
			PRIMARY KEY (`id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
		);
	}
	
	function to_16() {
		$this->dbconfig->add("table_accountingcodes", "accountingcodes");
		$this->db->query("ALTER TABLE `writings` ADD `accountingcodes_id` INT(11);");
		$this->db->query("CREATE TABLE `accountingcodes` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(255),
			`number` varchar(100),
			PRIMARY KEY (`id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
		);
	}
	
	function to_15() {
		$this->param->add("nb_max_writings", "100");
	}
	
	function to_14() {
		$this->db->query("ALTER TABLE `writings` DROP unique_key;");
		$this->db->query("CREATE TABLE `writingsimported` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`hash` varchar(100),
			`banks_id` int(11),
			`sources_id` int(11),
			PRIMARY KEY (`id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
		);
	}
	
	function to_13() {
		$this->db->query("ALTER TABLE `writingssimulations` ADD `evolution` VARCHAR(100);");
	}
	
	function to_12() {
		$this->db->query("ALTER TABLE `writings` CHANGE `number` `number` VARCHAR(100);");
	}
	
	function to_11() {
		$this->db->query("ALTER TABLE `writingssimulations` ADD `timestamp` INT(10)  NULL  DEFAULT NULL  AFTER `display`;");
	}
	
	function to_10() {
		$this->db->query("ALTER TABLE `categories` ADD `vat` DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER `name`;");
	}
	
	function to_9() {
		$this->db->query("ALTER TABLE `writings` ADD `timestamp` INT(10)  NULL  DEFAULT NULL  AFTER `vat`;");
	}
	
	function to_8() {
		$this->db->query("ALTER TABLE `writingssimulations` DROP `duration`;");
		$this->db->query("ALTER TABLE `writingssimulations` ADD `date_start` INT(10)  NOT NULL AFTER `periodicity`;");
		$this->db->query("ALTER TABLE `writingssimulations` ADD `amount_inc_vat` DECIMAL(12,6)  NOT NULL AFTER `name`;");
		$this->db->query("ALTER TABLE `writingssimulations` CHANGE `date` `date_stop` INT(10)  NOT NULL;");
	}
	
	function to_7() {
		$this->db->query("ALTER TABLE `banks` ADD `selected` TINYINT(1)  NOT NULL DEFAULT 0  AFTER `name`;");
	}
	
	function to_6() {
		$this->db->query("DROP TABLE `types`;");
		$this->db->query("ALTER TABLE `writings` CHANGE `types_id` `number` INT(20)  NULL  DEFAULT NULL;");
	}

	function to_5() {
		$this->dbconfig->add("table_writingssimulations", "writingssimulations");
		$this->db->query("CREATE TABLE `writingssimulations` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL DEFAULT '',
			`duration` varchar(50) NOT NULL DEFAULT '',
			`periodicity` varchar(50) NOT NULL DEFAULT '',
			`date` int(10) NOT NULL,
			`display` tinyint(1) NOT NULL,
			PRIMARY KEY (`id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
		);
		$this->db->query("ALTER TABLE `writings` ADD `simulations_id` INT(11)  NULL  DEFAULT NULL  AFTER `vat`;");
	}

	function to_4() {
		$this->dbconfig->add("table_users", "users");
		$this->db->query("CREATE TABLE `users` (`id` int(11) NOT NULL AUTO_INCREMENT,`username` varchar(80) NOT NULL DEFAULT '',`password` varchar(50) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`),
			UNIQUE KEY `username` (`username`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
		);
	}
	
	function to_3() {
		$this->dbconfig->add("table_categories", "categories");
		$this->db->query("ALTER TABLE `writings` CHANGE `account_id` `categories_id` INT(11)  NULL  DEFAULT NULL;");
		$this->db->query("ALTER TABLE `writings` CHANGE `source_id` `sources_id` INT(11)  NULL  DEFAULT NULL;");
		$this->db->query("ALTER TABLE `writings` CHANGE `type_id` `types_id` INT(11)  NULL  DEFAULT NULL;");
		$this->db->query("ALTER TABLE `writings` CHANGE `bank_id` `banks_id` INT(11)  NULL  DEFAULT NULL;");
		$this->db->query("RENAME TABLE `accounts` TO `categories`;");
	}
	
	function to_2() {
		$this->db->query("ALTER TABLE `writings` CHANGE `delay` `day` INT(10)  NOT NULL DEFAULT '0';");
	}

	function to_1() {
		$this->config->add("version", 0);
	}
	
	function current() {
		$values = $this->config->values();
		return $values['config']['version'];
	}

	function last() {
		$last = 0;
		$methods = get_class_methods($this);
		foreach ($methods as $method) {
			if (preg_match("/^to_[0-9]*$/", $method)) {
				$last = max($last, (int)substr($method, 3));
			}
		}
		return $last;
	}

	function config($key, $value) {
		$values = array('config' => $this->config->values());
		$values['config']['config'][$key] = $value;
		
		return $this->config->update($values);
	}
}
