<?php

	ob_start();
	session_start();
	$pageTitle = 'Lessons';

	if(isset($_SESSION['username'])){

		include_once 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		if($do == 'Manage'){ 

			//check if the user needs to delete a lesson
			if(isset($_GET['delete']) && $_GET['delete'] == 'Delete'){
				//check if the item id is valid
				$lessonid = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? $_GET['lessonid'] : 0;
				//delete the file from the folder before remove the lesson itself
				$file = selectItems('file', 'lessons', 'id = ' . $lessonid);
				if(!empty($file[0]['file'])){
					//remove the old file
					if(file_exists('../data/files/' . $file[0]['file'])){
						@unlink('../data/files/' . $file[0]['file']);
					}
				}
				//delete the item from the Batabase
				$stmt = $con->prepare('DELETE FROM lessons WHERE id = ?');
				$stmt->execute(array($lessonid));

				$stmt->rowCount() < 0 ? $formErrors[] = @lang('DELETE_FAILED') : $successMsg = @lang('SUCCESS_DELETE');
			}

			/*
			** check if the user clicked on the header panel 
			** to sort the data of the panel body based on what is in array
			*/
			$sortBy = 'id'; //initialize the sort variable to select from the table based on it
			if(isset($_GET['sort']) && !empty($_GET['sort'])){
				$sortArray = array('id', 'name', 'add_date', 'title_id', 'member_id', 'ordering', 'visible');
				
				if(in_array($_GET['sort'], $sortArray)) $sortBy = $_GET['sort'];
			}
			
			//Select The Items From Database
			if($_SESSION['admin'] == 1) //if the user is admin
				$AllLessons = selectItems('*', 'lessons', 1, array(),  $sortBy . ' DESC ');
			elseif($_SESSION['admin'] == 2) //if the user is supervisor
				$AllLessons = selectItems('*', 'lessons', 'member_id = ?', array(2),  $sortBy . ' DESC ');

		?>
			<!-- design the table which contains  the Items -->
			<div class="container">
				<h1 class="text-center"><?php echo @lang('MANAGE_LESSONS') ?></h1>
				<!-- display the error or success messages -->
				<?php displayMsg(@$formErrors, @$successMsg); ?>

				<div class="add-new-btn">
					<a href="lessons.php?do=Add" class="btn btn-primary">
						<i class="fa fa-plus"></i> <?php echo @lang('ADD_NEW_LESSON'); ?>
					</a>
				</div>
				<div class="table-responsive">
					<?php
						if(!empty($AllLessons)){?>
							<table class="manage-table text-center table table-bordered">
								<tr>
									<td><a href="lessons.php?sort=id" title="<?php echo @lang('SORT_LESSON_BY_ID') ?>">#ID</a></td>
									<td><a href="lessons.php?sort=name" title="<?php echo @lang('SORT_LESSON_BY_NAME') ?>"><?php echo @lang('TABLE_NAME') ?></a></td>
									<td><a href="lessons.php?sort=ordering" title="<?php echo @lang('SORT_LESSON_BY_ORDER') ?>"><?php echo @lang('TABLE_ORDER') ?></a></td>
									<td><a href="lessons.php?sort=visible" title="<?php echo @lang('SORT_LESSON_BY_VISIBLE') ?>"><?php echo @lang('TABLE_VISIBLE') ?></a></td>
									<td><a href="lessons.php?sort=add_date" title="<?php echo @lang('SORT_LESSON_BY_DATE') ?>"><?php echo @lang('TABLE_DATE') ?></a></td>
									<td><a href="lessons.php?sort=title_id" title="<?php echo @lang('SORT_LESSON_BY_LESSON') ?>"><?php echo @lang('TABLE_LESSON') ?></a></td>
									<td><a href="lessons.php?sort=title_id" title="<?php echo @lang('SORT_LESSON_BY_MATERIAL') ?>"><?php echo @lang('TABLE_MATERIAL') ?></td></a>
									<td><a href="lessons.php?sort=member_id" title="<?php echo @lang('SORT_LESSON_BY_USERNAME') ?>"><?php echo @lang('TABLE_USERNAME') ?></a></td>
									<td><?php echo @lang('TABLE_CONTROL') ?></td>

								</tr>
							<?php 
								foreach ($AllLessons as $lesson) {
							?>

								<tr>
									<td><?php echo $lesson['id'] ?></td>
									<td class="description"><?php echo $lesson['name'] ?></td>
									<td><?php echo $lesson['ordering'] ?></td>
									<td><?php echo $lesson['visible'] == 0 ? 'Yse' : 'No'; ?></td>
									<td><?php echo $lesson['add_date'] ?></td>
									<td>
										<?php 
											$title = selectWithJoin('titles.name, titles.id', 'titles', 'lessons', 
																  'titles.id = ' . $lesson['title_id']);
											echo $title[0]['name'];

										?>
									</td>

									<td>
										<?php 
											$material = getParentTitle($title[0]['id'], 0, 'titles');
											echo $material[0]['name'];
										?>
									</td>

									<td><?php 
										$user = selectWithJoin('users.username', 'users', 'lessons', 
															  'users.id = ' . $lesson['member_id']);
										echo $user[0]['username'];
									 	?>
									</td>

									<td class="btns control">
										<a href="lessons.php?do=Edit&lessonid=<?php echo $lesson['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> Edit</a>
										<a href="lessons.php?delete=Delete&lessonid=<?php echo $lesson['id']; ?>" class="btn btn-danger btn-xs confirm-delete"><i class="fa fa-times"></i> Delete</a>
									</td>
								</tr>
							<?php
								}
							?>
						
							</table>
						<?php } else{
							echo "<div class = 'alert alert-info text-center'><h4>There is no Lessons To Show</h4></div>";
						}
					?>
				</div>

			
				</div>


		<?php
		}elseif($do == 'Add'){

			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				@$name 			= filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
				@$parent 		= filter_var($_POST['material'], FILTER_SANITIZE_NUMBER_INT);
				@$teacher		= filter_var($_POST['member'], FILTER_SANITIZE_NUMBER_INT);
				@$order			= filter_var($_POST['order'], FILTER_SANITIZE_NUMBER_INT);
				@$visible		= filter_var($_POST['visible'], FILTER_SANITIZE_NUMBER_INT);
				@$external_file	= filter_var($_POST['external_file'], FILTER_SANITIZE_STRING);

				$formErrors = array();

				//get the File data
				$fileName 		= @$_FILES['file']['name'];
				$fileTemp 		= @$_FILES['file']['tmp_name'];

				// check if this file is MS file then must add its external embeded link
				$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				$MsExtension = array('ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx');
				if(in_array($fileExtension, $MsExtension) && empty($external_file)){
					$formErrors[] = @lang('ADD_MS_EMBED');
				}

				if(empty($fileName)) 	$formErrors[]  = @lang('ERR_ADD_FILE');
				if(empty($name)) 		$formErrors[]  = @lang('EMPTY_NAME_INPUT');
				if($parent == 0)		$formErrors[]  = @lang('CHOOSE_MATERIAL');

				//check the extension of the file if allowed or not
				//$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				if(! allowedExtension($fileExtension))
					$formErrors[] = @lang('FILE_NOT_ALLOWED');


				if(empty($formErrors)){

					$newName = encFileName($fileExtension); //just take the extension of the file and add random name for it
					$filePath = "../data/files/" . $newName; // The Path Which Upload The Image To

					if(@move_uploaded_file($fileTemp, $filePath)){   //upload the image

						$addLesson = insertItems('lessons', 
									'name, file, external_file, add_date, title_id, member_id, ordering, visible', 
									'? , ?, ?, now(), ?, ?, ?, ?', 
									array($name, $newName, $external_file, $parent, $teacher, $order, $visible));
		
						$addLesson > 0 ? $successMsg = @lang('LESSON_ADDED') : $formErrors[] = @lang('NO_CHANGE');

						/*  Start Adding Notification For All Members */
						$allusers = selectItems('id', 'users');  //get the id's of all users
						$lessonID = selectItems('id', 'lessons', 'file = ?', array($newName)); //get the id of this lesson
						foreach ($allusers as $user) {
							insertItems('notify', 
									'sender, reciever, lesson_id, type', '?, ?, ?, "lesson"', 
									array($_SESSION['userid'], $user['id'], $lessonID[0]['id']));
						}
						/*  End Adding Notification For All Members */


					}else{
						$formErrors[] = @lang('INVALID_UPLOAD');
					}

				}
			}
		?>

			<!-- just to change the title tag content with this -->
			<span id="pageTitle" hidden="">Add New Lesson</span>

			<h1 class="text-center"><?php echo lang('ADD LESSON') ?></h1>
					
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
					
						<!-- display the error or success messages -->
						<?php displayMsg(@$formErrors, @$successMsg); ?>

						<div class="edit-form form-group">
							<form action ='?do=Add' method="POST" enctype="multipart/form-data">
								<!-- Start Lesson Name Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('NAME'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			= "form-control input-lg" 
												type 			= "text" 
												name 			= "name" 
												placeholder 	= "<?php echo lang('LESSON NAME') ?>"
												value 			= "<?php echo @$name; ?>"
												required 		= "required"
												autofocus
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Lesson Name Input -->

								<!-- Start Lesson Oreder Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('ORDER'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			= "form-control input-lg" 
												type 			= "number" 
												name 			= "order" 
												placeholder 	= "<?php echo lang('LESSON_ORDER') ?>"
												value 			= "<?php echo @$order; ?>"
												autocomplete 	= 'off'
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Lesson Oreder Input -->

								<!-- Start Lesson File Input -->
								<div class="input-container ">
									<div class="col-sm-2"><label><?php echo @lang('UPLOAD_FILE'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 			= "form-control input-lg" 
												type 			= "file" 
												name 			= "file" 
												title 			= "<?php echo lang('UPLOAD_LESSON') ?>"
												required 		= "required"
											>
											<span class="style"></span>
										</div>
										<div class="note"><?php echo @lang('NOTE_EXTENSION'); ?></div>
									</div>
								</div>
								<!-- End Lesson File Input -->

								<!-- Start File Extension selectBox -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('EXTERNAL_FILE'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 		= "form-control"
												name 		= "external_file"
												value 		= "<?php echo @$external_file; ?>"
												placeholder = "<?php echo @lang('ADD_MS_FILE'); ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End File Extension selectBox -->

								<!-- Start Lesson Title SelectBox Input -->
								<div class="input-container disable">
									<div class="col-sm-2"><label><?php echo @lang('LESSON_TITLE'); ?></label></div>
									<div class="col-sm-10 ">
										<?php include 'parentTitles.php'; ?>
									</div>
								</div>
								<!-- End Lesson Title SelectBox Input -->

								<!-- Start Member SelectBox Input -->
								<div class="input-container disable">
									<div class="col-sm-2"><label><?php echo @lang('MEMBER'); ?></label></div>
									<div class="col-sm-10 ">
										<!-- The Members Field -->
										<div class="selectBox">
											<select name = "member">
												<?php
													$members = selectItems('id, username', 'users', 'admin in(1,2)', array(), 'username ASC');
													foreach ($members as $member) {
														echo "<option value = '" . $member['id'] . "'> ". $member['username'] . "</option>";
													}

												?>
												
											</select>
										</div>
									</div>
								</div>
								<!-- End Member SelectBox Input -->

								<!-- Start Visible Input -->
								<div class="input-container disable">
									<div class="col-sm-2"><label><?php echo @lang('VISIBLE'); ?></label></div>
									<div class="col-sm-10 ">
										<input id="vis-yes" type="radio" value="0" name="visible" checked>
										<label for="vis-yes">Yes</label>

										<input id="vis-no" type="radio" value="1" name="visible">
										<label for="vis-no">No</label>
									</div>
								</div>
								<!-- End Visible Input -->


								<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo @lang('ADD') ?>">
							</form>
						</div>
					</div>
					
				</div>
			</div>


			
		<?php
		}elseif($do == 'Edit'){

			$lessonid = isset($_GET['lessonid']) && is_numeric($_GET['lessonid']) ? $_GET['lessonid'] : 0;

			//when the user come from POST request
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$name 			= filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
				$parent 		= filter_var($_POST['material'], FILTER_SANITIZE_NUMBER_INT);
				$teacher		= filter_var($_POST['member'], FILTER_SANITIZE_NUMBER_INT);
				$order			= filter_var($_POST['order'], FILTER_SANITIZE_NUMBER_INT);
				$visible		= filter_var($_POST['visible'], FILTER_SANITIZE_NUMBER_INT);
				$external_file	= filter_var($_POST['external_file'], FILTER_SANITIZE_STRING);

				//get the file data
				$fileName = @$_FILES['file']['name'];
				$fileTemp = @$_FILES['file']['tmp_name'];

				// check if this file is MS file then must add its external embeded link
				$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				$MsExtension = array('ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx');
				if(in_array($fileExtension, $MsExtension) && empty($external_file)){
					$formErrors[] = @lang('ADD_MS_EMBED');
				}

				$formErrors = array();
				if(empty($name)) 		$formErrors[]  = @lang('EMPTY_NAME_INPUT');
				if($parent == 0)		$formErrors[]  = @lang('CHOOSE_MATERIAL');

				//check the extension of the file if allowed or not
				if(!empty($fileName) && !allowedExtension(strtolower(pathinfo($fileName, PATHINFO_EXTENSION))))
					$formErrors[] = @lang('FILE_NOT_ALLOWED');

				if(empty($formErrors)){
					
					//check if the lesson is exist or not to know how to deal with file of the image
					$lessonExist = selectItems('file', 'lessons', 'id = ?', array($lessonid));

					if(!empty($lessonExist)){

						//if the user changed the file of the lesson then remove the old and upload the new one
						if(!empty($fileName)){

							$newName = encFileName($fileExtension); //encrypt the name of the file to stor it in DB
							$imgPath = "../data/files/" . $newName; // The Path Which Upload The file To

							if(@move_uploaded_file($fileTemp, $imgPath)){   //upload the file
								
								//remove the old file before adding the new one
								if(file_exists('../data/files/' . $lessonExist[0]['file'])){
									@unlink('../data/files/' . $lessonExist[0]['file']);
								}

								//update the data of the lesson
								$updateLesson = updateItems('lessons', 
											'name = ?, file = ?, external_file = ?, title_id = ?, member_id = ?, ordering =?, visible = ?',
											 array($name, $newName, $external_file, $parent, $teacher, $order, $visible, $lessonid),
											 'id = ?');
								
								$updateLesson > 0 ? $successMsg = @lang('LESSON_UPDATED') : $formErrors[] = @lang('NO_CHANGE'); 
							}else{
								$formErrors[] = @lang('INVALID_UPLOAD');
							}
						}else{
							//update the data of the lesson
							$updateLesson = updateItems('lessons', 
										'name = ?, external_file = ?,  title_id = ?, member_id = ?, ordering =?, visible = ?',
										 array($name, $external_file, $parent, $teacher, $order, $visible, $lessonid),
										 'id = ?');
							
							$updateLesson > 0 ? $successMsg = @lang('LESSON_UPDATED') : $formErrors[] = @lang('NO_CHANGE'); 
						}

					}else{
						$formErrors[] = @lang('ERR_LESSONID');
					}
				}


			}

			$lesson = selectItems('*', 'lessons', 'id = ' . $lessonid);

			if(!empty($lesson)){ 

				//get the data of this lesson
				$name 			= $lesson[0]['name'];
				$order 			= $lesson[0]['ordering'];
				$title_id 		= $lesson[0]['title_id'];
				$member_id 		= $lesson[0]['member_id'];
				$visible 		= $lesson[0]['visible'];
				$file 			= $lesson[0]['file'];
				$external_file 	= $lesson[0]['external_file'];

			?>

				<!-- just to change the title tag content with this -->
				<span id="pageTitle" hidden="">Edit Lesson | <?php echo @$name; ?></span>

				<h1 class="text-center"><?php echo lang('EDIT_LESSON') ?></h1>
				<div class="container">
					<div class="row">
						<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
							<!-- display the error or success messages -->
							<?php displayMsg(@$formErrors, @$successMsg); ?>
							
							<div class="edit-form form-group">
								<form action ='?do=Edit&lessonid=<?php echo $lessonid; ?>' method="POST" enctype="multipart/form-data">

									<!-- Start Lesson Name Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('NAME'); ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			= "form-control input-lg" 
													type 			= "text" 
													name 			= "name" 
													placeholder 	= "<?php echo lang('LESSON NAME') ?>"
													value 			= "<?php echo @$name; ?>"
													required 		= "required"
													autofocus
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Lesson Name Input -->

									<!-- Start Lesson order Input -->
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('ORDER'); ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			= "form-control input-lg" 
													type 			= "number" 
													name 			= "order" 
													placeholder 	= "<?php echo lang('LESSON_ORDER') ?>"
													value 			= "<?php echo @$order; ?>"
													autocomplete 	= 'off'
													autofocus
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Lesson order Input -->

									<!-- Start Lesson File Input -->
									<div class="text-center">file Name : <?php echo @$file; ?></div>
									<div class="input-container">
										<div class="col-sm-2"><label><?php echo @lang('UPLOAD_FILE'); ?></label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			= "form-control input-lg" 
													type 			= "file" 
													name 			= "file" 
													title 			= "<?php echo lang('UPLOAD_LESSON') ?>"
													autofocus
												>
												<span class="style"></span>
											</div>
											<div class="note"><?php echo @lang('NOTE_EXTENSION'); ?></div>
										</div>
									</div>
									<!-- End Lesson File Input -->

									<!-- Start File Extension selectBox -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('EXTERNAL_FILE'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 		= "form-control"
												name 		= "external_file"
												value 		= "<?php echo @$external_file ?>"
												placeholder = "<?php echo @lang('ADD_MS_FILE'); ?>"
											>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End File Extension selectBox -->

									<!-- Start Lesson Title SelectBox Input -->
									<div class="input-container disable">
										<div class="col-sm-2"><label><?php echo @lang('LESSON_TITLE'); ?></label></div>
										<div class="col-sm-10 ">
											<?php
												$selected = $title_id;
												include 'parentTitles.php'; 
											?>
										</div>
									</div>
									<!-- End Lesson Title SelectBox Input -->

									<!-- Start Member SelectBox Input -->
									<div class="input-container disable">
										<div class="col-sm-2"><label><?php echo @lang('MEMBER'); ?></label></div>
										<div class="col-sm-10 ">
											<!-- The Members Field -->
											<div class="selectBox">
												<select name = "member">
													<?php
														$members = selectItems('id, username', 'users', 'admin in(1,2)', array(), 'username ASC');
														foreach ($members as $member) {?>
															<option value ="<?php echo $member['id']; ?>" <?php if(@$member_id == @$member['id']) echo 'selected'?> > <?php echo $member['username']; ?></option>
														<?php }

													?>
													
												</select>
											</div>
										</div>
									</div>
									<!-- End Member SelectBox Input -->

									<!-- Start Visible Input -->
									<div class="input-container disable">
										<div class="col-sm-2"><label><?php echo @lang('VISIBLE'); ?></label></div>
										<div class="col-sm-10 ">
											<input id="vis-yes" type="radio" value="0" name="visible" <?php if(@$visible == 0) echo 'checked'; ?>>
											<label for="vis-yes">Yes</label>

											<input id="vis-no" type="radio" value="1" name="visible"  <?php if(@$visible == 1) echo 'checked'; ?>>
											<label for="vis-no">No</label>
										</div>
									</div>
									<!-- End Visible Input -->


									<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo lang('EDIT'); ?>">
								</form>
							</div>
						</div>
						
					</div>
				</div>

			<?php

			}else{
				echo '<div class = "container"><h4 class = "error-msg">' . @lang('ERR_LESSONID') . '</h4></div>';
			}
		}else{
			header('location:index.php');
			exit();
		}


		include_once  $tpt_path . 'footer.php';

	}else{
		header('location:index.php');
		exit();
	}

	ob_end_flush();