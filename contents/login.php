<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

if (isset($_SESSION)) {
	session_destroy();
}
$auth = new User_Authentication();
$html = $auth->form();

echo "<div id=\"form_login\">".$html.show_status()."</div>";
