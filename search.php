<?php
	ob_start();
	session_start();
	$pageTitle = 'Search Page';

	include_once 'init.php';

	//Get all lessons that matching the search

	$search = isset($_GET['search']) ? filter_var($_GET['search'], FILTER_SANITIZE_STRING) : '';

	$results = array(); //initialize the result array to be global
	if(!empty($search))
		$results = selectLessonsFiles('lessons.visible = ? AND lessons.name LIKE "%' . $search . '%"', array(0), 'lessons.id DESC');

?>

	<!-- Set The Page title in title Tag -->
	<span id="pageTitle" hidden><?php echo @lang('SEARCH')?> | <?php echo $search ?></span>

	<section class="search last-lessons lessons">
		<div class="header outer-header">
			<!-- just used for overllay -->
			<div class="lesson-overlay"></div>
			<h1 class="text-center"><?php echo $search ?></h1>
			
		</div>

		<?php if(!empty($results)){ ?>
			
			<div class="container">
				<div class="row">
					<?php foreach($results as $result){ ?>
						<div class="col-sm-6">
							<div class="lesson-info">
								<div class="row">
									<div class="col-sm-12 col-lg-4">
										<div class="header inner-header">
											<?php $image = getFileIcon(pathinfo($result['file'], PATHINFO_EXTENSION)); ?>
											<a href="lessons.php?lessonid=<?php echo $result['id'] ?>">
												<img class="img-responsive" src="layout/images/icons/<?php echo $image; ?>" draggable = "false">
											</a>
										</div>
									</div>
									<div class="col-sm-12 col-lg-8">
										<div class="body">
											<h3 title="<?php echo $result['name']; ?>">
												<a href="lessons.php?lessonid=<?php echo $result['id'] ?>"><?php echo $result['name']; ?></a>
											</h3>

											<div class="date"><i class="fa fa-calendar-alt fa-md fa-fw"></i> <?php echo @date('Y M j', strtotime($result['add_date'])); ?></div>
											<div class="user">
												<a href="profile.php?uid=<?php echo $result['userID']; ?>">
													<i class="fa fa-user fa-md fa-fw"></i> <?php echo $result['FullName'] ?>
												</a>
											</div>
											<div class="title">
												<a href="titles.php?titleid=<?php echo $result['id_title']; ?>">
													<i class="fa fa-book fa-md fa-fw"></i> <?php echo $result['title_name'] ?>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					<?php }?>
				</div>
			</div>
		<?php }else{?>
			<h3 class="alert alert-info text-center"><?php echo @lang('NO_RESULT_SEARCH'); ?></h3>
		<?php }?>
	</section>


<?php
	include_once $tpt_path . 'footer.php';
	ob_end_flush();
?>