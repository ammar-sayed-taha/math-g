<?php
	ob_start();
	session_start();
	$pageTitle = 'Logout';
	include_once "init.php";  //include the initialize file

	session_unset();
	session_destroy();
	header('Location:index.php');

	exit();

	ob_end_flush();