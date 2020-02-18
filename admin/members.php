<?php
	session_start();
	$pageTitle = 'Members';

	if(isset($_SESSION['username']) && $_SESSION['admin'] == 1) {
		include_once "init.php";  //include the initialize file
		
		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		if($do == 'Manage'){  //manage page

			/*
			** When the user click on delete member then do this statement
			*/
			if(isset($_GET['delete']) && $_GET['delete'] == 'delete'){
				$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

				$stmt = $con->prepare('DELETE FROM users WHERE id = ' . $userid);
				$stmt->execute();
				$stmt->rowCount() > 0 ? $successMsg = @lang('MEM_DELETED') : $formErrors[] = @lang('NO_CHANGE');
			}

			/*
			** Sort the table based on what the admin choosed 
			*/
			$sortBy = 'id DESC';
			if(isset($_GET['sort']) && !empty($_GET['sort'])){
				$sortArr = array('id DESC', 'image DESC', 'username ASC', 'Email ASC', 'FullName ASC', 'Date DESC');
				if(in_array($_GET['sort'], $sortArr)) $sortBy = $_GET['sort'];
			}

			/* 
			** Checking If The Admin Clicked To Show Only One Member From Dashboard
			** then the table will show only this member who the admin clicked on it to show more detail about him
			*/
			if(isset($_GET['single']) && $_GET['single'] == 'true'){
				$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
				$users = selectItems('*', 'users', 'id = ?', array($userid));
			}else{
				$users = selectItems('*', 'users', 1, array(), $sortBy);
			}
			
	?>
			
			<!-- just to change the title tag content with this -->
			<span id="pageTitle" hidden=""><?php echo @lang('MEM_MANAGE_P'); ?></span>
			
			<!-- design the table which contains  the members -->
			<div class="container">

				<h1 class="text-center"><?php echo @lang('MANAGE_MEMBER') ?></h1>
				<!-- display the error or success messages -->
				<?php displayMsg(@$formErrors, @$successMsg); ?>

				<div class="add-new-btn">
					<a href="members.php?do=Add" class="btn btn-primary">
						<i class="fa fa-plus"></i> 
						<?php echo @lang('ADD_NEW_MEMBER'); ?>
					</a>
				</div>
				<div class="table-responsive">
					<?php
						if(!empty($users)){?>
							<table class="manage-table text-center table table-bordered">
								<tr>
									<td><a href="members.php?sort=id DESC" title="<?php echo @lang('SORT_MEM_BY_ID') ?>">#ID</a></td>
									<td><a href="members.php?sort=image DESC" title="<?php echo @lang('SORT_MEM_BY_IMG') ?>"><?php echo @lang('MEM_TABLE_IMAGE') ?></a></td>
									<td><a href="members.php?sort=username ASC" title="<?php echo @lang('SORT_MEM_BY_USERNAME') ?>"><?php echo @lang('MEM_TABLE_USERNAME') ?></a></td>
									<td><a href="members.php?sort=Email ASC" title="<?php echo @lang('SORT_MEM_BY_EMAIL') ?>"><?php echo @lang('MEM_TABLE_EMAIL') ?></a></td>
									<td><a href="members.php?sort=FullName ASC" title="<?php echo @lang('SORT_MEM_BY_F_NAME') ?>"><?php echo @lang('MEM_TABLE_F_NAME') ?></a></td>
									<td><a href="members.php?sort=Date DESC" title="<?php echo @lang('SORT_MEM_BY_DATE') ?>"><?php echo @lang('MEM_TABLE_REG_DATE') ?></a></td>
									<td><?php echo @lang('TABLE_CONTROL') ?></td>
								</tr>
								<?php 
									foreach($users as $user){
								?>
								<tr <?php if($user['admin'] == 1) echo 'class="admin" title="this is Admin Account Like You"';?> >
									<td><?php echo $user['id'] ?></td>
									<td class="img-item">
										<?php $image = empty($user['image'])? 'default.png' : $user['image'];  ?>
										<img src="../layout/images/profiles/avatar/<?php echo $image; ?>">
									</td>
									<td><?php echo $user['username'] ?></td>
									<td><?php echo $user['Email'] ?></td>
									<td><?php echo $user['FullName'] ?></td>
									<td><?php echo $user['Date'] ?></td>
									<td class="control">
										<a href="members.php?do=Edit&userid=<?php echo $user['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> Edit</a>
										<?php if($_SESSION['userid'] != $user['id']){ ?>
											<a href="members.php?delete=delete&userid=<?php echo $user['id']; ?>" class="btn btn-danger btn-xs confirm-delete"><i class="fa fa-times"></i> Delete</a>
										<?php }?>

									</td>
								</tr>
								<?php
									}
								?>
						
							</table>

						<?php }else{
							echo "<div class = 'alert alert-info text-center'><h4>There is no Members To Show</h4></div>";
						}
					?>
			</div>

			
			</div>


		<?php }
		elseif($do == 'Add'){ //Add new member

			if($_SERVER['REQUEST_METHOD'] === 'POST'){

				$username 	= filter_var($_POST['username'], FILTER_SANITIZE_STRING);
				$password 	= filter_var($_POST['password'], FILTER_SANITIZE_STRING);
				$fullname 	= filter_var(trim($_POST['full']), FILTER_SANITIZE_STRING);
				$hashedPass = password_hash($password, PASSWORD_DEFAULT);
				$admin 		= filter_var($_POST['admin'], FILTER_SANITIZE_NUMBER_INT);

				//check if the username exist in Database
				$userExist = selectItems('username', 'users', 'username = "' . $username . '"');

				$formErrors = array(); //initialize the error form array

				if(!empty($userExist)){
					$formErrors[] = @lang('USERNAME_EXIST');
				}
				
				if(strlen($username) < 3) $formErrors[] = @lang('USERNAME<3');
				if(strlen($password) < 5) $formErrors[] = @lang('PASSWORD<5');

				if(empty($formErrors)){
					//Add the new member to the database
					$addUser = insertItems('users',
									'username, password, FullName, `Date`, admin',
									'?, ?, ?, now(), ?',
									array($username, $hashedPass, $fullname, $admin));

					$addUser > 0 ? $successMsg = @lang('USER_ADDED') : $formErrors[] = @lang('NO_CHANGE');
				}

			}

		?>
			<!-- just to change the title tag content with this -->
			<span id="pageTitle" hidden=""><?php echo @lang('MEM_ADD_P'); ?></span>

			<h1 class="text-center"><?php echo lang('ADD MEMBERS') ?></h1>
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
						<div class="edit-form form-group">
							<!-- display the error or success messages -->
							<?php displayMsg(@$formErrors, @$successMsg); ?>

							<form action ='?do=Add' method="POST">
								<!-- Start username Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('USERNAME') ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			="form-control input-lg" 
												type 			="text" 
												name 			="username" 
												placeholder 	="<?php echo @lang('ADD_USERNAME') ?>"
												required 		="required"
												autocomplete 	= "off"
												value 			="<?php echo @$username; ?>"
												autofocus
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End username Input -->

								<!-- Start password Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('PASSWORD') ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			="form-control input-lg" 
												type 			="password" 
												name 			="password" 
												placeholder 	="<?php echo @lang('PASSWORD') ?>"
												required 		="required"
												autocomplete 	= "new-password"
												value 			="<?php echo @$password; ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End password Input -->

								<!-- Start Full Name Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('FULL_NAME') ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			="form-control input-lg" 
												type 			="text" 
												name 			="full" 
												placeholder 	="<?php echo @lang('FULLNAME') ?>"
												required 		="required"
												value 			="<?php echo @$fullname; ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Full Name Input -->

								<!-- Start Admin Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('ADMIN_?'); ?></label></div>
									<div class="col-sm-10 ">
										<input id="vis-no" type="radio" value="0" name="admin" checked>
										<label for="vis-no">Client</label>

										<input id="vis-yes" type="radio" value="1" name="admin">
										<label for="vis-yes">Admin</label>
										
										<input id="vis-super" type="radio" value="2" name="admin">
										<label for="vis-super">Supervisor</label>
									</div>
								</div>
								<!-- End Admin Input -->	

								<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo @lang('SAVE') ?>">
							</form>
						</div>
					</div>
				</div>
			</div>


		<?php }elseif($do == 'Edit'){	//Edit Page 

			$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
			
			if($_SERVER['REQUEST_METHOD'] === 'POST'){

				$username 	= filter_var($_POST['username'], FILTER_SANITIZE_STRING);
				$password 	= filter_var($_POST['password'], FILTER_SANITIZE_STRING);
				$fullname 	= filter_var(trim($_POST['full']), FILTER_SANITIZE_STRING);
				$hashedPass = password_hash($password, PASSWORD_DEFAULT);
				$admin 		= $_POST['admin'];

				$formErrors = array(); //initialize form errors array

				if(strlen($username) < 3) $formErrors[] = @lang('USERNAME<3');
				if(strlen($password) < 5 && !empty($password)) $formErrors[] = @lang('PASSWORD<5');

				//Check If The Username Is Exist In The Database
				$userExist = selectItems('username', 'users', 'username = ? AND id != ?', array($username, $userid));
				
				if(!empty($userExist)) $formErrors[] = @lang('USERNAME_EXIST');

				if(empty($formErrors)){

					if(empty($password)){ //if the password input is empty then the user didn't change the password
						$updateUser = updateItems('users', 
										'username = ?, FullName = ?, admin = ?', 
										array($username, $fullname, $admin, $userid), 
										'id = ?');
					}else{ //if the password field not empty then the user changed the password
						$updateUser = updateItems('users', 
										'username = ?, password = ?, FullName = ?, admin = ?', 
										array($username, $hashedPass, $fullname, $admin, $userid), 
										'id = ?');
					}

					//If the Query already succeed then prind success message
					if($updateUser > 0){
						$_SESSION['username'] = subString($fullname, ' ');
						$successMsg = @lang('USERUPDATED');
					}
					else{
						$formErrors[] = @lang('NO_CHANGE');
					}
				}

			}

			$user = selectItems('*', 'users', 'id = ?', array($userid));

			if(!empty($user)){  

				$username  = $user[0]['username'];
				$fullname  = $user[0]['FullName'];
				$email 	   = $user[0]['Email'];
				$admin 	   = $user[0]['admin'];
			?>

				<!-- just to change the title tag content with this -->
				<span id="pageTitle" hidden=""><?php echo @lang('EDIT MEMBERS') . ' | ' . @$fullname; ; ?></span>
				
				<h1 class="text-center"><?php echo @lang('EDIT MEMBERS') ?></h1>
				<div class="container">
					<div class="row">
						<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
							<div class="edit-form form-group">
								<!-- display the error or success messages -->
								<?php displayMsg(@$formErrors, @$successMsg); ?>

								<form action ='?do=Edit&userid=<?php echo $userid; ?>' method="POST">
									<!-- Start username Input -->
									<div class="input-container">
										<div class="col-sm-2"><label>Username</label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			="form-control input-lg" 
													type 			="text" 
													name 			="username" 
													placeholder 	="<?php echo @lang('USERNAME') ?>"
													required 		="required"
													autocomplete 	= "off"
													value 			="<?php echo @$username; ?>"
													autofocus
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End username Input -->

									<!-- Start password Input -->
									<div class="input-container">
										<div class="col-sm-2"><label>Password</label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			="form-control input-lg" 
													type 			="password" 
													name 			="password" 
													placeholder 	="<?php echo @lang('EDITPASSWORD') ?>"
													autocomplete 	= "new-password"
													value 			="<?php echo @$password; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End password Input -->

									<!-- Start Full Name Input -->
									<div class="input-container">
										<div class="col-sm-2"><label>Full Name</label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			="form-control input-lg" 
													type 			="text" 
													name 			="full" 
													placeholder 	="<?php echo @lang('FULLNAME') ?>"
													required 		="required"
													value 			="<?php echo @$fullname; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Full Name Input -->

									<!-- Start Admin Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('ADMIN_?'); ?></label></div>
										<div class="col-sm-10 ">
											<input id="vis-no" type="radio" value="0" name="admin" <?php if($admin == 0) echo 'checked'; ?>>
											<label for="vis-no">Client</label>
											
											<input id="vis-yes" type="radio" value="1" name="admin" <?php if($admin == 1) echo 'checked'; ?>>
											<label for="vis-yes">Admin</label>

											<input id="vis-super" type="radio" value="2" name="admin" <?php if($admin == 2) echo 'checked'; ?>>
											<label for="vis-super">Supervisor</label>
										</div>
									</div>
									<!-- End Admin Input -->	

									<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo @lang('SAVE') ?>">
								</form>
							</div>
						</div>
					</div>
				</div>


			<?php 
			}
			else {
				echo '<div class="container"><div class="error-msg">' . @lang('ERR_USERID') . '</div></div>';
			}
		}
		// elseif($do == 'Activate'){
		// 	$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;

		// 	$activateUser = updateItems('users', 'RegStatus = ?', array(1, $userid), 'userID = ?');

		// 	if($activateUser > 0){
		// 		$msg = "<div class='success-msg'>The Member Activated Successfully</div>";
		// 		echo "<div class='container text-center'>";
		// 			redirectHTTP($msg, 'back');
		// 		echo "</div>";
		// 	}else{
		// 		$msg = "<div class='alert alert-danger'>The Member Didn't Activate Try Again</div>";
		// 		echo "<div class='container text-center'>";
		// 			redirectHTTP($msg, 'back');
		// 		echo "</div>";
		// 	}
		// }

		include_once $tpt_path ."footer.php";

	}else{
		header('location:index.php');
		exit();
	}

	?>
