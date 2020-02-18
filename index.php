<?php
	ob_start();

	session_start();

	$pageTitle = "عباقرة الرياضيات ";

	include_once "init.php";  //include the initialize file

	// Get The News bar
	$newsBar = selectItems('*', 'bar', 'is_thanks = ?', array(1));

	//get the carousel
	$allCarousels = selectItems('*', 'carousel', 1, array(), 'id DESC');

	//get the personal info of Mr farag
	$mrFarag = selectItems('*', 'users', 'admin = ? AND owner = ?', array(1, 1));

	//Get The last 10 lessons added
	$lessonsCount = 8;
	// $lastLessons = selectItems('*', 'lessons', 'visible = ?', array(0), 'id DESC', $lessonsCount);
	$lastLessons = selectLessonsFiles('lessons.visible = ?', array(0), 'lessons.id DESC', $lessonsCount);

	// Get the amount comments, lessons, Memebers from the Database
	$commentsNum 	= selectItems('COUNT(id) AS count', 'comments');
	$usersNum 		= selectItems('COUNT(id) AS count', 'users');
	$lessonsNum 	= selectItems('COUNT(id) AS count', 'lessons');
?>

<!-- Start intro website section -->
<section class="index-header">
	<h1 class="text-center" data-value="<?php echo @lang('WEBSITE_NAME'); ?>"></h1>
	<div class="inner text-center">
		<div class="line-1" data-value="<?php echo @lang('INDEX_LINE1') ?>"></div>
		<div class="line-2" data-value="<?php echo @lang('INDEX_LINE2') ?>"></div>
		<div class="line-3" data-value="<?php echo @lang('INDEX_LINE3') ?>"></div>
	</div>
	<div class="see-more text-center">
		<a href="#seeMore">
			<i class="fas fa-angle-double-down fa-2x"></i> <br>
			<?php echo @lang('SEE_MORE'); ?>
		</a>
	</div>

</section>
<!-- End intro website section -->

<!-- Start Header Section -->
<section class="index" id="seeMore">
	<div class="container">

		<!-- Start Carousel Section -->
		<?php if(!empty($allCarousels)){ ?>
			<div class="carousel-head">
				<div id="carousel-gen" class="carousel slide" data-ride="carousel">
					<!-- Wrapper for slides -->
					<div class="carousel-inner">
					    <?php
					    $counter = 1;
					    foreach($allCarousels as $carousel){?>
					    	<div class="item <?php if($counter == 1) echo 'active'; $counter++;?>">
							   	<img src="layout/images/background/<?php echo $carousel['image'] ?>" alt="Image">
							    <div class="carousel-caption">
							        <p><?php echo $carousel['title']; ?></p>
							        <a 
							        	href="<?php echo empty($carousel['link']) ? '#' : $carousel['link']; ?>" 
							        	class="btn btn-primary btn-sm" 
							        	target="<?php echo empty($carousel['link']) ? '' : '_blank' ?>" 
							        	<?php if(empty($carousel['link'])) echo 'disabled="disabled"'; ?>
							        ><?php echo @lang('READ_MORE') ?></a>
							    </div>
						    </div>
					    <?php }?>

					</div>

					<!-- Controls -->
					<a class="left carousel-control" href="#carousel-gen" data-slide="prev">
					    <span class="glyphicon glyphicon-chevron-left"></span>
					</a>
					<a class="right carousel-control" href="#carousel-gen" data-slide="next">
					    <span class="glyphicon glyphicon-chevron-right"></span>
					</a>
				</div>
			</div>
		<?php }?>
		<!-- End Carousel Section -->
	</div>

	<?php if(!empty($newsBar)){ ?>
		<!-- Start News Bar  -->
		<div class="bar-section">
			<div class="container">
				<div class="parent-bar news-bar">
					<span id="time" class="bar-logo"></span>

					<span class="bar-word">
						<ul class="list-unstyled">
							<?php foreach ($newsBar as $bar) {?>
								<li><?php echo $bar['sentence']; ?></li>
								<img class="" src="layout/images/website_icon.ico">
							<?php } ?>
						</ul>
					</span>
				</div>
			</div>
		</div>
		<!-- End News Bar  -->
	<?php }?>
</section>
<!-- End Header Section -->

<!-- Start The state of the website section -->

<section class="home-state">
	<div class="container">
		<div class="row">
			<!-- The Members Count -->
			<div class="col-md-4">
				<div class="content user">
					<div class="row">
						<div class="col-xs-8">
							<div class="body">
								<div><?php echo @lang('HOME_MEM') ?></div>
								<div><?php echo !empty($usersNum[0]['count']) ? $usersNum[0]['count'] : '0'; ?></div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="font">
								<span><i class="far fa-user"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- The Comments Count -->
			<div class="col-md-4">
				<div class="content comment">
					<div class="row">
						<div class="col-xs-8">
							<div class="body">
								<div><?php echo @lang('HOME_COM') ?></div>
								<div><?php echo !empty($commentsNum[0]['count']) ? $commentsNum[0]['count'] : '0'; ?></div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="font">
								<span><i class="far fa-comments"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- The Lessons Count -->
			<div class="col-md-4">
				<div class="content lesson">
					<div class="row">
						<div class="col-xs-8">
							<div class="body">
								<div><?php echo @lang('HOME_LESSON') ?></div>
								<div><?php echo !empty($lessonsNum[0]['count']) ? $lessonsNum[0]['count'] : '0'; ?></div>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="font">
								<span><i class="far fa-file"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>

