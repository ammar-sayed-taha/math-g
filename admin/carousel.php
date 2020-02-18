<?php
	ob_start();
	session_start();

	$pageTitle = 'Carousel Page';

	if(isset($_SESSION['username'])){

		include_once 'init.php';

		$do = isset($_GET['do'])? $_GET['do'] : 'Manage';

		if($do == 'Manage'){

			/*
			** When the user clcik on delete btn then execute this statement to delete the comment
			*/
			if(isset($_GET['delete']) && $_GET['delete'] == 'delete'){

				$carouselid = isset($_GET['carouselid']) && is_numeric($_GET['carouselid']) ? $_GET['carouselid'] : 0;

				//remove the image from the folder first
				$caro = selectItems('image', 'carousel', 'id = ?', array($carouselid));
				if(!empty($caro)){
					@unlink('../layout/images/background/' . $caro[0]['image']);
				}

				// Check The Validity Of The Comment ID
				$stmt = $con->prepare(' DELETE 	FROM carousel WHERE id = ? ');
				$stmt->execute(array($carouselid));
				$stmt->rowCount() > 0 ? $successMsg = @lang('DELETE_CAROUSEL_YES') : $formErrors[] = @lang('NO_CHANGE');
			}

			/*
			** check if the user clicked on the header panel 
			** to sort the data of the panel body based on what is in array
			*/
			$sortBy = 'id DESC'; //initialize the sort variable to select from the table based on it
			if(isset($_GET['sort']) && !empty($_GET['sort'])){
				$sortArray = array('id DESC', 'image ASC', 'title ASC', 'link DESC');
				
				if(in_array($_GET['sort'], $sortArray)) $sortBy = $_GET['sort'];
			}

			$allCarousels = selectItems('*', 'carousel', 1, array(), $sortBy);
		?>
				
			<!-- design the table which contains  the Comments -->
			<section class="carousel">
				<div class="container">
					<h1 class="text-center"><?php echo @lang('MANAGE_CAROUSEL'); ?></h1>
					<!-- display the error or success messages -->
					<?php displayMsg(@$formErrors, @$successMsg); ?>

					<div class="add-new-btn">
						<a href="?do=Add" class="btn btn-primary">
							<i class="fa fa-plus"></i> 
							<?php echo @lang('ADD_NEW_CAROUSEL'); ?>
						</a>
					</div>

					<div class="table-responsive">

						<?php
							if(!empty($allCarousels)){?>
								<table class="manage-table text-center table table-bordered">
									<tr>

										<td><a href="?sort=id DESC" title="<?php echo @lang('SORT_CAROUSEL_BY_ID') ?>">#ID</a></td>
										<td><a href="?sort=image ASC" title="<?php echo @lang('SORT_CAROUSEL_BY_IMAGE') ?>"><?php echo @lang('MEM_TABLE_IMAGE') ?></a></td>
										<td><a href="?sort=title ASC" title="<?php echo @lang('SORT_CAROUSEL_BY_TITLE') ?>"><?php echo @lang('TABLE_TITLE') ?></a></td>
										<td><a href="?sort=link DESC" title="<?php echo @lang('SORT_CAROUSEL_BY_LINK') ?>"><?php echo @lang('TABLE_LINK') ?></a></td>
										<td><?php echo @lang('TABLE_CONTROL') ?></td>
									</tr>

									<?php 
										foreach($allCarousels as $carousel){
									?>

									<tr>
										<td><?php echo @$carousel['id'] ?></td>
										<td><img class="car-img" src="../layout/images/background/<?php echo  @$carousel['image'] ?>"></td>
										<td class="description"><?php echo @$carousel['title'] ?></td>
										<td class="link" title="<?php echo @$carousel['link'] ?>"><?php echo @$carousel['link'] ?></td>
										<td class="control">
											<a href="carousel.php?do=Edit&carouselid=<?php echo $carousel['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> Edit</a>
											<a href="carousel.php?delete=delete&carouselid=<?php echo $carousel['id']; ?>" class="btn btn-danger btn-xs confirm-delete"><i class="fa fa-times"></i> Delete</a>
											
										</td>
									</tr>
									<?php
										}
									?>
							
								</table>
							<?php }else{
								echo "<div class = 'alert alert-info text-center'><h4>There is no Carousel To Show</h4></div>";
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

				$title 		= @trim($_POST['title']);
				$link 		= @$_POST['link'];

				$fileName 	= @$_FILES['image']['name']; 
				$fileTemp 	= @$_FILES['image']['tmp_name'];
				$fileSize 	= @$_FILES['image']['size'];

				// check if this image is in allowed extension or not
				$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
				$allowedImages = array('png', 'jpg', 'jpeg', 'gif');
				if(! in_array($fileExtension, $allowedImages)){
					$formErrors[] = @lang('CAR_FILE_INVALID');
				}


				$formErrors = array();  //initialize ForErrors array
				if(empty($title)) 			$formErrors[]  	= @lang('CAROUSEL_TIT_EMPTY');
				if(empty($fileName)) 		$formErrors[]  	= @lang('CAR_ADD_FILE');
				if($fileSize > 10*1024*1024) 	$formErrors[]	= @lang('SIZE_EXCEEDED'); //if file exceeds 10MB
				
				if(empty($formErrors)){

					$newName = encFileName($fileExtension); //just take the extension of the file and add random name for it
					$filePath = "../layout/images/background/" . $newName; // The Path Which Upload The Image To

					if(@move_uploaded_file($fileTemp, $filePath)){   //upload the image

						$addCarousel = insertItems('carousel', 'title, link, image', '?, ?, ?', array($title, $link, $newName));
						$addCarousel > 0 ? $successMsg = @lang('CAR_ADDED_YES') : $formErrors[] = @lang('NO_CHANGE');
					
					}else{
						$formErrors[] = @lang('INVALID_UPLOAD');
					}

					
				}
			}
		?>

		<section>
			<!-- just to change the title tag content with this -->
			<span id="pageTitle" hidden=""><?php echo @lang('CAROUSEL_TITLE_TAG') ?></span>
			<h1 class="text-center"><?php echo lang('ADD_NEW_CAROUSEL') ?></h1>	
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
						<div class="edit-form form-group">
							<!-- display the error or success messages -->
							<?php displayMsg(@$formErrors, @$successMsg); ?>

							<form action ='?do=Add' method="POST" enctype="multipart/form-data">

								<!-- Start Carousel Title  Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('CAROUSEL_TITLE'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 		= "form-control" 
												type 		= "text"
												name 		= "title" 
												placeholder = "<?php echo @lang('PLACE_TITLE') ?>"
												value		= "<?php echo @$title; ?>" 
												required 	=  "required"
												autofocus
											></input>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Carousel Title  Input -->

								<!-- Start Carousel Link  Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('CAROUSEL_LINK'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 		= "form-control" 
												type 		= "text"
												name 		= "link" 
												placeholder = "<?php echo @lang('PLACE_LINK'); ?>"
												value		= "<?php echo @$link; ?>"
											></input>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Carousel Link  Input -->

								<!-- Start Carousel Image  Input -->
								<div class="input-container">
									<div class="col-sm-2"><label><?php echo @lang('CAROUSEL_IMAGE'); ?></label></div>
									<div class="col-sm-10 ">
										<div class="custom-input">
											<input 
												class 		= "form-control" 
												type 		= "file"
												name 		= "image"
												required 	= "required" 
											></input>
											<span class="style"></span>
										</div>
									</div>
								</div>
								<!-- End Carousel Image  Input -->

								<input class="btn btn-primary btn-block btn-lg" type="submit" value="<?php echo @lang('ADD') ?>">
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php
		}elseif($do == 'Edit'){

			$carouselid = isset($_GET['carouselid']) ? $_GET['carouselid'] : 0; //get the carouselid
			
			/*
			** if the user clicked on edit btn then execute this statement
			** to update the comment data
			*/
			if($_SERVER['REQUEST_METHOD'] == 'POST'){

				$title 		= @trim($_POST['title']);
				$link 		= @$_POST['link'];
				$fileName 	= @$_FILES['image']['name']; 
				$fileSize 	= @$_FILES['image']['size'];
				
				if(! empty($fileName)){
					$fileTemp 	= @$_FILES['image']['tmp_name'];

					// check if this image is in allowed extension or not
					$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
					$allowedImages = array('png', 'jpg', 'jpeg', 'gif');
					if(! in_array($fileExtension, $allowedImages)){
						$formErrors[] = @lang('CAR_FILE_INVALID');
					}
				}

				$formErrors = array();  //initialize ForErrors array
				if(empty($title)) 				$formErrors[]  = @lang('CAROUSEL_TIT_EMPTY');
				if($fileSize > 10*1024*1024) 	$formErrors[]	= @lang('SIZE_EXCEEDED'); //if file exceeds 10MB

				if(empty($formErrors)){

					if(! empty($fileName)){
						$newName = encFileName($fileExtension); //just take the extension of the file and add random name for it
						$filePath = "../layout/images/background/" . $newName; // The Path Which Upload The Image To

						//remove the old image first
						$caro = selectItems('image', 'carousel', 'id = ?', array($carouselid));
						if(!empty($caro)){
							@unlink('../layout/images/background/' . $caro[0]['image']);
						}

						if(@move_uploaded_file($fileTemp, $filePath)){   //upload the image
							$update = updateItems('carousel', 'title = ?, link = ?, image = ?', array($title, $link, $newName, $carouselid), 'id = ?');
							$update > 0 ? $successMsg = @lang('CAR_EDITED_YES') : $formErrors[] = @lang('NO_CHANGE');
						}else{
							$formErrors[] = @lang('INVALID_UPLOAD');
						}
					}else{
							$update = updateItems('carousel', 'title = ?, link = ?', array($title, $link, $carouselid), 'id = ?');
							$update > 0 ? $successMsg = @lang('CAR_EDITED_YES') : $formErrors[] = @lang('NO_CHANGE');
					}

					
				}
			}

			$carousel = selectItems('*', 'carousel', 'id = ' . $carouselid);

			if(!empty($carousel)){ ?>

				<section>
					<!-- just to change the title tag content with this -->
					<span id="pageTitle" hidden=""><?php echo $carousel[0]['title'] ?></span>
					<h1 class="text-center"><?php echo lang('EDIT_CAR') ?></h1>	
					<div class="container">
						<div class="row">
							<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
								<div class="edit-form form-group">
									<!-- display the error or success messages -->
									<?php displayMsg(@$formErrors, @$successMsg); ?>

									<form action ='?do=Edit&carouselid=<?php echo $carouselid; ?>' method="POST" enctype="multipart/form-data">

										<!-- Start Carousel Title  Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><?php echo @lang('CAROUSEL_TITLE'); ?></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<input 
														class 		= "form-control" 
														type 		= "text"
														name 		= "title" 
														placeholder = "<?php echo @lang('PLACE_TITLE') ?>"
														value		= "<?php echo $carousel[0]['title']; ?>" 
														required 	=  "required"
														autofocus
													></input>
													<span class="style"></span>
												</div>
											</div>
										</div>
										<!-- End Carousel Title  Input -->

										<!-- Start Carousel Link  Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><?php echo @lang('CAROUSEL_LINK'); ?></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<input 
														class 		= "form-control" 
														type 		= "text"
														name 		= "link" 
														placeholder = "<?php echo @lang('PLACE_LINK'); ?>"
														value		= "<?php echo @$carousel[0]['link']; ?>"
													></input>
													<span class="style"></span>
												</div>
											</div>
										</div>
										<!-- End Carousel Link  Input -->

										<!-- Start Carousel Link  Input -->
										<div class="input-container">
											<div class="col-sm-2"><label><img class="img-responsive" id="change-img" src="../layout/images/background/<?php echo $carousel[0]['image'] ?>"></label></div>
											<div class="col-sm-10 ">
												<div class="custom-input">
													<input 
														class 		= "form-control" 
														type 		= "file"
														name 		= "image"
													></input>
													<span class="style"></span>
												</div>
											</div>
										</div>
										<!-- End Carousel Link  Input -->

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