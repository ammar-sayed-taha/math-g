<?php
	ob_start();
	session_start();
	$pageTitle = 'Dashboard';

	if(isset($_SESSION['username'])){
		include_once "init.php";  //include the initialize file
		
		// ٍStart Dashboard Page

		// Get the Total Number of Members from the database
		$totalUsers 	= selectItems('COUNT(id) as userCounts', 'users');
		$totalComments	= selectItems('COUNT(id) AS count', 'comments'); //Get total Number of Comments
		$totalTitles 	= selectItems('COUNT(id) AS count', 'titles'); //Get total Number of Lessons
		$totalLessons 	= selectItems('COUNT(id) AS count', 'lessons'); //Get total Number of Lessons


		$latestLessons 			= 10;  //get the latest items in the database
		$latestTitles 			= 10;  //get the latest items in the database
		$latestComments 		= 10;  //Get The Latest Comments Added To Database
		$latestRegisteredUsers 	= 10;  //Get The Latest Users Who Made Account Recently

		//Get the Latest data from the database for based on the request colomns
		$lasetUsers 	= selectItems("*", "users", 1, array(), "id DESC", $latestRegisteredUsers);

		if($_SESSION['admin'] == 1){ //if the user is admin
			$lastLessons 	= selectItems("*", "lessons", 1, array(), "id DESC", $latestLessons);
			$lastTitles 	= selectItems("*", "titles", 1, array(), "id DESC", $latestTitles);
			$lastComments 	= selectItemsComments(1, array(), 'comments.id DESC', $latestComments);
		}
		elseif($_SESSION['admin'] == 2){ //if the user is supervisor
			$lastLessons 	= selectItems("*", "lessons", 'member_id = ?', array($_SESSION['userid']), "id DESC", $latestLessons);
			$lastTitles 	= selectItems("*", "titles", 'member_id = ?', array($_SESSION['userid']), "id DESC", $latestTitles);
			$lastComments 	= selectItemsComments('lessons.member_id = ?', array($_SESSION['userid']), 'comments.id DESC', $latestComments);
		}

	?>

		<section class="main-news text-center">
			<div class="container">
				<h1 class=''><?php echo @lang('WELCOME') . ' ' . $_SESSION['username']; ?></h1>
				
				<div class="row">
					<div class="col-md-3 col-sm-6">
						<a href="members.php">
							<div class="news">
								<h4><?php echo @lang('TOTAL_MEM') ?></h4>
								<span><?php echo @$totalUsers[0]['userCounts']; ?></span>
							</div>
						</a>
					</div>

					<div class="col-md-3 col-sm-6">
						<a href="comments.php">
							<div class="news">
								<h4><?php echo @lang('TOTAL_COM') ?></h4>
								<span><?php 
									echo @$totalComments[0]['count'];
								?></span>
							</div>
						</a>
					</div>

					<div class="col-md-3 col-sm-6">
						<a href="titles.php">
							<div class="news">
								<h4><?php echo @lang('TOTAL_TITLES') ?></h4>
								<span><?php 
									echo @$totalTitles[0]['count']
								?></span>
							</div>
						</a>
					</div>

					<div class="col-md-3 col-sm-6">
						<a href="lessons.php">
							<div class="news">
								<h4><?php echo @lang('TOTAL_FILES') ?></h4>
								<span><?php 
									echo @$totalLessons[0]['count']
								?></span>
							</div>
						</a>
					</div>

				</div>

			</div>
		</section>

		<section class="latests">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<div>
							<div class="last-register">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<i class="fa fa-users"></i><?php echo @lang('LATEST'); ?> <strong><?php echo $latestRegisteredUsers; ?></strong> <?php echo @lang('LAST_REG_USERS'); ?>
										<span class="showHidePanel"><i class="fa fa-minus"></i></span>
									</div>
									<div class="panel-body">
										<ul class="list-unstyled ">
											<?php

												if(!empty($lasetUsers)){

													foreach ($lasetUsers as $user) {
													?>
														<li <?php if($user['admin'] == 1) echo 'class="admin" title="This is Admin Like You!"'; ?>>
															<span class="panel-image">
																<?php $image = empty($user['image'])? 'default.png' : $user['image'];  ?>
																<img src="../layout/images/profiles/avatar/<?php echo $image; ?>">
															</span>

															<?php echo $user['username']; ?>
															<?php if($_SESSION['admin'] == 1){?>
																<a href="members.php?do=Edit&userid=<?php echo $user['id']; ?>"><span class="btn btn-success btn-xs pull-right"><i class="fa fa-edit fa-xs fa-fw"></i>Edit</span></a>
																<?php if($_SESSION['userid'] != $user['id']){ ?>
																	<a href="members.php?delete=delete&userid=<?php echo $user['id']; ?>" class="confirm-delete"><span class="btn btn-danger btn-xs pull-right"><i class="fa fa-times fa-xs fa-fw"></i>Delete</span></a>
															<?php }}?>
														</li>

													<?php
													}
												 											
												 }else{
													echo "<li>";
														echo "There is No Users To Show";
													echo "</li>";
												}
											?>
										 </ul>
									</div>
								</div>
							</div>
						</div>

						<div>
							<div class="last-lessons">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<i class="fa fa-tag"></i><?php echo @lang('LATEST') ?> <strong><?php echo $latestTitles;?></strong> <?php echo @lang('TITLES'); ?>
										<span class="showHidePanel"><i class="fa fa-minus"></i></span>
									</div>
									<div class="panel-body">
										<ul class="list-unstyled ">
											<?php 
												if(!empty($lastTitles)){
													foreach ($lastTitles as $title) { ?>
														<li>
															<?php echo $title['name']; ?>
															<a href="titles.php?do=Edit&titleid=<?php echo $title['id']; ?>"><span class="btn btn-success btn-xs pull-right"><i class="fa fa-edit fa-xs fa-fw"></i>Edit</span></a>
														</li>
													<?php } ?>
														

												<?php }else{
													echo "<li>";
														echo @lang('NO_FILES_TO_SHOW');
													echo "</li>";
												}
											 ?>
										 </ul>
									</div>
								</div>
							</div>

						</div>
					</div>

					<div class="col-md-6">
						<div>
							<div class="last-lessons">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<i class="fa fa-tag"></i><?php echo @lang('LATEST') ?> <strong><?php echo $latestLessons;?></strong> <?php echo @lang('FILES'); ?>
										<span class="showHidePanel"><i class="fa fa-minus"></i></span>
									</div>
									<div class="panel-body">
										<ul class="list-unstyled ">
											<?php 
												if(!empty($lastLessons)){
													foreach ($lastLessons as $lesson) { ?>
														<li>
															<?php echo $lesson['name']; ?>
															<a href="lessons.php?do=Edit&lessonid=<?php echo $lesson['id']; ?>"><span class="btn btn-success btn-xs pull-right"><i class="fa fa-edit fa-xs fa-fw"></i>Edit</span></a>
														</li>
													<?php } ?>
														

												<?php }else{
													echo "<li>";
														echo @lang('NO_FILES_TO_SHOW');
													echo "</li>";
												}
											 ?>
										 </ul>
									</div>
								</div>
							</div>

						</div>

						<div>
							<div class="last-comments">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<i class="fa fa-comment-alt fa-fw"></i>
										 <?php echo  @lang('LATEST') . ' <strong>' . $latestComments . '</strong> ' . @lang('COMMENTS') ; ?>
										<span class="showHidePanel"><i class="fa fa-minus"></i></span>
									</div>
									<div class="panel-body">

										<?php 
											if(!empty($lastComments)){
												foreach($lastComments as $comment){ ?>
												<div class="c-container">
													<div class="member-n">
														<a href="members.php?userid=<?php echo $comment['userID']; ?>&single=true">
															<div title="<?php echo @lang('SHOW_MR_ABOUT') . ' ' . $comment['username']; ?>"><?php echo $comment['username']; ?></div>
														</a>
													</div>
													<div class="member-c">
														<p><?php echo nl2br($comment['comment']); ?></p>

														<div class="btn-c text-right">
															<span class="date-c"><?php echo $comment['add_date']; ?></span>

															<!-- Check Is The Comment Is Not Approved  -->
																<?php
																if($comment['approve'] == 0){?>
																	<a href="comments.php?approve=approve&commentid=<?php echo $comment['id']; ?>">
																		<span class="btn btn-primary btn-xs">
																			<i class="fa fa-check fa-xs fa-fw"></i> Approve
																		</span>
																	</a>
																<?php } ?>

																<a href="comments.php?do=Edit&commentid=<?php echo $comment['id']; ?>">
																	<span class="btn btn-success btn-xs">
																		<i class="fa fa-edit fa-xs fa-fw"></i> Edit
																	</span>
																</a>

																<a href="comments.php?delete=delete&commentid=<?php echo $comment['id']; ?>" class="confirm-delete">
																	<span class="btn btn-danger btn-xs">
																		<i class="fa fa-times fa-xs fa-fw"></i> Delete
																	</span>
																</a>

														</div>
														<a href="comments.php?do=Manage&single=true&userid=<?php echo $comment['userID']; ?>">
															<span class="showAll-c">
																<?php echo  @lang('SHOW_COM_OF') . " " . $comment['username']; ?>
															</span>
														</a>
													</div>
													
												</div>
										<?php } 
										}else {
											echo "<div class='noComments' >";
												echo @lang('NO_COM_TO_SHOW');
											echo "</div>";
										} ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
				
			</div>

		</section>


	<?php
		// End Dashboard Page
		include_once $tpt_path ."footer.php";

	}else{
		header('location:index.php');
		exit();
	}


	ob_end_flush();
	?>

