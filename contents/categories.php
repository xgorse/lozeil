<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('manage the categories')));
echo $heading->show();

$categories = new Categories();
$categories->select();
echo $categories->show_form();
