<?php 
	ob_start();
	session_start();
	$pageTitle = 'login/Signup';

	if(isset($_SESSION['user'])){
		header('location:index.php');
		exit();
	}
	include_once "init.php";  //include the initialize file

	// Check If The User Came By Post Request
	if($_SERVER['REQUEST_METHOD'] === 'POST'){

		// Check if the user entered login form
		if(isset($_POST['login'])){			//the user clicked on Login btn

			$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
			$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
			// $hashedPass = password_hash($password, PASSWORD_DEFAULT);

			if(empty($username)) 		$formError[] = @lang('EMPTY_USERNAME');
			if(empty($password)) 		$formError[] = @lang('EMPTY_PASS');
			if(strlen($username) < 3) 	$formError[] = @lang('USER_LESS_3_CHARS');
			if(strlen($password) < 5) 	$formError[] = @lang('PASS_LESS_5_CHARS');

			if(empty($formError)){

				$allUsers = selectItems('id, username, password', 'users', 'username = ?', array($username));
			
				if(!empty($allUsers)){

					//Make sure The Password matches
					if(password_verify($password, $allUsers[0]['password'])){
						$_SESSION['user'] 	= $username;
						$_SESSION['uid'] 	= $allUsers[0]['id'];

						//if the user clciked on remember me then store it in cookies
						if(isset($_POST['remember']) && $_POST['remember'] == 'on'){
							//these names of cookies just for make the hacker doesn't know the username or password
							$encUsername = encFileName(''); //create random name to save it in cookies and in database 
							setcookie('playNow', $encUsername, time() + 31104000, '/'); //set it for one year
						
							//save the encUsername in the database
							updateItems('users', 'rememberMe = ?', array($encUsername)); //save the random value to remember the user when login next time
						}

						header('location:index.php');
						exit();
					}else{
						$formError[] = @lang('PASS_NOT_RIGHT');
					}
					
				}else{
					$formError[] = @lang('USER_NOT_RIGTH');
				}
			}

			

			
		}elseif(isset($_POST['signup'])){	//the user clicked on signup btn

			$user 		= filter_var($_POST['username'], FILTER_SANITIZE_STRING);
			$pass 		= filter_var($_POST['password'], FILTER_SANITIZE_STRING);
			$hashedPass = password_hash($pass, PASSWORD_DEFAULT);
			$fullname 	= filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);

			if(empty($user))			$formError[] = @lang('EMPTY_USERNAME');
			if(empty($pass))			$formError[] = @lang('EMPTY_PASS');
			if(empty($fullname))		$formError[] = @lang('EMPTY_F_NAME');
			if(strlen($user) < 3) 		$formError[] = @lang('USER_LESS_3_CHARS');
			if(strlen($pass) < 5) 		$formError[] = @lang('PASS_LESS_5_CHARS');
			if(! @checkChars($user))  	$formError[] = @lang('INVALID_CHARS');  //check if the characters are allowed
			if(! @checkChars($pass))  	$formError[] = @lang('INVALID_CHARS');  //check if the characters are allowed
			// if(! @checkChars($fullname)) $formError[] = @lang('INVALID_CHARS');  //check if the characters are allowed


			if(empty($formError)){

				$allUsers = selectItems('username', 'users', 'username = ?', array($user));
			
				if(!empty($allUsers)){
					$formError[] = @lang('EXIST_USERNAME');
				}else{

					$addUser = insertItems('users', "username, password, FullName, `Date`",'?, ?, ?, NOW()', 
									array($user, $hashedPass, $fullname) );
					if($addUser > 0){
						$newUser = selectItems('id', 'users', 'username = ?', array($user));

						$_SESSION['user'] = $user;
						$_SESSION['uid'] = $newUser[0]['id'];

						header('location:index.php');
						exit();
					}else{
						$formError[] = @lang('ERR_SIGNUP');
					}
				}
			}

			$userSignup = $user; 	//copy the user for display it on signup form to display
			$passSignup = $pass;  	//copy the pass for display it on signup form to display
		
		}
	}

