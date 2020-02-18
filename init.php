<?php
	ob_start();

	session_regenerate_id(); //change the id of the session for more security

	ini_set('display_errors', 'on');  //set it to off after finishing
	error_reporting(E_ALL);
	
	// phpinfo();


	date_default_timezone_set('Africa/cairo'); //set default timezone

	//connect with the database
	include_once "DB/connectDB.php";


	$tpt_path = "includes/template/";
	$lang_path = "includes/languages/";
	$func		 = "includes/functions/";

	$admin_css_path = "admin/layout/css/";
	$css_path = "layout/css/";
	
	$admin_js_path = "admin/layout/js/";
	$js_path = "layout/js/";

	/* Start Language Section */

	//if the user changed the language then change it here too
	if(isset($_GET['changeLang'])){
		if($_GET['changeLang'] == 'english')
			setcookie('lang', 'english.php', time() + 31104000, '/'); //set it for one year

		elseif($_GET['changeLang'] == 'arabic')
			setcookie('lang', 'arabic.php', time() + 31104000, '/'); //set it for one year
		header('location:index.php');
		exit();
	}
	$lang = isset($_COOKIE['lang'])  && !empty($_COOKIE['lang']) ? $_COOKIE['lang'] : 'arabic.php';

	/* End Language Section */

	include_once $lang_path . $lang; //make it top because i call it in some functions in functions.php
	include_once $func . 'functions.php';

	/* Start Login Section */

	//check if the user has stored account in cookies
	//these names of cookies just for make the hacker doesn't know the username or password
	if((isset($_COOKIE['playNow']) && !empty($_COOKIE['playNow'])) ){
		//get the rememberMe colomn from the database
		$login = selectItems('id, username', 'users', 'rememberMe = ?', array($_COOKIE['playNow']));

		if(!empty($login)){
			//get the playNow of the account stored in cookies
			$_SESSION['user'] 	= @$login[0]['username'];
			$_SESSION['uid'] 	= @$login[0]['id'];
		}
		
	}
	/* End Login Section */

	if(! isset($noHeader)){
		include_once $tpt_path . "header.php";
	}


	ob_end_flush();