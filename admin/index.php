<?php 
	ob_start();
	session_start();
	$Nonavbar = '';
	$pageTitle = 'Login';
	
	if(isset($_SESSION['username'])){
		header('location:dashboard.php');
		exit();
	}

	include_once "init.php";  //include the initialize file

	//define global variables
	$notAdmin = '';  //diplay in form tag

	//check the request method
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$username = $_POST['user'];
		$password = $_POST['pass'];

		$admins = selectItems('id, username, password, admin', 'users', 'username = ? AND  admin in(1,2)', array($username)); //open account for admin or supervisor

		if(!empty($admins)){
			foreach ($admins as $admin) {
			  //encode the password
			if(password_verify($password, $admin['password'])){

				$_SESSION['username'] = subString($admin['FullName'], ' ');
				$_SESSION['userid'] = $admin['id']; 
				$_SESSION['admin'] = $admin['admin']; 
				header('location:dashboard.php');
				exit();

			}else{
				$notAdmin = @lang('WRONGLOGIN');
			}
		}
		}else{
			$notAdmin = @lang('WRONGLOGIN');
		}


	}

?>


<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-offset-3 col-sm-6 col-md-offset-4 col-md-4">
			<form class="login form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<?php if(!empty($notAdmin)) echo '<div class = "error-msg">'. $notAdmin . '</div>'; ?>
				
				<h4 class="text-center">Admin Login</h4>
				<input class="form-control input-lg" type="text" name="user" placeholder="username" >
				<input class="form-control input-lg" type="password" name="pass" placeholder="password" autocomplete="new-password">
				<input class="btn btn-block btn-primary btn-lg" type="submit" value="Login">

			</form>
		</div>
	</div>
</div>
	


<?php 
	include_once $tpt_path ."footer.php";
	ob_end_flush();
?>