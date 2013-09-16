<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$category = new Category();
$category->load($_REQUEST['value']);

echo $category->vat;
exit;