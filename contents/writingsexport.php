<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$writings_export = new Writings_Export();
$writings_export->clean_and_set($_POST);
$writings_export->export();

header("Location: ".link_content("content=writings.php"));
exit;
