<?php

	ob_start();
	session_start();
	$pageTitle = "Settings";
	if(isset($_SESSION['uid'])){
		include_once "init.php";	

		$uid = $_SESSION['uid'];

		//Start Personal Information Programming

		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['personal-info'])){

			$formErrors   = array(); // for personal information field

			$fullname 		= filter_var(@$_POST['fullname'], FILTER_SANITIZE_STRING);
			$password 		= filter_var(@$_POST['password'], FILTER_SANITIZE_STRING);
			$hashPassword 	= password_hash($password, PASSWORD_DEFAULT);
			$username 		= filter_var(@$_POST['username'], FILTER_SANITIZE_STRING);
			$phone 			= filter_var(@$_POST['phone'], FILTER_SANITIZE_STRING);
			$email 			= filter_var(@$_POST['email'], FILTER_SANITIZE_EMAIL);
			$birthdate 		= filter_var(@$_POST['birthdate'], FILTER_SANITIZE_STRING);

			//if the password field was empty then the user didn't change the password
			if(empty($password)){
				$profileInfo = selectItems('password', 'users', 'id = ' . $uid);
				$hashPassword = $profileInfo[0]['password'];
			}else{
				if(strlen($password) < 5) $formErrors[] = @lang('INVALID_PASS');
			}			

			if(empty($username)) 			$formErrors[] = @lang('EMPTY_USERNAME');
			if(empty($fullname)) 			$formErrors[] = @lang('EMPTY_F_NAME');
			// if(! @checkChars($fullname))  	$formErrors[]  = @lang('INVALID_CHARS');  //check if the characters are allowed
			if(! @checkChars($password))  	$formErrors[]  = @lang('INVALID_CHARS');  //check if the characters are allowed
			if(! @checkChars($username))  	$formErrors[]  = @lang('INVALID_CHARS');  //check if the characters are allowed


			if(empty($formErrors)){
				//update the data
				$updateInfo = updateItems('users', 
								'username = ?, password = ?, Email = ?, FullName = ?, phone = ?, birthdate = ?', 
								array($username, $hashPassword, $email, $fullname, $phone, $birthdate, $uid),
								'id = ?');
				if($updateInfo > 0){
					$successMsg = @lang('P_INFO_UPDATED');
					//update the session
					$_SESSION['user'] = $username;
				}else{$formErrors[] = @lang('NO_CHANGE');}
			}

			//upload image if exist
			if(!empty(@$_FILES['image']['name'])){
				//get the image colomn to update the image
				$myprofile  = selectItems('image', 'users', 'id = ?', array($uid));
				$formErrors = uploadImg($_FILES, $myprofile, 'image', $uid);
				//redirect the page if the image changed to refresh the image in navbar
				if(empty($formErrors)){header('location:?'); exit();} 
			}

		}
		//End Personal Information Programming

		//Start additional information programming
		elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['additional-info'])){

			$formErrors = array(); // for Additional information field

			$location 		= filter_var(@$_POST['location'], FILTER_SANITIZE_STRING);
			$bio 			= filter_var(@$_POST['bio'], FILTER_SANITIZE_STRING);
			$occupation		= filter_var(@$_POST['occupation'], FILTER_SANITIZE_STRING);			

			//update the data
			$updateInfo = updateItems('users', 
							'location = ?, bio = ?, occupation = ?', 
							array($location, $bio, $occupation, $uid),
							'id = ?');
			$updateInfo > 0 ? $successMsg = @lang('ADD_INFO_UPDATED') : $formErrors[] = @lang('NO_CHANGE');

		}
		//End additional information programming

		//Start additional information programming
		elseif($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['social-info'])){

			$formErrors = array(); // for Additional information field

			$facebook 		= filter_var(@$_POST['facebook'], FILTER_SANITIZE_URL);
			$twitter 		= filter_var(@$_POST['twitter'], FILTER_SANITIZE_URL);
			$youtube 		= filter_var(@$_POST['youtube'], FILTER_SANITIZE_URL);
			$instagram		= filter_var(@$_POST['instagram'], FILTER_SANITIZE_URL);			
			$pintrest		= filter_var(@$_POST['pintrest'], FILTER_SANITIZE_URL);			
			$googleplus		= filter_var(@$_POST['googleplus'], FILTER_SANITIZE_URL);			
			$linkedin		= filter_var(@$_POST['linkedin'], FILTER_SANITIZE_URL);			

			//check if there is member id with this account exist or first time
			$memberExist = selectItems('id', 'social_links', 'member_id = ?', array($uid));
			//update the data of the user if it is exist
			if(!empty($memberExist)){
				$updateInfo = updateItems('social_links',
							'facebook=?, youtube=?, twitter=?, instagram=?, pintrest=?, googleplus=?, linkedin=?',
							array($facebook, $youtube, $twitter, $instagram, $pintrest, $googleplus, $linkedin, $uid),
							'member_id = ?');
			
				$updateInfo > 0 ? $successMsg = @lang('SOCIAL_INFO_UPDATED') : $formErrors[] = @lang('NO_CHANGE');

			}else{ //the user insert the links first time
				$addInfo = insertItems('social_links', 'facebook, youtube, twitter, instagram, pintrest, googleplus, linkedin, member_id',
									'?, ?, ?, ?, ?, ?, ?, ?', 
									array($facebook, $youtube, $twitter, $instagram, $pintrest, $googleplus, $linkedin, $uid));
				$addInfo > 0 ? $successMsg = @lang('SOCIAL_INFO_UPDATED') : $formErrors[] = @lang('NO_CHANGE');
			}
			
		}
		//End additional information programming


		// get the data of that profile to display it after updating it
		$profileInfo = selectItems('*', 'users', 'id = ' . $uid);
		if(!empty($profileInfo)){

			//get personal information
			$fullName 	= $profileInfo[0]['FullName'];
			$phone 		= $profileInfo[0]['phone'];
			$username 	= $profileInfo[0]['username'];
			$password 	= $profileInfo[0]['password'];
			$email 		= $profileInfo[0]['Email'];
			$birthdate 	= $profileInfo[0]['birthdate'];

			//get additional information
			$location 	= $profileInfo[0]['location'];
			$bio 	    = $profileInfo[0]['bio'];
			$occupation	= $profileInfo[0]['occupation'];

		}

		$socialLinks = selectItems('*', 'social_links', 'member_id = ' . $uid);
		if(!empty($socialLinks)){

			//get the social Media Links
			@$facebook 		= @$socialLinks[0]['facebook'];
			@$youtube  		= @$socialLinks[0]['youtube'];
			@$twitter 		= @$socialLinks[0]['twitter'];
			@$instagram 	= @$socialLinks[0]['instagram'];
			@$pintrest 		= @$socialLinks[0]['pintrest'];
			@$googleplus 	= @$socialLinks[0]['googleplus'];
			@$linkedin 		= @$socialLinks[0]['linkedin'];

		}



