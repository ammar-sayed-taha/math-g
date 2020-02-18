<?php

	ob_start();
	session_start();

	$pageTitle = 'Comments Page';

	if(isset($_SESSION['username'])){

		include_once 'init.php';

		$do = isset($_GET['do'])? $_GET['do'] : 'Manage';

		if($do == 'Manage'){

			/*
			** check if the user clicked on approve btn to approve on comment the do this statement
			*/
			if(isset($_GET['approve']) && $_GET['approve'] == 'approve'){
				// Check The Validity Of The Comment ID
				$commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? $_GET['commentid'] : 0;
				$approveComment = updateItems('comments', 'approve = ?', array(1, $commentid), 'id = ?');
				$approveComment > 0 ? $successMsg = @lang('COMMENT_APPROVED_YES') : $formErrors[] = @lang('NO_CHANGE');
			}

			/*
			** When the user clcik on delete btn then execute this statement to delete the comment
			*/
			if(isset($_GET['delete']) && $_GET['delete'] == 'delete'){
				// Check The Validity Of The Comment ID
				$commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? $_GET['commentid'] : 0;
				$stmt = $con->prepare(' DELETE 	FROM comments WHERE id = ? ');
				$stmt->execute(array($commentid));
				$stmt->rowCount() > 0 ? $successMsg = @lang('DELETE_COMMENT_YES') : $formErrors[] = @lang('NO_CHANGE');

			}

			/*
			** check if the user clicked on the header panel 
			** to sort the data of the panel body based on what is in array
			*/
			$sortBy = 'id DESC'; //initialize the sort variable to select from the table based on it
			if(isset($_GET['sort']) && !empty($_GET['sort'])){
				$sortArray = array('id DESC', 'comment ASC', 'add_date DESC', 'lessons.name ASC', 'users.username ASC', 'comments.parent ASC');
				
				if(in_array($_GET['sort'], $sortArray)) $sortBy = $_GET['sort'];
			}

			/* 
			** Checking If The Admin Clicked To Show All Comments of a single member
			*/
			if(isset($_GET['single']) && $_GET['single'] == 'true'){
				$userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
				
				$allComments = selectItemsComments('comments.member_id = ?', array($userid), $sortBy);
			}elseif($_SESSION['admin'] == 1){ //if the user is admin
				//Get All Comments From The Comments Table
				$allComments = selectItemsComments(1, array(), $sortBy);
			}elseif($_SESSION['admin'] == 2){ //if the user is supervisor
				$allComments = selectItemsComments('lessons.member_id = ?', array($_SESSION['userid']), $sortBy);
			}
			

		?>
				
			<!-- design the table which contains  the Comments -->
			<section>
				<div class="container">
					<h1 class="text-center"><?php echo @lang('MANAGE_COMMENT'); ?></h1>
					<!-- display the error or success messages -->
					<?php displayMsg(@$formErrors, @$successMsg); ?>

					<div class="add-new-btn">
						<a href="comments.php?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> 
							<?php echo @lang('ADD_NEW_COMMENT'); ?>
						</a>
					</div>

					<div class="table-responsive">
						<?php
							if(!empty($allComments)){?>
								<table class="manage-table text-center table table-bordered">
									<tr>

										<td><a href="comments.php?sort=id DESC" title="<?php echo @lang('SORT_COMMENT_BY_ID') ?>">#ID</a></td>
										<td><a href="comments.php?sort=comment ASC" title="<?php echo @lang('SORT_COMMENT_BY_NAME') ?>"><?php echo @lang('TABLE_COMMENT') ?></a></td>
										<td><a href="comments.php?sort=add_date DESC" title="<?php echo @lang('SORT_COMMENT_BY_DATE') ?>"><?php echo @lang('TABLE_DATE') ?></a></td>
										<td><a href="comments.php?sort=users.username ASC" title="<?php echo @lang('SORT_COMMENT_BY_USERNAME') ?>"><?php echo @lang('TABLE_USERNAME') ?></a></td>
										<td><a href="comments.php?sort=lessons.name ASC" title="<?php echo @lang('SORT_COMMENT_BY_LESSON') ?>"><?php echo @lang('TABLE_LESSON') ?></a></td>
										<td><a href="comments.php?sort=comments.parent ASC" title="<?php echo @lang('SORT_COMMENT_BY_PARENT') ?>"><?php echo @lang('PARENT_?') ?></a></td>
										<td><?php echo @lang('TABLE_CONTROL') ?></td>
									</tr>

									<?php 
										foreach($allComments as $comment){
									?>

									<tr>
										<td><?php echo @$comment['id'] ?></td>
										<td class="description" title="<?php echo $comment['comment']; ?>"><?php echo @$comment['comment']; ?></td>
										<td><?php echo @$comment['add_date'] ?></td>
										<td><?php echo @$comment['username'] ?></td>
										<td><?php echo @$comment['lesson_name'] ?></td>
										<td><?php echo @$comment['parent'] ?></td>
										<td class="control">
											<a href="comments.php?do=Edit&commentid=<?php echo $comment['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> Edit</a>
											<a href="comments.php?delete=delete&commentid=<?php echo $comment['id']; ?>" class="btn btn-danger btn-xs confirm-delete"><i class="fa fa-times"></i> Delete</a>
											<?php
												if(@$comment['approve'] == 0){?>
													<a href="comments.php?approve=approve&commentid=<?php echo $comment['id']; ?>" class="btn btn-primary btn-xs"><i class="fa fa-check"></i> Approve</a>
											<?php
												}
											?>
										</td>

									</tr>
									<?php
										}
									?>
							
								</table>
							<?php }else{
								echo "<div class = 'alert alert-info text-center'><h4>There is no Comments To Show</h4></div>";
							}
						?>
					</div>			
				</div>
			</section>


		<?php

		}elseif($do == 'Add'){  //Add Page
			
			/*
			** when the user clicked on add new comment btn then Enter in POST request
			*/
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$comment 	= filter_var(trim($_POST['comment']), FILTER_SANITIZE_STRING);
				$member 	= $_POST['member'];
				$lesson 	= $_POST['lesson'];
				$parent 	= $_POST['parent'];

				$formErrors = array();  //initialize ForErrors array
				if(empty($comment)) $formErrors[]  = @lang('COM_FIELD_EMPTY');
				if($lesson == 0) 	$formErrors[]  = @lang('ERR_CHOOSE_LESSON');
				
				if(empty($formErrors)){
					$addComment = insertItems('comments', 'comment, approve, add_date, member_id, lesson_id, parent',
									'?, 1, NOW(), ?, ?, ?',
									array($comment, $member, $lesson, $parent));
					$addComment > 0 ? $successMsg = @lang('COMMENT_ADDED_YES') : $formErrors[] = @lang('NO_CHANGE');

				}
			}
		?>

		<section>
			<!-- just to change the title tag content with this -->
			<span id="pageTitle" hidden="">Comments Page | Add New Comment</span>
			<h1 class="text-center"><?php echo lang('ADD COMMENT') ?></h1>	
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
						<div class="edit-form form-group">
							<!-- display the error or success messages -->
							<?php displayMsg(@$formErrors, @$successMsg); ?>

							<form action ='?do=Add' method="POST">

								<!-- Start Lesson Comment Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('COMMENT'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<textarea 
												class 		= "form-control" 
												name 		= "comment" 
												placeholder = "<?php echo lang('COMMENT FIELD') ?>" 
												required 	=  "required"
												autofocus
											><?php echo @$comment; ?></textarea>

											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Lesson Comment Input -->

								<!-- Start SelectBox Members Input -->
								<div class="input-container disable">
									<div class="col-sm-2"><label><?php echo @lang('MEMBERS'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="selectBox">
											<select name = "member">
												<?php
													$members = selectItems('id, username', 'users', 1, array(), 'username ASC');
													foreach ($members as $member) {
														echo "<option value = '" . $member['id'] . "'> ". $member['username'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
								</div>
								<!-- End SelectBox Members Input -->

								<!-- Start SelectBox Lessons Input -->
								<div class="input-container disable">
									<div class="col-sm-2"><label><?php echo @lang('LESSONS'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="selectBox">
											<select name = "lesson">
												<?php
													if($_SESSION['admin'] == 1) //if the user is admin
														$lessons = selectItems('id, name', 'lessons', 1, array(), 'name ASC');
													elseif($_SESSION['admin'] == 2) //if the user is supervisor
														$lessons = selectItems('id, name', 'lessons', 'member_id = ?', array($_SESSION['userid']), 'name ASC');

													foreach ($lessons as $lesson) {
														echo "<option value = '" . $lesson['id'] . "'> ". $lesson['name'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
								</div>
								<!-- End SelectBox Lessons Input -->

								<!-- Start SelectBox parent comment Input -->
								<div class="input-container disable">
									<div class="col-sm-2"><label><?php echo @lang('PARENT_?'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="selectBox">
											<select name = "parent">
												<option value="0" >none</option>
												<?php
													$comments = selectItems('id', 'comments', 'parent = ?', array(0));
													foreach ($comments as $comment) {
														echo "<option value = '" . $comment['id'] . "'> ". $comment['id'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
								</div>
								<!-- End SelectBox parent comment Input -->



								<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo lang('ADD') ?>">
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php
		}elseif($do == 'Edit'){

			$commentid = isset($_GET['commentid']) ? $_GET['commentid'] : 0; //get the commentid
			
			/*
			** if the user clicked on edit btn then execute this statement
			** to update the comment data
			*/
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$comment 	= $_POST['comment'];
				$member 	= $_POST['member'];
				$lesson 	= $_POST['lesson'];
				$parent 	= $_POST['parent'];

				$updateComment = updateItems('comments', 'comment = ?, member_id = ?, lesson_id = ?, parent = ?', 
									array($comment, $member, $lesson, $parent, $commentid), 'id = ?');
				$updateComment > 0 ? $successMsg = @lang('SUCCESS_COM_EDIT') : $formErrors[] = @lang('NO_CHANGE');
			}

			$comments = selectItems('comment, member_id, lesson_id, parent', 'comments', 'id = ' . $commentid);

			if(!empty($comments)){ ?>

				<section>
					<!-- just to change the title tag content with this -->
					<span id="pageTitle" hidden="">Comments Page | Edit Comment ?></span>
					<h1 class="text-center"><?php echo lang('EDIT COMMENT') ?></h1>
						<div class="container">
							<div class="row">
								<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
									<div class="edit-form form-group">
										<!-- display the error or success messages -->
										<?php displayMsg(@$formErrors, @$successMsg); ?>

										<form action ='?do=Edit&commentid=<?php echo $commentid; ?>' method="POST">

											<!-- Start Lesson Comment Input -->
											<div class="input-container">
												<div class="col-sm-2"><label><?php echo @lang('COMMENT'); ?></label></div>
												<div class="col-sm-10 ">
													<div class="custom-input">
														<textarea 
															class 		= "form-control" 
															name 		= "comment" 
															placeholder = "<?php echo lang('COMMENT FIELD') ?>" 
															required 	=  "required"
															autofocus
														><?php echo @$comments[0]['comment']; ?></textarea>

														<span class="style"></span>
													</div>
												</div>
											</div>
											<!-- End Lesson Comment Input -->

											<!-- Start SelectBox Members Input -->
											<div class="input-container disable">
												<div class="col-sm-2"><label><?php echo @lang('MEMBERS'); ?></label></div>
												<div class="col-sm-10 ">
													<div class="selectBox">
														<select name = "member">
															<?php
																$members = selectItems('id, username', 'users', 1, array(), 'username ASC');
																foreach ($members as $member) {?>
																	<option value = "<?php echo $member['id']; ?>" <?php if($comments[0]['member_id'] == $member['id']) echo 'selected'; ?>>
																		<?php echo $member['username']; ?></option>
																<?php }
															?>
														</select>
													</div>
												</div>
											</div>
											<!-- End SelectBox Members Input -->

											<!-- Start SelectBox Lessons Input -->
											<div class="input-container disable">
												<div class="col-sm-2"><label><?php echo @lang('LESSONS'); ?></label></div>
												<div class="col-sm-10 ">
													<div class="selectBox">
														<select name = "lesson">
															<?php
																if($_SESSION['admin'] == 1) //if the user is admin
																	$lessons = selectItems('id, name', 'lessons', 1, array(), 'name ASC');
																elseif($_SESSION['admin'] == 2) //if the user is supervisor
																	$lessons = selectItems('id, name', 'lessons', 'member_id = ?', array($_SESSION['userid']), 'name ASC');

																foreach ($lessons as $lesson) {?>
																	<option value = "<?php echo $lesson['id']; ?>" <?php if($comments[0]['lesson_id'] == $lesson['id']) echo 'selected'; ?>>
																		<?php echo $lesson['name']; ?></option>";
																<?php }
															?>
														</select>
													</div>
												</div>
											</div>
											<!-- End SelectBox Lessons Input -->

											<!-- Start SelectBox Lessons Input -->
											<div class="input-container disable">
												<div class="col-sm-2"><label><?php echo @lang('PARENT_?'); ?></label></div>
												<div class="col-sm-10 ">
													<div class="selectBox">
														<select name = "parent">
															<option value="0">none</option>
															<?php
																$parentComments = selectItems('id', 'comments', 'parent = ?', array(0));
																foreach ($parentComments as $parentComment) {?>
																	<option value = "<?php echo $parentComment['id']; ?>" <?php if($comments[0]['parent'] == $parentComment['id']) echo 'selected'; ?>>
																		<?php echo $parentComment['id']; ?></option>";
																<?php }
															?>
														</select>
													</div>
												</div>
											</div>
											<!-- End SelectBox Lessons Input -->


											<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo lang('EDIT') ?>">
										</form>
									</div>
								</div>
							</div>
						</div>
				</section>

			<?php
			}
			else{
				$msg = "<div class='alert alert-danger text-center'>the Comment Id = " . $commentid . " is not Exist</div>";
				echo "<div class='container text-center'>";
					redirectHTTP($msg, 'back');
				echo "</div>";

			}


		}else{
			header('location:comments.php');
			exit();
		}


		include_once  $tpt_path . 'footer.php';


	}else{
		header('location:index.php');
		exit();
	}

	ob_end_flush();