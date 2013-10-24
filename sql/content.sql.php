<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$queries = array(
	'categories' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_categories']." (
		  id INT(21) NOT NULL AUTO_INCREMENT,
		  name VARCHAR(100) NOT NULL DEFAULT '',
		  vat DECIMAL(5,2) NOT NULL DEFAULT 0,
		  vat_category TINYINT(1) NOT NULL DEFAULT 0,
		  PRIMARY KEY (`id`)
		 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'sources' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_sources']." (
		  id INT(21) NOT NULL AUTO_INCREMENT,
		  name VARCHAR(100) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'banks' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_banks']." (
		  id INT(21) NOT NULL AUTO_INCREMENT,
		  name VARCHAR(100) NOT NULL DEFAULT '',
		  selected TINYINT(1) NOT NULL DEFAULT 0,
		  PRIMARY KEY (`id`)
		 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
		"INSERT INTO ".$GLOBALS['dbconfig']['table_banks']." VALUES (1, 'cic', 1);",
		"INSERT INTO ".$GLOBALS['dbconfig']['table_banks']." VALUES (2, 'coop', 1);",

	'writings' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_writings']." (
		  id INT(21) NOT NULL AUTO_INCREMENT,
		  categories_id INT(11),
		  amount_excl_vat DECIMAL(12,6),
		  amount_inc_vat DECIMAL(12,6),
		  banks_id INT(11),
		  comment TEXT NOT NULL,
		  day INT(10) NOT NULL DEFAULT '0',
		  information TEXT NOT NULL,
		  paid TINYINT(1) NOT NULL DEFAULT '0',
		  search_index TEXT NOT NULL,
		  sources_id INT(11),
		  simulations_id INT(11),
		  number VARCHAR(100),
		  vat DECIMAL(5,2),
		  accountingcodes_id INT(11),
		  attachment TINYINT(1) NOT NULL DEFAULT 0,
		  timestamp INT(10),
		  PRIMARY KEY (`id`),
		  KEY categories_id (categories_id),
		  KEY sources_id (sources_id),
		  KEY simulations_id (simulations_id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'users' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_users']." (
		  id INT(11) NOT NULL AUTO_INCREMENT,
		  name VARCHAR(250),
		  username VARCHAR(80) NOT NULL DEFAULT '',
		  password VARCHAR(50) NOT NULL DEFAULT '',
		  email VARCHAR(250),
		  PRIMARY KEY (`id`),
		  UNIQUE KEY username (username)
		 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'writingssimulations' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_writingssimulations']." (
		`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(100) NOT NULL DEFAULT '',
		`amount_inc_vat` DECIMAL(12,6),
		`periodicity` VARCHAR(50) NOT NULL DEFAULT '',
		`date_start` INT(10) NOT NULL,
		`date_stop` INT(10) NOT NULL,
		`display` TINYINT(1) NOT NULL,
		`timestamp` INT(10),
		`evolution` VARCHAR(100),
		PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'writingsimported' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_writingsimported']." (
		`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
		`hash` VARCHAR(100),
		`banks_id` INT(11),
		`sources_id` INT(11),
		PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'accountingcodes' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_accountingcodes']." (
		`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(255),
		`number` VARCHAR(100),
		PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'bayesianelements' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_bayesianelements']." (
		`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
		`element` VARCHAR(100),
		`field` VARCHAR(100),
		`table_name` VARCHAR(100),
		`table_id` INT(11),
		`occurrences` INT(11),
		PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
	
	'files' => "CREATE TABLE ".$GLOBALS['dbconfig']['table_files']." (
		`id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
		`writings_id` INT(11),
		`hash` VARCHAR(100),
		`value` VARCHAR(255),
		PRIMARY KEY (`id`)
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);
