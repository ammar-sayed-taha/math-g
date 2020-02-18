<?php
	ob_start();
	session_start();
	$pageTitle = 'Lessons Page';

	include_once 'init.php';

	//used to show the lesson and the comments of it
	$lessonid = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? $_GET['lessonid'] : 0;

	//when the user add new comment then we will need this variable
	$parent 	= isset($_GET['parent']) && is_numeric($_GET['parent'])? filter_var($_GET['parent'], FILTER_SANITIZE_NUMBER_INT) : 0;


	//if the lesson id == 0 then redirect to index page
	if($lessonid == 0){
		header('location: index.php');
		exit();
	}

	//Get the Lesson Data
	$lesson = selectItems('*', 'lessons', 'id = ? AND visible = ?', array($lessonid, 0));
	$fileExtension = ''; //initialize the var to make it global
	if(!empty($lesson))
		$fileExtension = pathinfo($lesson[0]['file'], PATHINFO_EXTENSION); //Get the extension of the file

	//get the admin status to show delete btn of the comment if theis member is admin
	$admin = selectItems('admin', 'users', 'id = ?', array(@$_SESSION['uid']));

	/* Start Adding Comment Comment */
	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		$comment = filter_var(trim($_POST['comment']), FILTER_SANITIZE_STRING);

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
	/* End Adding Comment Comment */



	// Get the Comments of the file
	$parentComments = selectItemsComments('comments.approve = ? AND lessons.id = ? AND parent = ?', array(1, $lessonid, 0), 'comments.id DESC');
	$childComments = selectItemsComments('comments.approve = ? AND lessons.id = ? AND parent != ?', array(1, $lessonid, 0), 'comments.id ASC');
