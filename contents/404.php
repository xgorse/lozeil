<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$heading = new Heading_Area($status_404);
echo $heading->show();

$working = __('sorry, page not found');
$working .= "<br />";
$working .= "<br />";
if (isset($_SERVER['HTTP_REFERER'])) {
	$working .= "<a href=\"".$_SERVER['HTTP_REFERER']."\">&laquo; ".__('back')."</a>";
}
if ($GLOBALS['config']['error_handling']) {
	error_handling("E_404", __('sorry, page not found'), $_GET['content'], "", "");
}

echo $working;
