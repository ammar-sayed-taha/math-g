<?php
	ob_start();

	session_start();
	$pageTitle = 'Material Titles';

	if(isset($_SESSION['username'])){

		include_once 'init.php';

		$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

		if($do == 'Manage'){


			//check if the user needs to delete a Title
			if(isset($_GET['delete']) && $_GET['delete'] == 'delete'){
				//check if the item id is valid
				$titleid = isset($_GET['titleid']) && is_numeric($_GET['titleid']) ? $_GET['titleid'] : 0;

				//check first if this title has childs then don't delete
				$hasChilds = selectItems('id', 'titles', 'parent = ?', array($titleid));
				if(empty($hasChilds)){
					//delete the item from the Batabase
					$stmt = $con->prepare('DELETE FROM titles WHERE id = ?');
					$stmt->execute(array($titleid));
					$stmt->rowCount() < 0 ? $formErrors[] = @lang('ERR_TITLE_DELETE') : $successMsg = @lang('TITLE_SUCESS_DEL');
				}else{
					$formErrors[] = @lang('CNT_DELETE');
				}
				
			}

			$sortBy = 'ordering DESC';  //initialize sort variable to sort the panel
			//get the parent Titles 
			if($_SESSION['admin'] == 1){ //geall titles if the user is admin
				$parentTitles 	= selectItems('*', 'titles', 'parent = ?', array(0), $sortBy);
			}elseif($_SESSION['admin'] == 2){ //get just titles of the supervisor
				$parentTitles 	= selectItems('*', 'titles', 'parent = ? AND member_id = ?', array(0, $_SESSION['admin']), $sortBy);
			}
			//get the child titles of the parent
			$childTitles	= selectItems('*', 'titles', 'parent != ?', array(0), $sortBy);
										

		?>

			<h1 class="text-center"><?php echo @lang('TITLES_MANAGE') ?></h1>

			<section class="titles">
				<div class="container">
					<!-- display the error or success messages -->
					<?php displayMsg(@$formErrors, @$successMsg); ?>

					<a href='?do=Add'><span class="btn btn-primary add-lesson"><bdi><i class="fa fa-plus"></i> <?php echo @lang('ADD_NEW_TITLE'); ?></bdi></span></a>
					
					<?php
						if(!empty($parentTitles)){ ?>

							<div class="panel panel-primary">
							<div class="panel-heading">
								<div><?php echo @lang('TITLES_MANAGE') ?></div>
							</div>
							<div class="panel-body">
								<div>
									<!-- Include The foreachOfTitleManagePage.php File To Display The Data Of Titles -->
									<?php include_once 'foreachOfTitleManagePage.php'; ?>
								</div>
							</div>
							
						</div>
						<?php
						}else{
							echo "<div class = 'alert alert-info text-center'><h4>" . @lang('NO_TITLES_TO_SHOW') . "</h4></div>";
						}

					?>
				</div>
				

			</section>

			
		<?php }elseif($do == 'Add'){

			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$name 		= filter_var(trim(@$_POST['name']), FILTER_SANITIZE_STRING);
				$order 		= filter_var(@$_POST['order'], FILTER_SANITIZE_NUMBER_INT);
				$parent 	= filter_var(@$_POST['material'], FILTER_SANITIZE_NUMBER_INT);
				$visible 	= filter_var(@$_POST['visible'], FILTER_SANITIZE_NUMBER_INT);

				$formErrors = array();  //initialize the form error array
				if(empty($name)) 	$formErrors[] = @lang('INSERT_NAME');

				if(empty($formErrors)){

					$addTitle = insertItems('titles', 'name	, ordering, parent, visible, member_id', '?, ?, ?, ?, ?',
									array($name, $order, $parent, $visible, $_SESSION['admin']));

					//check if the Title Added
					$addTitle > 0 ? $successMsg = @lang('SUCCESS_ADD') : $formErrors[] = @lang('NO_CHANGE');
				}


			}
		?>

			<section class="titles">
				<h1 class="text-center"><?php echo @lang('ADD TITLE'); ?></h1>
					
				<div class="container">
					<div class="row">
						<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
							<div class="edit-form form-group">
								<!-- display the error or success messages -->
								<?php displayMsg(@$formErrors, @$successMsg); ?>

								<form action ='?do=Add' method="POST">

									<!-- Start Title Input -->
									<div class="input-container">
										<div class="col-sm-2"><label>Title</label></div>
										<div class="col-sm-10 ">
											<div class="custom-input">
												<input 
													class 			="form-control input-lg" 
													type 			="text" 
													name 			="name" 
													placeholder 	="<?php echo lang('TITLE NAME') ?>"
													required 		="required"
													autocomplete 	= "off"
													value 			="<?php echo @$name; ?>"
													autofocus
												>
												<span class="style"></span>
											</div>
										</div>
									</div>
									<!-- End Title Input -->

									<!-- Start Ordering Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><?php echo @lang('ORDER'); ?></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<input 
														class 			="form-control input-lg" 
														type 			="number" 
														name 			="order" 
														placeholder 	="<?php echo lang('TITLE ORDER') ?>"
														value 			="<?php echo @$order; ?>"
														min 			= "0"
														autofocus
													>
													<span class="style"></span>
												</div>
											</div>
										</div>
									<!-- End Ordering Input -->

									<!-- Start The title parent selectbox -->
									<div class="input-container  disable">
										<div class="col-sm-2"><label><?php echo @lang('PARENT_?'); ?></label></div>
										<div class="col-sm-10 ">
											<?php include 'parentTitles.php'; ?>
										</div>
									</div>
									<!-- End The title parent selectbox -->
									

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
											

									<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo lang('SAVE') ?>">
								</form>
							</div>
						</div>
					</div>
					
				</div>
			</section>


		<?php
		}elseif($do == 'Edit'){

			$titleid = isset($_GET['titleid']) && is_numeric($_GET['titleid']) ? intval($_GET['titleid']) : 0; 

			//if the user came from POST Request
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$name 		= filter_var(trim(@$_POST['name']), FILTER_SANITIZE_STRING);
				$order 		= filter_var(@$_POST['order'], FILTER_SANITIZE_NUMBER_INT);
				$parent 	= filter_var(@$_POST['material'], FILTER_SANITIZE_NUMBER_INT);
				$visible 	= filter_var(@$_POST['visible'], FILTER_SANITIZE_NUMBER_INT);

				$formErrors = array();  //initialize the form error array
				if(empty($name)) 	$formErrors[] = @lang('INSERT_NAME');

				if(empty($formErrors)){

					$updateTitle = updateItems('titles', 
										'name = ?, ordering = ?,parent = ?,  visible = ?, member_id = ?',
										array($name, $order, $parent, $visible, $_SESSION['admin'], $titleid),
										'id = ?');

					$updateTitle > 0 ? $successMsg = @lang('SUCCESS_EDIT') : $formErrors[] = @lang('NO_CHANGE');


				}
			}

			$title = selectItems('*', 'titles', 'id = ' . $titleid);

			if(!empty($title)){

				$name 	 = $title[0]['name'];
				$order 	 = $title[0]['ordering'];
				$parent  = $title[0]['parent'];
				$visible = $title[0]['visible'];

			?>
				<!-- just to change the title tag content with this -->
				<span id="pageTitle" hidden>Edit Title | <?php echo $name; ?></span>
				<section >
					<h1 class="text-center">Edit Title</h1>
					<div class="container">
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
								<div class="edit-form">
									<!-- display the error or success messages -->
									<?php displayMsg(@$formErrors, @$successMsg); ?>

									<form action ='?do=Edit&titleid=<?php echo $titleid; ?>' method="POST">
										<!-- send the id of this title to use it in updating its data -->
										<input type="number" name="titleid" value = "<?php echo $titleid; ?>"  hidden>
										
										<!-- Start Title Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><?php echo @lang('NAME'); ?></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<input 
														class 			="form-control input-lg" 
														type 			="text" 
														name 			="name" 
														placeholder 	="<?php echo lang('TITLE NAME') ?>"
														required 		="required"
														autocomplete 	= "off"
														value 			="<?php echo @$name; ?>"
														autofocus
													>
													<span class="style"></span>
												</div>
											</div>
										</div>
										<!-- End Title Input -->

										<!-- Start Ordering Input -->
										<div class="input-container ">
											<div class="col-sm-2"><label><?php echo @lang('ORDER'); ?></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<input 
														class 			="form-control input-lg" 
														type 			="number" 
														name 			="order" 
														placeholder 	="<?php echo lang('TITLE ORDER') ?>"
														value 			="<?php echo @$order; ?>"
														min 			= "0"
														autofocus
													>
													<span class="style"></span>
												</div>
											</div>
										</div>
										<!-- End Ordering Input -->

										<!-- Start The title parent selectbox -->
										<div class="input-container disable">
											<div class="col-sm-2"><label><?php echo @lang('PARENT_?'); ?></label></div>
											<div class="col-sm-10 ">
												<?php
													$selected = $parent;
													include 'parentTitles.php'; 
												?>
											</div>
										</div>
										<!-- End The title parent selectbox -->

										<!-- Start Visible Input -->
										<div class="input-container disable">
											<div class="col-sm-2"><label><?php echo @lang('VISIBLE'); ?></label></div>
											<div class="col-sm-10 ">
												<input id="vis-yes" type="radio" value="0" name="visible" <?php if($visible == 0) echo 'checked'; ?>>
												<label for="vis-yes">Yes</label>

												<input id="vis-no" type="radio" value="1" name="visible"  <?php if($visible == 1) echo 'checked'; ?>>
												<label for="vis-no">No</label>
											</div>
										</div>
										<!-- End Visible Input -->
												

										<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo lang('SAVE') ?>">
									</form>
								</div>
							</div>
						</div>
					</div>
				</section>

			<?php
			}else{
				echo '<div class = "container"><h4 class = "error-msg">' . @lang('TITLE_NOT_EXIST') . '</h4></div>';
			}

		}elseif($do == 'Delete'){
			
			$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? $_GET['catid'] : 0;

			//Select The Category To Make Sure that the Category Is In Database
			$cat = selectItems('id', 'categories', 'id = ' . $catid);

			if(!empty($cat)){

				$stmt = $con->prepare('DELETE FROM categories WHERE id = ?');
				$stmt->execute(array($catid));

				if($stmt->rowCount() > 0){
					$msg = "<div class='success-msg'>The Category Deleted Successfully</div>";
					echo "<div class='container text-center'>";
						redirectHTTP($msg, 'back');
					echo "</div>";

				}else{
					$msg = "<div class='alert alert-danger text-center'>The Category Didn't Delete Try Again</div>";
					echo "<div class='container text-center'>";
						redirectHTTP($msg, 'back');
					echo "</div>";
				}


			}else{
				$msg = "<div class='alert alert-danger text-center'>You Can't Enter This Page Directly</div>";
				echo "<div class='container text-center'>";
					redirectHTTP($msg);
				echo "</div>";
			}

		}


		include_once $tpt_path . 'footer.php';

	}else{
		header('location:index.php');
		exit();
	}

	ob_end_flush();