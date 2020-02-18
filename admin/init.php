<?php
	ini_set('display_errors', 'on');
	error_reporting(E_ALL);
	
	// phpinfo();
	
	//set the default time zone
	date_default_timezone_set('Africa/cairo');
	//connect with the database
	include_once "../DB/connectDB.php";

	$tpt_path = "includes/template/";
	$lang_path = "includes/languages/";
	$func		 = "includes/functions/";
	$css_path = "layout/css/";
	$js_path = "layout/js/";
	


	//include the important files
	include_once $func . 'functions.php';
	include_once $lang_path . "english.php";
	include_once $tpt_path . "header.php";

	 if(!isset($Nonavbar)) include_once $tpt_path . "navbar.php";  //don't show navbar in index page