?>
		<!-- just to change the title tag content with this -->
		<span id="pageTitle" hidden><?php echo @lang('SETTING_PAGE'); ?></span>

		<!-- Start Personal Information Section  -->
		<section class="info personal-info" id="personal-info">
			<h1 class="text-center"><?php echo @lang('SETTING_PAGE'); ?></h1>

			<div class="container">
				<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
					<!-- display the error or success messages -->
					<?php displayMsg(@$formErrors, @$successMsg); ?>
				</div>

				<div class="row">
					<div class="info-body col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
						
						<div>
							<h2><?php echo @lang('PERSONAL_INFO') ?></h2>
							<form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST" enctype="multipart/form-data">
								<!-- Start Name Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('F_NAME');?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			= "form-control" 
												type 			= "text" 
												name 			= "fullname" 
												placeholder 	= "<?php echo @lang('PLACE_F_NAME') ?>"
												required 		= "required"
												autocomplete 	= "off"
												value 			= "<?php echo @$fullName; ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Name Input -->

								<!-- Start Phone Input -->
								<?php if($profileInfo[0]['admin'] != 0){ ?>
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('PHONE')?></label></div>
										<div class="col-sm-10">
											<div class="custom-input">
												<input 
													class 			="form-control" 
													type 			="text" 
													name 			="phone"
													placeholder 	= "<?php echo @lang('PLACE_PHONE') ?>"
													autocomplete 	= "off"
													value 			="<?php echo @$phone; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
								<?php }?>
								<!-- End Phone Input -->

								<!-- Start Username Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('USERNAME'); ?></label></div>
									<div class="col-sm-10">
										<div class="custom-input">
											<input 
												class 			= "form-control" 
												type 			= "text" 
												name 			= "username" 
												required 		= "required"
												placeholder 	= "<?php echo @lang('PLACE_USERNAME') ?>"
												autocomplete 	= "off"
												value 			= "<?php echo @$username; ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Username Input -->

								<!-- Start Password Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('PASSWORD'); ?></label></div>
									<div class="col-sm-10">
										<div class="custom-input">
											<input 
												class 			= "form-control" 
												type 			= "password" 
												name 			= "password"
												placeholder 	= "<?php echo @lang('PLACE_PASS') ?>" 
												autocomplete 	= "new-password"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Password Input -->

								<!-- Start Email Input -->
								<?php if($profileInfo[0]['admin'] != 0){ ?>
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('EMAIL')  ?></label></div>
										<div class="col-sm-10">
											<div class="custom-input">
												<input 
													class 		= "form-control" 
													type 		= "email" 
													name 		= "email"
													placeholder = "<?php echo @lang('PLACE_EMAIL') ?>"
													value 		= "<?php echo @$email; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
								<?php }?>
								<!-- End Email Input -->

								<!-- Start Image Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('IMAGE') ?></label></div>
									<div class="col-sm-10">
										<div class="custom-input">
											<input 
												class 		= "form-control" 
												type 		= "file" 
												name 		= "image"
												value 		= "<?php echo @$image; ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Image Input -->

								<!-- Start Birthdate Input -->
								<?php if($profileInfo[0]['admin'] != 0){ ?>
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('BIRTHDATE') ?></label></div>
										<div class="col-sm-10">
											<div class="custom-input">
												<input 
													class 		= "form-control" 
													pattern 	= "[0-9]{4}-[0-9]{2}-[0-9]{2}"
													type 		= "date" 
													name 		= "birthdate"
													value 		= "<?php echo @$birthdate; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
								<?php }?>
								<!-- End Birthdate Input -->
								
								<div class="col-sm-10 col-sm-offset-2">
									<input 
										class 	="btn btn-primary btn-block" 
										type 	="submit"
										name 	= "personal-info"
										value 	="Edit"
									>
								</div>

							</form>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- End Personal Information Section  -->


		<!-- show additional and social info only for the admin -->
		<?php if($profileInfo[0]['admin'] != 0){ //if the user is admin or supervisor?>

			<!-- Start Additional Information Section -->

			<section class="info additional-info" id="additional-info">
				<div class="container">
					<div class="row">
						<div class="info-body col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
							<div class="">
								<h2><?php echo @lang('DDITIONAL_INFO') ?></h2>

								<form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST">
									<!-- Start Location Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('LOCATION'); ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="location" 
													placeholder ="<?php echo @lang('PLACE_LOCATION')  ?>"
													value 		="<?php echo @$location; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Location Input -->

									<!-- Start Bio Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('BIO') ?></label></div>
										<div class="col-sm-10">
											<div class="custom-input">
												<textarea 
													class 		= "form-control"
													name 		= "bio"
													placeholder = "<?php echo @lang('PLACE_BIO') ?>" 
												><?php echo @$bio; ?></textarea>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Bio Input -->

									<!-- Start occupation Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('OCCUPATION') ?></label></div>
										<div class="col-sm-10">
											<div class="custom-input">
												<input 
													class 			= "form-control" 
													type 			= "text" 
													name 			= "occupation"
													placeholder 	= "<?php echo @lang('PLACE_OCCUPATION') ?>"
													value 			= "<?php echo @$occupation; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End occupation Input -->

									<div class="col-sm-10 col-sm-offset-2">
										<div class="form-btn">
											<input 
												class 	="btn btn-primary btn-block" 
												type 	="submit"
												name 	= "additional-info"
												value 	="Edit"
											>
										</div>
									</div>

								</form>
							</div>
						</div>
					</div>
				</div>
			</section>


			<!-- End Additional Information Section -->

			<!-- Start Social Information Section -->

			<section class="info social-info" id="social-info">
				<div class="container">
					<div class="row">
						<div class="info-body col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
							<div class="">
								<h2><?php echo @lang('SOCIAL_LINKS') ?></h2>

								<form class="form-group" action="<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST">
									<!-- Start Facebook Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('FACEBOOK'); ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="facebook" 
													placeholder ="<?php echo @lang('PLACE_FACEBOOK'); ?>"
													value 		="<?php echo @$facebook; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Facebook Input -->

									<!-- Start Twitter Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('TWITTER') ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="twitter" 
													placeholder ="<?php echo @lang('PLACE_TWITTER') ?>"
													value 		="<?php echo @$twitter; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Twitter Input -->

									<!-- Start Youtube Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('YOUTUBE')?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="youtube" 
													placeholder ="<?php echo @lang('PLACE_YOUTUBE')?>"
													value 		="<?php echo @$youtube; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Youtube Input -->

									<!-- Start Instagram Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('INSTA') ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="instagram" 
													placeholder ="<?php echo @lang('PLACE_INSTA') ?>"
													value 		="<?php echo @$instagram; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Instagram Input -->

									<!-- Start Pinterst Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('PINTEREST') ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="pintrest" 
													placeholder ="<?php echo @lang('PLACE_PINTEREST') ?>"
													value 		="<?php echo @$pintrest; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Pinterst Input -->

									<!-- Start Google+ Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('GOOGLE+') ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="googleplus" 
													placeholder ="<?php echo @lang('PLACE_GOOGLE+') ?>"
													value 		="<?php echo @$googleplus; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Google+ Input -->

									<!-- Start Linkedin Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('LINKEDIN') ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 		="form-control" 
													type 		="text" 
													name 		="linkedin" 
													placeholder ="<?php echo @lang('PLACE_LINKEDIN') ?>"
													value 		="<?php echo @$linkedin; ?>"
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Linkedin Input -->

									<div class="col-sm-10 col-sm-offset-2">
										<input 
											class 	="btn btn-primary btn-block" 
											type 	="submit"
											name 	= "social-info"
											value 	="Edit"
										>
									</div>

								</form>
							</div>
						</div>
					</div>
				</div>
			</section>

			<!-- End Social Information Section -->

		<?php }?>




<?php
		include_once $tpt_path . "footer.php";
	}else{
		header('location:index.php');
		exit();
	}
	ob_end_flush();
?>