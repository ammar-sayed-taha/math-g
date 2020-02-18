<?php

	ob_start();
	session_start();

	$pageTitle = 'Bar Page';

	if(isset($_SESSION['username'])){

		include_once 'init.php';

		$do = isset($_GET['do'])? $_GET['do'] : 'Manage';

		if($do == 'Manage'){

			/*
			** When the user clcik on delete btn then execute this statement to delete the comment
			*/
			if(isset($_GET['delete']) && $_GET['delete'] == 'delete'){
				// Check The Validity Of The Comment ID
				$barid = isset($_GET['barid']) && is_numeric($_GET['barid']) ? $_GET['barid'] : 0;
				$stmt = $con->prepare(' DELETE 	FROM bar WHERE id = ? ');
				$stmt->execute(array($barid));
				$stmt->rowCount() > 0 ? $successMsg = @lang('DELETE_BAR_YES') : $formErrors[] = @lang('NO_CHANGE');
			}

			/*
			** check if the user clicked on the header panel 
			** to sort the data of the panel body based on what is in array
			*/
			$sortBy = 'id DESC'; //initialize the sort variable to select from the table based on it
			if(isset($_GET['sort']) && !empty($_GET['sort'])){
				$sortArray = array('id DESC', 'sentence ASC', 'is_thanks DESC');
				
				if(in_array($_GET['sort'], $sortArray)) $sortBy = $_GET['sort'];
			}

			$allBars = selectItems('*', 'bar', 1, array(), $sortBy);
		?>
				
			<!-- design the table which contains  the Comments -->
			<section>
				<div class="container">
					<h1 class="text-center"><?php echo @lang('MANAGE_BAR'); ?></h1>
					<!-- display the error or success messages -->
					<?php displayMsg(@$formErrors, @$successMsg); ?>

					<div class="add-new-btn">
						<a href="?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> 
							<?php echo @lang('ADD_NEW_BAR'); ?>
						</a>
					</div>

					<div class="table-responsive">

						<?php
							if(!empty($allBars)){?>
								<table class="manage-table text-center table table-bordered">
									<tr>

										<td><a href="?sort=id DESC" title="<?php echo @lang('SORT_BAR_BY_ID') ?>">#ID</a></td>
										<td><a href="?sort=sentence ASC" title="<?php echo @lang('SORT_BAR_BY_SENTENCE') ?>"><?php echo @lang('TABLE_BAR') ?></a></td>
										<td><a href="?sort=is_thanks DESC" title="<?php echo @lang('SORT_BAR_BY_TYPE') ?>"><?php echo @lang('TABLE_TYPE') ?></a></td>
										<td><?php echo @lang('TABLE_CONTROL') ?></td>
									</tr>

									<?php 
										foreach($allBars as $bar){
									?>

									<tr>
										<td><?php echo @$bar['id'] ?></td>
										<td class="description"><?php echo @$bar['sentence'] ?></td>
										<td><?php echo @$bar['is_thanks'] == 0 ?  @lang('Top_BAR') : @lang('BOTTOM_BAR') ?></td>
										<td class="control">
											<a href="bar.php?do=Edit&barid=<?php echo $bar['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> Edit</a>
											<a href="bar.php?delete=delete&barid=<?php echo $bar['id']; ?>" class="btn btn-danger btn-xs confirm-delete"><i class="fa fa-times"></i> Delete</a>
											
										</td>
									</tr>
									<?php
										}
									?>
							
								</table>
							<?php }else{
								echo "<div class = 'alert alert-info text-center'><h4>There is no Bars To Show</h4></div>";
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

				$sentence 	= trim($_POST['sentence']);
				$type 		= $_POST['is_thanks'];

				$formErrors = array();  //initialize ForErrors array
				if(empty($sentence)) $formErrors[]  = @lang('BAR_SEN_EMPTY');
				
				if(empty($formErrors)){
					$addBar = insertItems('bar', 'sentence, is_thanks', '?, ?', array($sentence, $type));
					$addBar > 0 ? $successMsg = @lang('BAR_ADDED_YES') : $formErrors[] = @lang('NO_CHANGE');

				}
			}
		?>

		<section>
			<!-- just to change the title tag content with this -->
			<span id="pageTitle" hidden="">Bar Page | Add New Bar</span>
			<h1 class="text-center"><?php echo lang('ADD_BAR') ?></h1>	
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
						<div class="edit-form form-group">
							<!-- display the error or success messages -->
							<?php displayMsg(@$formErrors, @$successMsg); ?>

							<form action ='?do=Add' method="POST">

								<!-- Start Bar Sentence  Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('BAR_SENTENCE'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<textarea 
												class 		= "form-control" 
												name 		= "sentence" 
												placeholder = "<?php echo lang('SENTENCE_FIELD') ?>" 
												required 	=  "required"
												autofocus
											><?php echo @$sentence; ?></textarea>

											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Bar Sentence  Input -->

								<!-- Start Type Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('BAR_TYPE'); ?></label></div>
									<div class="col-sm-10 ">
										<input id="vis-yes" type="radio" value="0" name="is_thanks" checked>
										<label for="vis-yes"><?php echo @lang('BAR_TOP'); ?></label>

										<input id="vis-no" type="radio" value="1" name="is_thanks">
										<label for="vis-no"><?php echo @lang('BAR_BOTTOM'); ?></label>
									</div>
								</div>
								<!-- End Type Input -->


								<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo @lang('ADD') ?>">
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php
		}elseif($do == 'Edit'){

			$barid = isset($_GET['barid']) ? $_GET['barid'] : 0; //get the barid
			
			/*
			** if the user clicked on edit btn then execute this statement
			** to update the comment data
			*/
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$sentence 	= $_POST['sentence'];
				$type 		= $_POST['is_thanks'];

				$updateBar = updateItems('bar', 'sentence = ?, is_thanks = ?', 
									array($sentence, $type, $barid), 'id = ?');
				$updateBar > 0 ? $successMsg = @lang('SUCCESS_BAR_EDIT') : $formErrors[] = @lang('NO_CHANGE');
			}

			$bar = selectItems('*', 'bar', 'id = ' . $barid);

			if(!empty($bar)){ ?>

				<section>
					<!-- just to change the title tag content with this -->
					<span id="pageTitle" hidden>Edit Bar | <?php echo @$bar['is_thanks'] == 0 ?  @lang('DEFINITION_BAR') : @lang('THANKS_BAR'); ?></span>
					<h1 class="text-center"><?php echo lang('EDIT_BAR') ?></h1>	
					<div class="container">
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
								<div class="edit-form form-group">
									<!-- display the error or success messages -->
									<?php displayMsg(@$formErrors, @$successMsg); ?>

									<form action ='?do=Edit&barid=<?php echo $barid ?>' method="POST">

										<!-- Start Bar Sentence  Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><?php echo @lang('BAR_SENTENCE'); ?></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<textarea 
														class 		= "form-control" 
														name 		= "sentence" 
														placeholder = "<?php echo lang('SENTENCE_FIELD') ?>" 
														required 	=  "required"
														autofocus
													><?php echo @$bar[0]['sentence']; ?></textarea>

													<span class="style"></span>
												</div>
											</div>
										</div>
										<!-- End Bar Sentence  Input -->

										<!-- Start Type Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><?php echo @lang('BAR_TYPE'); ?></label></div>
											<div class="col-sm-10 ">
												<input id="vis-yes" type="radio" value="0" name="is_thanks" <?php if($bar[0]['is_thanks'] == 0) echo 'checked'; ?>>
												<label for="vis-yes"><?php echo @lang('BAR_TOP'); ?></label>

												<input id="vis-no" type="radio" value="1" name="is_thanks" <?php if($bar[0]['is_thanks'] == 1) echo 'checked'; ?>>
												<label for="vis-no"><?php echo @lang('BAR_BOTTOM'); ?></label>
											</div>
										</div>
										<!-- End Type Input -->


										<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo @lang('EDIT') ?>">
									</form>
								</div>
							</div>
						</div>
					</div>
				</section>

			<?php
			}
			else{
				$msg = "<div class='alert alert-danger text-center'>the Bar Id = " . $barid . " is not Exist</div>";
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