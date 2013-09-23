<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$timestamp_selected = determine_integer_from_post_get_session(null, "start");

$menu = new Menu_Area();
$menu->prepare_navigation(__FILE__);
echo $menu->show();

$heading = new Heading_Area(utf8_ucfirst(__('consult statistics')));
echo $heading->show();

$followupwritings = new Writings_Followup();
echo $followupwritings->show_timeseries_at($timestamp_selected);
