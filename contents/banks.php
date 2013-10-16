<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the banks')));
echo $heading->show();

$banks = new Banks();
$banks->select();
echo $banks->show_form();