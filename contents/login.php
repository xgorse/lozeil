<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_SESSION)) {
	session_destroy();
}
$auth = new User_Authentication();

$working = $auth->form();
$working .= "<div id=\"form_login\">".$html."</div>";
$area = new Working_Area($working);
echo $area->show();
