<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

session_destroy();
header ("Location: ".link_content("content=login.php"));

exit(0);