?>


	<!-- just to change the title tag content with this -->
	<span id="pageTitle" hidden=""><?php echo @lang('LOGIN_SIGNUP'); ?></span>

	<section class="login-page">
		<div class="container">

			<h1 class="text-center log-regist">
				<span class="active" data-class="login" data-color="#337ab7"><?php echo @lang('LOGIN'); ?></span> |
				<span data-class="signup" data-color="#5cb85c"><?php echo @lang('SIGNUP'); ?></span>
			</h1>
			<!-- Error List -->
				<div class="errors">
					<!-- display the error or success messages -->
					<?php displayMsg(@$formError); ?>
				</div>
				<!-- Start Login Form -->

			<div class="login-container">

				<div class="login">
					<form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
						<div class="required">
							<div class="custom-input">
								<input 
									class 			= "form-control" 
									type 			= "text" 
									name 			= "username" 
									required 		= "required"
									autocomplete 	= "off"
									placeholder 	= "<?php  echo @lang('LOGIN_PLACE_UNAME') ?>"
									<?php if(isset($username)) {echo 'value="' . $username . '"';} ?>
								>
								<span class="style"></span>
							</div>
							<div class="errors">
								<span class="errorMsg"><?php echo @lang('USER_LESS_3_CHARS') ?></span>
							</div>
						</div>

						<div class="required">
							<div class="custom-input">
								<input 
									class 			= "form-control" 
									type 			= "password" 
									name 			= "password" 
									required 		= "required"
									autocomplete 	= "new-password"
									placeholder 	= "<?php  echo @lang('LOGIN_PLACE_PASS') ?>"
									<?php if(isset($password)) {echo 'value="' . $password . '"';} ?>
								>
								<span class="style"></span>
							</div>
							<div class="errors">
								<span class="errorMsg"><?php echo @lang('PASS_LESS_5_CHARS') ?></span>
							</div>
						</div>
						<div class="remember">
							<input id="rem" type="checkbox" name="remember">
							<label for="rem"><?php echo @lang('REMEMBER_ME') ?></label>
							
						</div>
						<input class="btn btn-primary btn-block" name="login" type="submit" value="<?php echo @lang('LOGIN') ?>">
						

					</form>
				</div>
				<!-- End Login Form -->

				<!-- Start Register Form -->
				<div class="signup">
					<form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
						<div class="required">
							<div class="custom-input">
								<input 
									class 			= "form-control" 
									type 			= "text" 
									name 			= "username" 
									required 		= "required"
									placeholder 	= "<?php  echo @lang('LOGIN_PLACE_UNAME') ?>"
									<?php if(isset($userSignup)) {echo 'value="' . @$userSignup . '"';} ?>
								>
								<span class="style"></span>
							</div>
							<div class="errors">
								<span class="errorMsg"><?php echo @lang('USER_LESS_3_CHARS') ?></span>
							</div>
						</div>
						<div class="required">
							<div class="custom-input">
								<input 
									class 			= "form-control" 
									type 			= "password" 
									name 			= "password" 
									required 	  	= "required" 
									placeholder 	= "<?php  echo @lang('LOGIN_PLACE_PASS') ?>" 
									autocomplete 	= "new-password"
									<?php if(isset($passSignup)) {echo 'value="' . $passSignup . '"';} ?>
								>
								<span class="style"></span>
							</div>
							<div class="errors">
								<span class="errorMsg"><?php echo @lang('PASS_LESS_5_CHARS') ?></span>
							</div>
						</div>
						<div class="required">
							<div class="custom-input">
								<input 
									class 			= "form-control" 
									type 			= "text" 
									name 			= "fullname" 
									required 		= "required"
									placeholder 	= "<?php echo @lang('PLACE_FULLNAME') ?>"
									<?php if(isset($fullname)) {echo 'value="' . $fullname . '"';} ?>
								>
								<span class="style"></span>
							</div>
							<div class="errors">
								<span class="errorMsg"><?php echo@lang('EMPTY_F_NAME') ?></span>
							</div>
						</div>
						<input class="btn btn-success btn-block" name="signup" type="submit" value="<?php echo @lang('SIGNUP') ?>">
						

					</form>
				</div>
				<!-- End Register Form -->
			</div>

		</div>
	</section>



<?php 
	include_once $tpt_path ."footer.php"; 
	ob_end_flush();
?>