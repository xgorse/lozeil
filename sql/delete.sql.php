<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$db = new db();
$tables = array();
foreach ($GLOBALS['dbconfig'] as $parameter => $table) {
	if (substr($parameter, 0, 6) == 'table_') {
		$tables[] = $table;
	}
}
$db->query("DROP TABLE IF EXISTS ".join(", ", $tables));