?>

	<!-- Set The Page title in title Tag -->
	<span id="pageTitle" hidden><?php echo @$lesson[0]['name']; ?></span>

	<!-- Start Comments Section -->
	<div class="container">
		<!-- display the error or success messages -->
		<?php displayMsg(@$formErrors, @$successMsg); ?>
	</div>	

	<section class="lessons">

		<div class="header">
			<!-- just used for overllay -->
			<div class="lesson-overlay"></div>
			<h1 class="text-center"><?php echo @$lesson[0]['name']; ?></h1>
			<div class="under-name">
				<span>
					<span><i class="far fa-clock fa-md fa-fw"></i> <?php echo @date('d M Y', @strtotime(@$lesson[0]['add_date'])) ?> | </span>
				
					<?php $member = selectItems('FullName', 'users', 'id = ?',array(@$lesson[0]['member_id'])); ?>
					<a href="profile.php?uid=<?php echo @$lesson[0]['member_id'] ?>"><span><i class="far fa-user fa-md fa-fw"></i> <?php echo @$member[0]['FullName'] ?></span></a>
					

				</span>
			</div>

		</div>
		<!-- Start path bar section  -->
		<?php $material = getParentTitle($lesson[0]['title_id'], 0, 'titles');  //get the material name of this lesson
		?>
		<div class="path-bar">
			<ol class="breadcrumb"> 
				<li><a href="index.php"><?php echo @lang('HOME'); ?></a></li>
				<?php for($i = count($material) - 1; $i >= 0; $i--){ 
						if($material[$i]['parent'] != 0){ ?>
							<li><a href="titles.php?titleid=<?php echo $material[$i]['id']; ?>"><?php echo $material[$i]['name']; ?></a></li>
				<?php }}?>
				<li class="active"><?php echo $lesson[0]['name'] ?></li>
			</ol>
		</div>
		<!-- End path bar section  -->

		<div class="container">
			
			<div class="back">
				<span id="goBack" title="<?php echo @lang('BACK'); ?>">
					<i class="fa fa-angle-left fa-md fa-fw"></i> <?php echo @lang('BACK'); ?>
				</span>
			</div>
			<div class="files-con">
				<?php 
				switch(@$fileExtension){ 
					case 'doc':
					case 'docx':
					case 'xls':
					case 'xlsx':
					case 'ppt':
					case 'pptx':
					case 'txt': ?>
					<iframe 
							src="<?php echo $lesson[0]['external_file']; ?>" 
							allowfullscreen="true" 
							mozallowfullscreen="true" 
							webkitallowfullscreen="true">
						
							<?php echo '<div class="error-msg">' . @lang('NO_SUPPORT_FRAME') . '</div>'; ?> <!--if the browser not supported -->
						</iframe>

					<?php
					break;

					case 'pdf':
					case 'htm':
					case 'html': ?>
						<iframe 
							src="data/files/<?php echo $lesson[0]['file'] ?>"
							width="960" 
							height="749" 
						>
							<?php echo '<div class="error-msg">' . @lang('NO_SUPPORT_FRAME') . '</div>'; ?> <!--if the browser not supported -->
						</iframe>
					<?php
					break;

					case 'mp4':
					case 'wmv':
					case 'flv':
					case 'avi': ?>
						<video controls="controls" controlsList="nodownload" >
							<source src="data/files/<?php echo $lesson[0]['file'] ?>" 
							type="video/<?php echo $fileExtension; ?>">

							<?php echo '<div class="error-msg">' . @lang('NO_SUPPORT_VIDEO') . '</div>'; ?> <!--if the browser not supported -->

						</video>
					<?php
					break;

					case 'png':
					case 'jpg':
					case 'jpeg':
					case 'gif': ?>
						<div><img class="img-responsive img-thumbnail" src="data/files/<?php echo $lesson[0]['file'] ?>"></div>
					<?php
					break;

					case 'swf':?>
						<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="csSWF">
			                <param name="movie" 			value="data/files/<?php echo $lesson[0]['file'] ?>" />
			                <param name="quality" 			value="best" />
			                <param name="allowfullscreen" 	value="true" />
			                <param name="scale" 			value="showall" />
			                <param name="allowscriptaccess" value="always" />
			                <param name="flashvars" value="autostart=false&showstartscreen=true&showendscreen=true" />
			                <!--[if !IE]>-->
			                <object 
			                	type="application/x-shockwave-flash" 
			                	data="data/files/<?php echo $lesson[0]['file'] ?>" 
			                >
			                    <param name="quality" value="best" />
			                    <!-- <param name="bgcolor" value="#1a1a1a" /> -->
			                    <param name="allowfullscreen" value="true" />
			                    <param name="scale" value="showall" />
			                    <param name="allowscriptaccess" value="always" />
			                    <param name="clickable" value="true" />
			                    <param name="flashvars" value="autostart=false&showstartscreen=true&showendscreen=true" />
			                <!--<![endif]-->
			                    <div id="noUpdate">
			                        <p>
			                        	<bdi><?php echo @lang('UPDATE_FLASH') ?></bdi>
			                        	<bdi><a href="http://www.adobe.com/go/getflashplayer"><?php echo @lang('DOWN_FLASH') ?></a></bdi>
			                        </p>
			                    </div>
			                <!--[if !IE]>-->
			                </object>
			                <!--<![endif]-->
			            </object>
					<?php
					break;

					default:?>
						
						<?php echo '<div class="error-msg">' . @lang('NOT_VALID_FILE') . '</div>'; ?> <!--if the browser not supported -->

				<?php }?>
				
			</div>

		</div>
	</section>

	<section class="comments">

		<!-- Start Hidden edit-comments field -->
		<div class="edit-com-field">
			<div class="inner-field">
				<textarea class="form-control"></textarea>
				<button class="btn btn-primary btn-md edit" type="button"><?php echo @lang('EDIT'); ?></button>
				<button class="btn btn-primary btn-md cancel" type="button"><?php echo @lang('CANCEL'); ?></button>
			</div>
		</div>
		<!-- End Hidden edit-comments field -->


		<div class="container">
			<div class="com">
				<h2><?php echo @lang('LESSON_COMS') . ' - ' . (count($parentComments) + count($childComments)); ?></h2>
				<?php if(!empty($parentComments)) {?>
					<div class="com-body">
						<?php foreach($parentComments as $parentComment) { ?>

							<div class="com-container">
								<span class="inner-con">
									<?php 
										$memberImg = empty($parentComment['memberImg']) ? 'default.png' : $parentComment['memberImg']; 
										$adminLink = $parentComment['admin'] != 0 ? 'profile.php?do=Manage&uid=' . $parentComment['userID'] : '#';
									?>
									<a href="<?php echo $adminLink; ?>">
										<img class="img-thumbnail" src="layout/images/profiles/avatar/<?php echo @$memberImg; ?>">
									</a>
								</span>
								<span class="inner-con">
									<div class="com-header">
										<span class="com-n">
											<a href="<?php echo $adminLink; ?>">
												<div class="name <?php if($parentComment['admin'] != 0) echo 'admin'; ?>"><?php echo $parentComment['FullName'] ?></div>
											</a>
											<div class="date"><?php echo @date('j M Y', strtotime($parentComment['add_date'])); ?></div>
										</span>
									</div>
									<div class="com-footer">
										<p data-id="<?php echo $parentComment['id'] ?>" ><?php echo nl2br($parentComment['comment']); ?></p>

										<!-- appear when the parentComment is on its lessons of that session -->
										<div class="under-com">
											<?php  if(isset($_SESSION['uid'])){ 
												if($admin[0]['admin'] != 0 || $parentComment['member_id'] == $_SESSION['uid']) {?>
													<a class="confirm-delete delete-com" href="query.php?lessonid=<?php echo @$lessonid; ?>&deleteCom=true&comid=<?php echo $parentComment['id']; ?>" data-parent="0">
														<span><?php echo @lang('DELETE_COM') ?></span>
													</a>

													<!-- edit btn -->
													<a class="edit-btn" data-value="_<?php echo $parentComment['id'] ?>" href="#">
														<span><?php echo @lang('EDIT') ?></span>
													</a>

												<?php }
												 } ?>
											<!-- reply btn -->
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
													<p data-id="<?php echo $childComment['id'] ?>"><?php echo nl2br($childComment['comment']); ?></p>

													<!-- appear when the childComment is on its lessons of that session -->
													<div class="under-com">
														<?php  if(isset($_SESSION['uid'])){ 
															//if the user is admin or the owner of the comment then he can delete it
															if($admin[0]['admin'] != 0 || $childComment['member_id'] == $_SESSION['uid']) {?>
																<a 
																	class="confirm-delete delete-com" 
																	href="query.php?lessonid=<?php echo @$lessonid; ?>&deleteCom=true&comid=<?php echo $childComment['id']; ?>"
																	data-parent="<?php echo $childComment['parent']; ?>"
																>
																	<span><?php echo @lang('DELETE_COM') ?></span>
																</a>

																<!-- Edit btn -->
																<a class="edit-btn" data-value="_<?php echo $parentComment['id'] ?>" href="#">
																	<span><?php echo @lang('EDIT') ?></span>
																</a>
															<?php }	
															 } ?>
															
															<!-- reply btn -->
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

											<form action="query.php?lessonid=<?php echo $lessonid; ?>&parent=<?php echo $parentComment['id'] ?>&member_id=<?php echo @$_SESSION['uid']; ?>" method="POST" data-value="<?php echo $parentComment['id'] ?>">
												<textarea class="form-control" name="comment" placeholder="<?php echo @lang('PLACE_CHILD_COM', $parentComment['FullName']) ?>" required></textarea>
												<input class="btn btn-primary btn-md" type="submit" value="<?php echo @lang('COMMENT') ?>">
											</form>

										<?php }else{ ?>
												<span class="no-login"><a href="login.php"><?php echo @lang('LOGIN_FIRST') ?></span>
										<?php } ?>
									</div>

								</div>
						 <?php } ?>

					</div>
				<?php }else{
					echo "<h3 class='no-comments text-center'>" . @lang('NO_COMMENTS_EXIST') . "</h3>";
				}?>

				<!-- Add New Comment on The Lesson -->
				<div class="com-now">
					<h3><?php echo @lang('ADD_COMMENT') ?></h3>
					<?php
					//make sure first if the user loged in or not
					if(isset($_SESSION['user'])){?>

						<form action="lessons.php?lessonid=<?php echo $lessonid; ?>&parent=0" method="POST">
							<textarea class="form-control" name="comment" placeholder="<?php echo @lang('PLACE_COM') ?>" required></textarea>
							<input class="btn btn-primary btn-md" type="submit" value="<?php echo @lang('COMMENT') ?>">
						</form>

					<?php }else{ ?>
							<span class="no-login"><a href="login.php"><?php echo @lang('LOGIN_FIRST') ?></span>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>

	<!-- End Comments Section -->

<?php
	include_once $tpt_path . 'footer.php';
	ob_end_flush();
?>