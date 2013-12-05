<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$menu = Plugins::factory("Menu_Area");
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the banks')));
echo $heading->show();

$banks = new Banks();
$banks->select();

$working = $banks->show_form();
$area = new Working_Area($working);
echo $area->show();
