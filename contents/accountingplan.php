<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$menu = Plugins::factory("Menu_Area");
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage accounting plan')));
echo $heading->show();

$accounting_codes = new Accounting_Codes();
$accounting_codes->select();

$working = $accounting_codes->display();
$area = new Working_Area($working);
echo $area->show();
