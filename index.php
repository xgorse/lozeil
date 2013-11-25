<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

require dirname(__FILE__)."/inc/require.inc.php";

$application = new Application();
$application->boot();

$global_status = false;

if ($GLOBALS['config']['db_profiler']) {
	$dbInst = new db_perf();
} else {
	$dbInst = new db();
}

$timer = new Benchmark_Timer;
$timer->start();

$content_object = new Content();

if (isset($_POST['username']) and $_POST['username'] != '') {
	$auth = new User_Authentication();
	if ($auth->is_authorized($_POST['username'], $_POST['password'])) {
		$_SESSION += $auth->session_headers();
		$authenticated_user = new User_Authenticated($_SESSION['userid']);
		$content_object->user($authenticated_user);
	}
} else {
	$authenticated_user = new User_Authenticated();
}

if (isset($_SESSION['username']) and $_SESSION['username']) {
	
	if (isset($_GET['content']) and !empty($_GET['content']) and $_GET['content'] != 'login.php') {
		$content_object->filename($_GET['content']);
	} else {
		$content_object->filename($authenticated_user->defaultpage());
	}
	$content = $content_object->filename();
	$content_included = $content_object->pathname();
	
	$location = clean_location($_SERVER['PHP_SELF']);
	if (!isset($_REQUEST['method']) and !preg_match("/ajax/", $content) and !preg_match("/export/", $content)) {
		$theme = new Theme_Default();

		echo $theme->html_top();
		echo $theme->head();
		echo $theme->body_top($location, $content);

		echo $theme->content_top();

		include($content_included);

		echo $theme->content_bottom();
		echo $theme->show_status();

		echo $theme->body_bottom();
		echo $theme->html_bottom();
	} else {
		include($content_included);
	}
} else {
	$location = clean_location($_SERVER['PHP_SELF']);
	if (isset($_GET['content']) and $_GET['content'] == "passwordrequest.php") {
		$content_object->filename($_GET['content']);
	} else {
		$content_object->filename_login();
	}
	
	$content = $content_object->filename();
	$content_included = $content_object->pathname();
	
	$theme = new Theme_Default();
	echo $theme->html_top();
	echo $theme->head();
	echo $theme->body_top($location, $content);

	echo $theme->content_top();
	include($content_included);
	echo $theme->content_bottom();
	echo $theme->show_status();
	echo $theme->body_bottom();
	echo $theme->html_bottom();
}

$timer->stop();
register_shutdown_function(array($application, "shutdown"));

