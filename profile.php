<?php
	ob_start();
	session_start();
	$pageTitle = "Profile Page";

	include_once 'init.php';

	$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';	

	if($do == 'Manage'){

		$uid = isset($_GET['uid'])  && is_numeric($_GET['uid']) ? $_GET['uid'] : 0;

		$myprofile = selectItems('*', 'users', 'id = ' . $uid);

		//if this user is not admin then redirect to homepage
		if($myprofile[0]['admin'] == 0){
			header('location:index.php');
			exit();
		}

		/*
		  Get The social media links of this profile
		*/
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

		/* Start checking if this profile of the user then change cover or avatar */
		if(isset($_SESSION['uid']) && @$_SESSION['uid'] == $uid){

			//if the user will update the avatar or the cover image
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				// Changing The Cover Image Profile

				if(@$_POST['changeCover'])
					$formErrors = uploadImg($_FILES, $myprofile, 'cover', $_SESSION['uid']);
				// Changing The Avatar Image Profile
				elseif(@$_POST['changeAvatar'])
					$formErrors = uploadImg($_FILES, $myprofile, 'image', $_SESSION['uid']);

				if(empty($formErrors) && (isset($_POST['changeCover']) || isset($_POST['changeAvatar']))){  //refresh the page to update the image in navbar section
					header('location:profile.php?uid=' . $uid);
					exit();
				}
			}

			//if the user need to delete the avatar or the cover image
			elseif(isset($_GET['delete'])) {
				// Deleting The Cover Image
				if($_GET['delete'] == 'cover')
					$formErrors = deleteImg($myprofile, 'cover', $_SESSION['uid']);
				// Deleting The Avatar Image
				elseif($_GET['delete'] == 'avatar')
					$formErrors = deleteImg($myprofile, 'image', $_SESSION['uid']);

				if(empty($formErrors)){  //refresh the page to update the image in navbar section
					header('location:profile.php?uid=' . $uid);
					exit();
				}
			}			
		}
		/* End checking if this profile of the user then do the following */

		/* Start Deleting Comment */
		if(isset($_GET['deleteComment']) && $_GET['deleteComment'] == true){
			//Get the comment id which will be deleted
			$comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? $_GET['comid'] : 0;

			$delete = deleteItems('comments', 'id = ?', array($comid));
			if($delete <= 0)
				$formErrors[] = @lang('NO_CHANGE');

		}
		/* End Deleting Comment */

		/* Start Adding The comments */

		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])){
			$comment 	= filter_var(trim($_POST['comment']), FILTER_SANITIZE_STRING);
			//when the user add new comment then we will need this variable that tell us which parent of this comment
			$parent 	= isset($_GET['parent']) && is_numeric($_GET['parent'])? filter_var($_GET['parent'], FILTER_SANITIZE_NUMBER_INT) : 0;
			$lessonid 	= isset($_GET['lessonid']) && is_numeric($_GET['lessonid'])? filter_var($_GET['lessonid'], FILTER_SANITIZE_NUMBER_INT) : 0;


			$formErrors = array();  //initialize ForErrors array
			if(empty($comment)) $formErrors[]  = @lang('COM_FIELD_EMPTY');
			
			if(empty($formErrors)){
				$approve = $parent != 0 ? 1 : 0; //if the parent comment is approved then the child comment also approved
				$addComment = insertItems('comments', 'comment, add_date, member_id, lesson_id, parent, approve',
								'?, NOW(), ?, ?, ?, ?',
								array($comment, @$_SESSION['uid'], $lessonid, $parent, $approve));
				if($parent == 0) //show the message only for the parent comments
					$addComment > 0 ? $successMsg = @lang('COMMENT_ADDED_YES') : $formErrors[] = @lang('NO_CHANGE');

			}
		}

		/* End Adding The comments */

		/*if there is no data selected for this $uid 
		this means this $uid is not in data base then redirect this page to home page */

		if(!empty($myprofile)){  //if not empty then print the profile

			//Get The Last 10 Comments On Items For This Profile
			$latestComments = 10;
			$parentComments = selectItemsComments('comments.approve = ? AND lessons.member_id = ? AND comments.parent = ?', array(1, $uid, 0), NULL, $latestComments);
			//the reply comments
			$childComments = selectItemsComments('comments.approve = ? AND lessons.member_id = ? AND parent != ?', array(1, $uid, 0));

		?>
			<!-- Set The Page title in title Tag -->
			<span id="pageTitle" hidden><?php echo  @lang('PRO_PAGE') . ' | ' . $myprofile[0]['FullName']; ?></span>
 

			<section class="pro">
				<!-- <div class="container"> -->
					<!-- display the error or success messages -->
					<?php displayMsg(@$formErrors, @$successMsg); ?>

					<div class="pro-cover">
						<?php 
							// $coverImg = selectItems('cover', 'users', 'userID = ' . $uid);
							$cover = empty($myprofile[0]['cover']) ? 'default.jpg' : $myprofile[0]['cover']; ?>
						<div class="img-con"><img id ="change-cover-img" src="layout/images/profiles/cover/<?php echo @$cover; ?>"></div>

						<!-- Start The Cover Camera Icon And Its Menu -->
						<?php if(isset($_SESSION['uid']) && @$_SESSION['uid'] == $myprofile[0]['id']){ ?>
							<span class="camera"><i class="fa fa-camera" title="Upload or delete the cover"></i></span>
							<div class="camera-menu">
								<span class="change-img">
									<form action="<?php echo $_SERVER['PHP_SELF'] . '?uid='. $uid ;?>" method="POST" enctype="multipart/form-data">
										<div>
											<input type="file" name="image" title="upload image">
											<span><i class="fa fa-camera fa-fw"></i> Change</span>
										</div>

										<div>
											<input class="btn btn-default" type="submit" name="changeCover" value="Upload">
											<span><i class="fa fa-upload fa-fw"></i> Upload</span>
										</div>
									</form>
								</span>
								<a class="confirm-delete" href="<?php echo $_SERVER['PHP_SELF'] ?>?delete=cover&uid=<?php echo $uid; ?>"><div title="delete image"><i class="fa fa-times fa-fw"></i> Delete</div></a>
							</div>
						<?php }?>
						<!-- End The Cover Camera Icon And Its Menu -->

					</div>
		
					<div class="pro-avatar">
						<div class="avatar">
							<div class="avatar-container">
								<?php 
									$avatar = empty($myprofile[0]['image']) ? 'default.png' : $myprofile[0]['image']; ?>
								<img id="change-avatar-img" src="layout/images/profiles/avatar/<?php echo @$avatar; ?>">

								<!-- Start The Avatar Camera Icon And Its Menu -->
								<?php if(isset($_SESSION['uid']) && @$_SESSION['uid'] == $myprofile[0]['id']){ ?>
									<span class="camera"><i class="fa fa-camera" title="Upload or delete the avatar"></i></span>
									<div class="camera-menu">
										<span class="change-img">
											<form action="<?php echo $_SERVER['PHP_SELF'] . '?uid='. $uid ;?>" method="POST" enctype="multipart/form-data">
												<div>
													<input type="file" name="image" title="upload image">
													<span><i class="fa fa-camera fa-fw"></i> Change</span>
												</div>
												<div>
													<input class="btn btn-default" type="submit" name="changeAvatar" value="Upload">
													<span><i class="fa fa-upload fa-fw"></i> Upload</span>
												</div>
											</form>
										</span>
										<a class="confirm-delete" href="<?php echo $_SERVER['PHP_SELF'] ?>?delete=avatar&uid=<?php echo $uid; ?>"><div title="delete image"><i class="fa fa-times fa-fw"></i> Delete</div></a>
									</div>
								<?php }?>
								<!-- End The Avatar Camera Icon And Its Menu -->
							</div>
						</div>
					</div>
				<!-- </div> -->
			</section>

			<section class="pro-body">
				<div class="container">
					<div class="row">
						<div class="col-sm-6">
							<div class="info">
								<div>
									<div>
										<span><?php echo @lang('F_NAME') ?></span>
										<span><?php echo !empty($myprofile[0]['FullName']) ? $myprofile[0]['FullName'] : @lang('NO_ADDED'); ?> </span>
									</div>
									<div>
										<span><?php echo @lang('EMAIL') ?></span>
										<span><?php echo !empty($myprofile[0]['Email']) ? $myprofile[0]['Email'] : @lang('NO_ADDED'); ?></span>
									</div>
									<div>
										<span><?php echo @lang('PHONE') ?></span>
										<span><?php 
											if(!empty($myprofile[0]['phone'])) echo $myprofile[0]['phone'];
											else echo @lang('NO_ADDED');
										?></span>
									</div>
									<div class="links">
										<a <?php if(!empty($facebook)) echo 'href="' . $facebook . '"'; else echo 'disabled'; ?> target="_blank" >
											<span 
												class="facebook <?php if(empty($facebook)) echo 'no-url'; ?>">
												<i class="fab fa-facebook-square fa-lg fa-fw"></i>
											</span>
										</a>
										<a <?php if(!empty($youtube)) echo 'href="' . $youtube . '"'; else echo 'disabled'; ?> target="_blank">
											<span 
												class="youtube <?php if(empty($youtube)) echo 'no-url'; ?>">
												<i class="fab fa-youtube fa-lg fa-fw"></i>
											</span>
										</a>
										<a <?php if(!empty($twitter)) echo 'href="' . $twitter . '"'; else echo 'disabled'; ?> target="_blank">
											<span 
												class="twitter <?php if(empty($twitter)) echo 'no-url'; ?>">
												<i class="fab fa-twitter fa-lg fa-fw"></i>
											</span>
										</a>
										<a <?php if(!empty($instagram)) echo 'href="' . $instagram . '"'; else echo 'disabled'; ?> target="_blank">
											<span 
												class="instagram <?php if(empty($instagram)) echo 'no-url'; ?>">
												<i class="fab fa-instagram fa-lg fa-fw"></i>
											</span>
										</a>
										<a <?php if(!empty($pintrest)) echo 'href="' . $pintrest . '"'; else echo 'disabled'; ?> target="_blank">
											<span 
												class="pintrest <?php if(empty($pintrest)) echo 'no-url'; ?>">
												<i class="fab fa-pinterest fa-lg fa-fw"></i>
											</span>
										</a>
										<a <?php if(!empty($googleplus)) echo 'href="' . $googleplus . '"'; else echo 'disabled'; ?> target="_blank">
											<span 
												class="googleplus <?php if(empty($googleplus)) echo 'no-url'; ?>">
												<i class="fab fa-google-plus fa-lg fa-fw"></i>
											</span>
										</a>
										<a <?php if(!empty($linkedin)) echo 'href="' . $linkedin . '"'; else echo 'disabled'; ?> target="_blank">
											<span class="linkedin <?php if(empty($linkedin)) echo 'no-url'; ?>">
												<i class="fab fa-linkedin fa-lg fa-fw"></i>
											</span>
										</a>

									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="info additional-info">
								<div>
									<div>
										<span><?php echo @lang('LOCATION') ?></span>
										<span><?php 
											if(!empty($myprofile[0]['location'])) echo $myprofile[0]['location'];
											else echo @lang('NO_ADDED');
										?></span>
									</div>

									<div>
										<span><?php echo @lang('WEBSITE') ?></span>
										<span><?php 
											if(!empty($myprofile[0]['website'])) echo $myprofile[0]['website'];
											else echo @lang('NO_ADDED');
										?></span>
									</div>

									<div>
										<span><?php echo @lang('OCCUPATION') ?></span>
										<span><?php 
											if(!empty($myprofile[0]['occupation'])) echo $myprofile[0]['occupation'];
											else echo @lang('NO_ADDED');
										?></span>
									</div>

									<div class="bio">
										<span>Bio:</span>
										<span class="inner-bio"><?php 
											if(!empty($myprofile[0]['bio'])) echo nl2br($myprofile[0]['bio']);
											else echo @lang('NO_ADDED');
										?></span>
									</div>

								</div>
							</div>
						</div>
					</div>

					<div class="com">
						<h2><?php echo @lang('LAST_COMMENTS', $myprofile[0]['FullName']) ?></h2>
						<?php if(!empty($parentComments)) {?>
							<div class="com-body">
								<?php foreach($parentComments as $parentComment){ ?>

									<div class="com-container">
										<span class="inner-con">
											<?php $memberImg = empty($parentComment['memberImg']) ? 'default.png' : $parentComment['memberImg']; ?>
											<a href="?do=Manage&uid=<?php echo $parentComment['userID']; ?>">
												<img src="layout/images/profiles/avatar/<?php echo @$memberImg; ?>">
											</a>
										</span>
										<span class="inner-con">
											<div class="com-header">
												<span class="com-n">
													<a href="?do=Manage&uid=<?php echo $parentComment['userID']; ?>">
														<div class="name <?php if($parentComment['admin'] != 0) echo 'admin'; ?>"><?php echo $parentComment['FullName'] ?></div>
													</a>
													<div class="date"><?php echo @date('j M Y', strtotime($parentComment['add_date'])); ?></div>
												</span>
												<a href="lessons.php?do=Manage&lessonid=<?php echo $parentComment['id_lesson']; ?>">
													<span class="lesson-n" title="<?php echo @lang('SHOW_LESSON_PG') ?>"><?php echo $parentComment['lesson_name']; ?></span>
												</a>
											</div>
											<div class="com-footer">
												<p><?php echo nl2br($parentComment['comment']); ?></p>

												<!-- appear when the parentComment is on its lessons of that session -->
												
												<div class="under-com">
													<?php if(isset($_SESSION['uid'])){ ?>
															<a class="confirm-delete" href="?uid=<?php echo @$uid; ?>&lessonDelete=true&comid=<?php echo $parentComment['id']; ?>">
																<span class="delete"><?php echo @lang('DELETE_COM') ?></span>
															</a>
													<?php } ?>

													<a class="reply-btn" data-value="_<?php echo $parentComment['id'] ?>" href="#">
														<span><?php echo @lang('REPLY') ?></span>
													</a>

													<?php if(isset($_SESSION['uid'])){  //make sure the user is login first?>
														<span class="like">
															<?php 
																$emojiMember = selectItems('emoji', 'emoji_comments', 'member_id = ? AND comment_id = ?', array(@$_SESSION['uid'], $parentComment['id'])); //get the like of the user on this comment 
															?>
															
															<span class="inner-like">
																<?php
																if(empty($emojiMember)){
																	echo '<span class="emo-name newLike">'.@lang('LIKE').'</span>';
																	echo '<img class="newReact" src="layout/images/emojis/new-like.png" alt="like-image" draggable="false">';

																}else{
																	$emo = 'under-like.png';
																	$emoName = @lang('LIKE');
																	switch($emojiMember[0]['emoji']){
																		case 1:  $emo = 'angry.gif';  	$emoName = @lang('ANGRY'); break;
																		case 2:  $emo = 'sad.gif'; 		$emoName = @lang('SAD'); break;
																		case 3:  $emo = 'wow.gif'; 		$emoName = @lang('WOW'); break;
																		case 4:  $emo = 'haha.gif'; 	$emoName = @lang('HAHA'); break;
																		case 5:  $emo = 'love.gif'; 	$emoName = @lang('LOVE'); break;
																		default: $emo = 'like.gif'; 	$emoName = @lang('LIKE');
																	}
																?>
																	<span class="emo-name"><?php echo $emoName; ?></span>
																	<img src="layout/images/emojis/<?php echo $emo ?>" alt="react-face" > 
																<?php } ?>
															</span>
															
															<span class="float" data-url="query.php?member_id=<?php echo @$_SESSION['uid'] ?>&comment_id=<?php echo $parentComment['id'] ?>">
																<img id="1" src="layout/images/emojis/angry.gif" alt="angry-face" draggable="false" data-value="<?php echo @lang('ANGRY') ?>">
																<img id="2" src="layout/images/emojis/sad.gif" alt="sad-face" draggable="false" data-value="<?php echo @lang('SAD') ?>">
																<img id="3" src="layout/images/emojis/wow.gif" alt="wow-face" draggable="false" data-value="<?php echo @lang('WOW') ?>">
																<img id="4" src="layout/images/emojis/haha.gif" alt="haha-face" draggable="false" data-value="<?php echo @lang('HAHA') ?>">
																<img id="5" src="layout/images/emojis/love.gif" alt="love-img" draggable="false" data-value="<?php echo @lang('LOVE') ?>">
																<img id="6" src="layout/images/emojis/like.gif" alt="like-img" draggable="false" data-value="<?php echo @lang('LIKE') ?>">	
														
															</span>
														</span>
													<?php }?>

														<!-- Start The reactions counts -->
														<?php
															//get the count of all types of reactions
															$react = get_emoji_details($parentComment['id']);
															//get the total number of reactions
															$emojies = selectItems('COUNT(id) AS count', 'emoji_comments', 'comment_id = ?', array($parentComment['id']));
														?>
														<?php if($emojies[0]['count'] > 0){ ?>
															<span class="emoji-detail">
																<!-- Angry reaction -->
																<?php 
																	if($emojies[0]['count'] > 0){ ?>
																		<span class="num"><?php echo $emojies[0]['count'] ?></span>
																<?php } ?>

																<?php if($react['angry'][0]['count'] > 0){ ?>
																	<span class="img-con">
																		<img class="_angry" src="layout/images/emojis/under-angry.png" alt="angry-face" draggable="false">
																		<span class="count"><?php echo $react['angry'][0]['count']; ?></span>
																	</span>
																<?php }?>

																<!-- Sad reaction -->
																<?php if($react['sad'][0]['count'] > 0){ ?>
																	<span class="img-con">
																		<img class="_sad" src="layout/images/emojis/under-sad.png" alt="sad-face" draggable="false">
																		<span class="count"><?php echo $react['sad'][0]['count']; ?></span>
																	</span>
																<?php }?>

																<!-- Wow reaction -->
																<?php if($react['wow'][0]['count'] > 0){ ?>
																	<span class="img-con">
																		<img class="_wow" src="layout/images/emojis/under-wow.png" alt="wow-face" draggable="false">
																		<span class="count"><?php echo $react['wow'][0]['count']; ?></span>
																	</span>
																<?php }?>

																<!-- Haha reaction -->
																<?php if($react['haha'][0]['count'] > 0){ ?>
																	<span class="img-con">
																		<img class="_haha" src="layout/images/emojis/under-haha.png" alt="haha-face" draggable="false">
																		<span class="count"><?php echo $react['haha'][0]['count']; ?></span>
																	</span>
																<?php }?>

																<!-- Love reaction -->
																<?php if($react['love'][0]['count'] > 0){ ?>
																	<span class="img-con">
																		<img class="_love" src="layout/images/emojis/under-love.png" alt="love-img" draggable="false">
																		<span class="count"><?php echo $react['love'][0]['count']; ?></span>
																	</span>
																<?php }?>

																<!-- Like reaction -->
																<?php if($react['like'][0]['count'] > 0){ ?>
																	<span class="img-con">
																		<img class="_like" src="layout/images/emojis/under-like.png" alt="like-img" draggable="false">
																		<span class="count"><?php echo $react['like'][0]['count']; ?></span>
																	</span>
																<?php }?>
															</span>
														<?php }?>

														<!-- End The reactions counts -->
												</div>
												
												
											</div>
										</span>										

										<?php
										// Display the child Comments
											foreach($childComments as $childComment){
												if($childComment['parent'] == $parentComment['id']){?>

													<div class="child-com">
														<span class="inner-con">
															<?php 
																$memberImg = empty($childComment['memberImg']) ? 'default.png' : $childComment['memberImg']; 
																$adminLink = $childComment['admin'] != 0 ? 'profile.php?do=Manage&uid=' . $childComment['userID'] : '';
															?>
															<?php if($adminLink != ''){ ?>
																	<a href="<?php echo $adminLink; ?>"><img src="layout/images/profiles/avatar/<?php echo @$memberImg; ?>"></a>
															<?php }else{?>
																<img src="layout/images/profiles/avatar/<?php echo @$memberImg; ?>">
															<?php }?>
														</span>
														<span class="inner-con">
															<div class="com-header">
																<span class="com-n">
																	<?php if($adminLink != ''){ ?>
																		<a href="<?php echo $adminLink; ?>">
																			<div class="name <?php if($childComment['admin'] != 0) echo 'admin'; ?>"><?php echo $childComment['FullName'] ?></div>
																		</a>
																	<?php }else{?>
																		<div class="name <?php if($childComment['admin'] != 0) echo 'admin'; ?>"><?php echo $childComment['FullName'] ?></div>
																	<?php }?>
																	
																	<div class="date"><?php echo @date('j M Y', strtotime($childComment['add_date'])); ?></div>
																</span>
															</div>
															<div class="com-footer">
																<p><?php echo nl2br($childComment['comment']); ?></p>

																<!-- appear when the childComment is on its lessons of that session -->
																<div class="under-com">
																	<?php  if(isset($_SESSION['uid'])){ 
																		//if the user is admin or the owner of the comment then he can delete it
																		if($admin[0]['admin'] == 1 || $childComment['member_id'] == $_SESSION['uid']) {?>
																			<a class="confirm-delete delete-com" href="?lessonid=<?php echo @$lessonid; ?>&deleteComment=true&comid=<?php echo $childComment['id']; ?>&uid=<?php echo $uid; ?>">
																				<span><?php echo @lang('DELETE_COM') ?></span>
																			</a>
																		<?php }	
																		 } ?>
																		<a class="reply-btn" data-value="_<?php echo $parentComment['id'] ?>" href="#">
																			<span><?php echo @lang('REPLY') ?></span>
																		</a>

																		<?php if(isset($_SESSION['uid'])){  //make sure the user is login first?>
																			<span class="like">
																				<?php 
																					$emojiMember = selectItems('emoji', 'emoji_comments', 'member_id = ? AND comment_id = ?', array(@$_SESSION['uid'], $childComment['id'])); //get the like of the user on this comment 
																				?>
																				
																				<span class="inner-like">
																					<?php
																					if(empty($emojiMember)){
																						echo '<span class="emo-name newLike">'.@lang('LIKE').'</span>';
																						echo '<img class="newReact" src="layout/images/emojis/new-like.png" alt="like-image" draggable="false">';

																					}else{
																						$emo = 'under-like.png';
																						$emoName = @lang('LIKE');
																						switch($emojiMember[0]['emoji']){
																							case 1:  $emo = 'angry.gif';  	$emoName = @lang('ANGRY'); break;
																							case 2:  $emo = 'sad.gif'; 		$emoName = @lang('SAD'); break;
																							case 3:  $emo = 'wow.gif'; 		$emoName = @lang('WOW'); break;
																							case 4:  $emo = 'haha.gif'; 	$emoName = @lang('HAHA'); break;
																							case 5:  $emo = 'love.gif'; 	$emoName = @lang('LOVE'); break;
																							default: $emo = 'like.gif'; 	$emoName = @lang('LIKE');
																						}
																					?>
																						<span class="emo-name"><?php echo $emoName; ?></span>
																						<img src="layout/images/emojis/<?php echo $emo ?>" alt="react-face" > 
																					<?php } ?>
																				</span>
																				
																				<span class="float" data-url="query.php?member_id=<?php echo @$_SESSION['uid'] ?>&comment_id=<?php echo $childComment['id'] ?>">
																					<img id="1" src="layout/images/emojis/angry.gif" alt="angry-face" draggable="false" data-value="<?php echo @lang('ANGRY') ?>">
																					<img id="2" src="layout/images/emojis/sad.gif" alt="sad-face" draggable="false" data-value="<?php echo @lang('SAD') ?>">
																					<img id="3" src="layout/images/emojis/wow.gif" alt="wow-face" draggable="false" data-value="<?php echo @lang('WOW') ?>">
																					<img id="4" src="layout/images/emojis/haha.gif" alt="haha-face" draggable="false" data-value="<?php echo @lang('HAHA') ?>">
																					<img id="5" src="layout/images/emojis/love.gif" alt="love-img" draggable="false" data-value="<?php echo @lang('LOVE') ?>">
																					<img id="6" src="layout/images/emojis/like.gif" alt="like-img" draggable="false" data-value="<?php echo @lang('LIKE') ?>">	
																			
																				</span>
																			</span>
																		<?php }?>

																		<!-- Start The reactions counts -->
																		<?php
																			//get the total number of reactions
																			$emojies = selectItems('COUNT(id) AS count', 'emoji_comments', 'comment_id = ?', array($childComment['id']));

																			//get the count of all types of reactions
																			$react = get_emoji_details($childComment['id']);
																		?>
																		<?php if($emojies[0]['count'] > 0){ ?>
																			<span class="emoji-detail">
																				<?php 
																					if($emojies[0]['count'] > 0){ ?>
																						<span class="num"><?php echo $emojies[0]['count'] ?></span>
																				<?php }?>
																				
																				<!-- Angry reaction -->
																				<?php if($react['angry'][0]['count'] > 0){ ?>
																					<span class="img-con">
																						<img src="layout/images/emojis/under-angry.png" alt="angry-face" draggable="false">
																						<span class="count"><?php echo $react['angry'][0]['count']; ?></span>
																					</span>
																				<?php }?>

																				<!-- Sad reaction -->
																				<?php if($react['sad'][0]['count'] > 0){ ?>
																					<span class="img-con">
																						<img src="layout/images/emojis/under-sad.png" alt="sad-face" draggable="false">
																						<span class="count"><?php echo $react['sad'][0]['count']; ?></span>
																					</span>
																				<?php }?>

																				<!-- Wow reaction -->
																				<?php if($react['wow'][0]['count'] > 0){ ?>
																					<span class="img-con">
																						<img src="layout/images/emojis/under-wow.png" alt="wow-face" draggable="false">
																						<span class="count"><?php echo $react['wow'][0]['count']; ?></span>
																					</span>
																				<?php }?>

																				<!-- Haha reaction -->
																				<?php if($react['haha'][0]['count'] > 0){ ?>
																					<span class="img-con">
																						<img src="layout/images/emojis/under-haha.png" alt="haha-face" draggable="false">
																						<span class="count"><?php echo $react['haha'][0]['count']; ?></span>
																					</span>
																				<?php }?>

																				<!-- Love reaction -->
																				<?php if($react['love'][0]['count'] > 0){ ?>
																					<span class="img-con">
																						<img src="layout/images/emojis/under-love.png" alt="love-img" draggable="false">
																						<span class="count"><?php echo $react['love'][0]['count']; ?></span>
																					</span>
																				<?php }?>

																				<!-- Like reaction -->
																				<?php if($react['like'][0]['count'] > 0){ ?>
																					<span class="img-con">
																						<img src="layout/images/emojis/under-like.png" alt="like-img" draggable="false">
																						<span class="count"><?php echo $react['like'][0]['count']; ?></span>
																					</span>
																				<?php }?>
																			</span>
																		<?php }?>

																		<!-- End The reactions counts -->

																</div>
															</div>
														</span>
														
													</div>

										<?php 	}
											}?>
											
											<!-- add new child comment -->
											<div class="<?php echo $parentComment['id'] ?>"></div>

											<!-- reply Comment will apprear under every parent comment -->
											<div class="add-reply com-now inner-com" id="_<?php echo $parentComment['id'] ?>">
												<?php
												//make sure first if the user loged in or not
												if(isset($_SESSION['user'])){?>

													<form action="query.php?lessonid=<?php echo $parentComment['id_lesson']; ?>&parent=<?php echo $parentComment['id'] ?>&member_id=<?php echo @$_SESSION['uid']; ?>" method="POST" data-value="<?php echo $parentComment['id'] ?>">
														<textarea class="form-control" name="comment" placeholder="<?php echo @lang('PLACE_CHILD_COM', $parentComment['FullName']) ?>" required></textarea>
														<input class="btn btn-primary btn-md" type="submit" value="<?php echo @lang('COMMENT') ?>">
													</form>

												<?php }else{ ?>
														<span class="no-login"><a href="login.php"><?php echo @lang('LOGIN_FIRST') ?></span>
												<?php } ?>
											</div>

									</div>

								<?php }?>

							</div>
						<?php }else{
							echo "<h3 class='no-comments text-center'>" . @lang('NO_COMMENTS_EXIST') . "</h3>";
						}?>
					</div>
				</div>
			</section>


		<?php

		}else{
			header('location:index.php');
			exit();
		}

	}else{
		
		header('Location:index.php');
		exit();
	}


?>


<?php 
	include_once $tpt_path . 'footer.php';
	ob_end_flush();
?>