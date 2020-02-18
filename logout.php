<?php
	ob_start();
	session_start();
	$pageTitle = 'Logout';
	include_once "init.php";  //include the initialize file

	session_unset();
	session_destroy();

	//remove the cookies
	setcookie('playNow', '', time() - 31104000, '/'); //set it for one year
	//empty the rememberMe colomn from the database
	updateItems('users', 'rememberMe = ?', array(''));

	header('Location:login.php');
	exit();

	ob_end_flush();
?>