<!-- End The state of the website section -->


<!-- Start Teh Last Lessons Added -->
<?php if(!empty($lastLessons)){ ?>
	<section class="last-lessons">
		<div class="flag"><i class="fa fa-book"></i></div>
		<h2><span><?php echo @lang('LAST_LESSONS') ?></span></h2>
		<div class="container">
			<div class="row">
				<?php foreach($lastLessons as $lastLesson){ ?>
					<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
						<div class="lesson-info">
							<div class="row">
								<div class="col-sm-12">
									<div class="header">
										<?php $image = getFileIcon(pathinfo($lastLesson['file'], PATHINFO_EXTENSION)); ?>
										<a href="lessons.php?lessonid=<?php echo $lastLesson['id'] ?>">
											<img class="img-responsive" src="layout/images/icons/<?php echo $image; ?>" draggable = "false">
										</a>
									</div>
								</div>
								<div class="col-sm-12">
									<div class="body">
										<h3 title="<?php echo $lastLesson['name']; ?>">
											<a href="lessons.php?lessonid=<?php echo $lastLesson['id'] ?>"><?php echo $lastLesson['name']; ?></a>
										</h3>

										<div class="date"><i class="fa fa-calendar-alt fa-md fa-fw"></i> <?php echo @date('Y M j', strtotime($lastLesson['add_date'])); ?></div>
										<div class="user">
											<a href="profile.php?uid=<?php echo $lastLesson['userID']; ?>">
												<i class="fa fa-user fa-md fa-fw"></i> <?php echo $lastLesson['FullName'] ?>
											</a>
										</div>
										<div class="title">
											<a href="titles.php?titleid=<?php echo $lastLesson['id_title']; ?>">
												<i class="fa fa-book fa-md fa-fw"></i> <?php echo $lastLesson['title_name'] ?>
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
	</section>
<?php }?>
<!-- End Teh Last Lessons Added -->


<script type="text/javascript">
	var win 		= $(window),
		winWidth 	= win.outerWidth(),
		winHeight 	= win.outerHeight();

	//move the page down when click on see more btn
   	var seeMore = $('.index-header .see-more a');
   	seeMore.click(function () {
   		$('html, body').animate({
   			scrollTop: $('#seeMore').offset().top
   		}, 1000);
   	});	

	var navbar 				= $('.navbar-inverse'),
		indexHeader 		= $('.index-header'),
		brand 				= $('.navbar>.container .navbar-brand');
	//	navbarLinks			= $('.dashboard, div.custom-dropdown, .navbar .search-btn, .navbar .menu, .navbar .notify, .navbar .msg');

	//change the background of the page when the navbar when scrolling
	if(!(navbar.offset().top + navbar.height() > indexHeader.offset().top + indexHeader.height())){
			navbar.addClass('inactive-navbar');
			brand.addClass('inactive-brand');
			//change the color of navbar
			//navbarLinks.addClass('inactive-colors');
	}

	win.scroll(function(){
		if(navbar.offset().top + navbar.height() > indexHeader.offset().top + indexHeader.height()){
			navbar.removeClass('inactive-navbar');
			brand.removeClass('inactive-brand');
			//change the color of navbar
			//navbarLinks.removeClass('inactive-colors');

		}else{
			navbar.addClass('inactive-navbar');
			brand.addClass('inactive-brand');
			//change the color of navbar
			//navbarLinks.addClass('inactive-colors');
		}
	});

	var siteHeader 	= indexHeader.find('h1'),
		siteVal 	= siteHeader.attr('data-value'),
		line1 		= indexHeader.find('.line-1'),
		line2 		= indexHeader.find('.line-2'),
		line3 		= indexHeader.find('.line-3'),
		line1Val 	= line1.attr('data-value'),
		line2Val 	= line2.attr('data-value'),
		line3Val 	= line3.attr('data-value');

		//start writing the hedaer lines when the page load
		var i = 0, a=0,b=0,c=0;
		//write the header
		setInterval(function () {
			if(i < siteVal.length){ //write the h1 header
				siteHeader.text(siteHeader.text() + siteVal[i]);
				i++;
			}
			
		}, 100);
		//write the lines
		setInterval(function () {
			if(i < siteVal.length);//wait untile the header writes
			else if(a < line1Val.length){ //write line 1
				line1.text(line1.text() + line1Val[a]);
				a++;
			}
			else if(b < line2Val.length){ //write line 1
				line2.text(line2.text() + line2Val[b]);
				b++;
			}
			else if(c < line3Val.length){ //write line 1
				line3.text(line3.text() + line3Val[c]);
				c++;
			}
			else if(c == line3Val.length){
				seeMore.fadeIn(1000);
			}
		}, 60);

		// siteHeader.text(siteHeader.attr('data-value'));

	// Start index-header section

   	//get full screen height in the home page
   	var navHeight = $('.navbar-inverse').outerHeight();
   	indexHeader.height(winHeight).css('margin-top', -navHeight); 

   	// End index-header section

</script>
<?php
	include_once $tpt_path ."footer.php";
	ob_end_flush();
?>
