<?php
	ob_start();
	session_start();
	$pageTitle = 'Titles Page';

	include_once 'init.php';

	//get the titleid
	$titleid = isset($_GET['titleid']) && is_numeric($_GET['titleid']) ? $_GET['titleid'] : 0;

	//if the titleid == 0 then redirect to index page
	if($titleid == 0){
		header('location: index.php');
		exit();
	}

	//get the name of this title
	$titlePage = selectItems('name', 'titles', 'id = ?', array($titleid));

	//get all titles of this titleid
	$allTitles = selectItems('id, name, member_id', 'titles', 'parent = ? AND visible = ?', array($titleid, 0));

	//get all lessons of this titlesid
	$allLessons = selectItems('id, name, file, add_date, member_id', 'lessons', 'title_id = ? AND visible = ?', array($titleid, 0), 'ordering DESC');

?>
	
	<!-- Set The Page title in title Tag -->
	<span id="pageTitle" hidden><?php echo @$titlePage[0]['name']; ?></span>
 
	<section class="titles">

		<div class="header">
			<!-- just used for overllay -->
			<div class="title-overlay"></div>
			<h1 class="text-center"><?php echo @$titlePage[0]['name']; ?></h1>
		</div>

		<!-- Start path bar section  -->
		<?php $material = getParentTitle($titleid, 0, 'titles');  //get the material name of this lesson?>
		<div class="path-bar">
			<ol class="breadcrumb"> 
				<li><a href="index.php"><?php echo @lang('HOME') ?></a></li>
				<?php for($i = count($material) - 1; $i > 0; $i--){ 
						if($material[$i]['parent'] != 0){ ?>
							<li><a href="titles.php?titleid=<?php echo $material[$i]['id']; ?>"><?php echo $material[$i]['name']; ?></a></li>
				<?php }} ?>
				<li class="active"><?php echo @$titlePage[0]['name'] ?></li>
			</ol>
		</div>
		<!-- End path bar section  -->

		<div class="container">

			<!-- display the folders and files -->
			<div class="folder-file">
				<ul class="list-unstyled">
					<li class="grid">
						<span class="back" id="goBack" title="<?php echo @lang('BACK'); ?>"><?php echo @lang('BACK'); ?> <i class="fa fa-angle-left fa-md fa-fw"></i></span>
						
						<span class="grid-icons">
							<i class="fas fa-th-large fa-md fa-fw grid" data-value="gird-view" title="Grid view"></i>
							<i class="fas fa-list fa-md fa-fw list" data-value="list" title="List view"></i>
						</span>
					</li>
					
					<div class="grid-view">
						<!-- Display All Titles  -->
						<?php if(!empty($allTitles)){ ?>
							<div>
								<h4><?php echo @lang('FOLDER_TITLE') ?></h4>
								<?php foreach ($allTitles as $title) {?>
									<a class="folder" href="?titleid=<?php echo $title['id'] ?>" title="<?php echo $title['name'] ?>">
										<li class="text-center">
											<div><img draggable="false" src="layout/images/icons/folder.png"></div>
											<span class="icon-name"><?php echo $title['name'] ?></span>
										</li>
									</a>
								<?php } ?>
							</div>
						<?php }?>

						<?php if(!empty($allLessons)){ ?>
							<div>
								<h4><?php echo @lang('FILE_TITLE') ?></h4>
								<!-- Display All Files -->
								<?php foreach ($allLessons as $lesson) {
									//check the extension of the image
									$fileExtension 	= strtolower(pathinfo($lesson['file'], PATHINFO_EXTENSION));
									$fileName 		= getFileIcon($fileExtension); //get the file name
								?>
									<a class="file" href="lessons.php?lessonid=<?php echo $lesson['id'] ?>" title="<?php echo $lesson['name'] ?>">
										<li class="text-center">
											<?php if(in_array($fileExtension, array('png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG', 'gif', 'GIF'))){ ?>
												<div><img draggable="false" src="data/files/<?php echo $lesson['file'] ?>" ></div>
											<?php }else{?>
												<div><img draggable="false" src="layout/images/icons/<?php echo $fileName ?>" ></div>
											<?php }?>
											
											<span class="icon-name"><?php echo $lesson['name'] ?></span>
										</li>
									</a>
								<?php } ?>
							</div>
						<?php }?>
					</div>

					<div class="list-view li-view">
						<div class="list-view-con">
							<div class="table-responsive">
								<table class="table">
									<div class="list-head">
										<tr>
											<td><?php echo @lang('LIST_NAME') ?></td>
											<td><?php echo @lang('LIST_TYPE') ?></td>
											<td><?php echo @lang('LIST_OWNER') ?></td>
											<td class="list-date"><?php echo @lang('LIST_DATE') ?></td>
										</tr>
									</div>

									<div class="list-body">
										<!-- Start display the folders -->
										<!-- Display All Titles  -->
										<?php foreach ($allTitles as $title) {?>
											<tr>
												<td>
													<a class="list-folder" href="?titleid=<?php echo $title['id'] ?>" title="<?php echo $title['name'] ?>">
														<img draggable="false" src="layout/images/icons/folder.png">
														<span class="list-icon-name"><?php echo $title['name'] ?></span>
													</a>
												</td>
												<td class="list-date">-</td>

												<?php $uploader = selectItems('FullName', 'users', 'id = ?', array($title['member_id'])); ?>
												<td class="list-owner"><a href="profile.php?uid=<?php echo $title['member_id'] ?>"><?php echo $uploader[0]['FullName'] ?></a></td>
												<td class="list-date">-</td>
											</tr>

										<?php } ?>
										<!-- End display the folders -->


										<!-- Lessons files -->
										
										<!-- Start display the files -->
										<!-- Display All Files -->
										<?php foreach ($allLessons as $lesson) {
											//check the extension of the image
											$fileExtension 	= strtolower(pathinfo($lesson['file'], PATHINFO_EXTENSION));
											$iconName 		= getFileIcon($fileExtension); //get the file name
										?>
											<tr>
												<td>
													<a class="list-file" href="lessons.php?lessonid=<?php echo $lesson['id'] ?>" title="<?php echo $lesson['name'] ?>">
														<img draggable="false" src="layout/images/icons/<?php echo $iconName ?>" >
														<span class="list-icon-name"><?php echo $lesson['name'] ?></span>
													</a>
												</td>
												<td class="list-date"><?php echo $fileExtension?></td>

												<?php $uploader = selectItems('FullName', 'users', 'id = ?', array($lesson['member_id'])); ?>
												<td class="list-owner"><a href="profile.php?uid=<?php echo $title['member_id'] ?>"><?php echo $uploader[0]['FullName'] ?></a></td>
												<td class="list-date"><?php echo @date('d M Y', @strtotime(@$lesson['add_date'])) ?></td>

											</tr>

										<?php } ?>
										<!-- End display the files -->
									</div>
									
								</table>
							</div>
								
						</div>
					</div>

					<!-- if the folder empty then print empty message -->
					<?php if(empty($allTitles) && empty($allLessons)){?>
						<div class="alert alert-info text-center"><?php echo @lang('EMPTY_TITLE'); ?></div>
					<?php }?>

				</ul>
			</div>
		</div>
	</section>


<?php
	include_once $tpt_path . 'footer.php';
	ob_end_flush();
?>