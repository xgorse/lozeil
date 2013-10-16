<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the sources')));
echo $heading->show();

$sources = new Sources();
$sources->select();
echo $sources->show_form();