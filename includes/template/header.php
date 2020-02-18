<?php
	include_once "init.php";

	$isAdmin = false; //used to check if the user admin or not 

	// mark notification as read if the user clicked on one of them
	if(isset($_GET['notification_id']) && !empty($_GET['notification_id'])){
		$notiID = is_numeric($_GET['notification_id']) ? intval($_GET['notification_id']) : 0;
		updateItems('notify', 'is_read = ?', array(1, $notiID), 'id = ?');
	}

?>

<!DOCKTYP html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="description" content="<?php echo @lang('META_DESCRIPTION') ?>">
	    <meta name="keywords" content="<?php echo @lang('META_KEYWORDS') ?>">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title><?php pageTitle(); ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="layout/images/website_icon.ico">
		<link rel="stylesheet" href="<?php echo $admin_css_path; ?>bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo $admin_css_path; ?>fontawesome-all.min.css">
		<link rel="stylesheet" href="<?php echo $admin_css_path; ?>jquery-ui.min.css">
		<link rel="stylesheet" href="<?php echo $admin_css_path; ?>jquery.selectBoxIt.css">
		<link rel="stylesheet" href="<?php echo $css_path; ?>frontend.css">
		<!-- set acarbic.css file -->
		<?php
			if(!isset($_COOKIE['lang']) || (isset($_COOKIE['lang'])  && $_COOKIE['lang'] == 'arabic.php')){?>
				<link rel="stylesheet" href="<?php echo $css_path; ?>arabic.css">
		<?php } ?>
		<link rel="stylesheet" href="<?php echo $css_path; ?>theme.css">
		<link rel="stylesheet" href="<?php echo $css_path; ?>mediaQuery.css">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
		<script src="<?php echo $admin_js_path; ?>jquery-3.2.1.min.js"></script>

		<script type="text/javascript">
	    	/* check if the user chosed a specific theme */
			if (localStorage['theme'] != null) {
				$('link[href*="theme"]').attr('href', localStorage['theme']);
		    }else{
		    	$('link[href*="theme"]').attr('href', 'layout/css/white__theme.css');
		    }

	    </script>

	</head>

	<body>

		<?php
			// Start Getting The Aatar Image If The User Logged In
			if(isset($_SESSION['user'])){
				$myImg = 'layout/images/profiles/avatar/default.png'; //the default image

				$profileImg = selectItems('image', 'users', 'id = ?', array($_SESSION['uid']));
				if(! empty($profileImg[0]['image'])){
					$myImg = 'layout/images/profiles/avatar/' . $profileImg[0]['image'];
				}
			}
		?>

		<!-- End Getting The Aatar Image If The User Logged In -->


		<!-- ***** Start The Menu ************ -->

		<div class="list-menu">
			<a href="index.php">
				<h3><?php echo @lang('WEBSITE_NAME') ? @lang('WEBSITE_NAME') : 'الفيزياء اللذيذة'; ?></h3>
			</a>
			<div class="personal">
				<!-- if the user didn't login -->
				<?php if(! isset($_SESSION['user'])){?>
					<div class="menu-login">
						<a href="login.php" title="<?php echo @lang('LOGIN_SIGNUP') ?>">
							<i class="fas fa-sign-in-alt fa-sm fa-fw"></i> <?php echo @lang('LOGIN') ?>
						</a>
					</div>
			    <?php }?>

				<!-- Show The Personal Links if the user login -->
				<?php if(isset($_SESSION['uid']) && isset($_SESSION['user'])){ 
					$admin = selectItems('admin', 'users', 'username = ?', array($_SESSION['user']));
				?>
					
				 <?php
	            	//display dashboard if the user is admin and its account link
		        	if(@$admin[0]['admin'] != 0){?>
		        		<a href="profile.php?uid=<?php echo @$_SESSION['uid'] ?>">
							<div class="my-account"><?php echo @lang('MY_ACCOUNT'); ?></div>
						</a>

		        		<a class="dashboard" href="admin/index.php"><div><?php echo @lang('DASHBOARD'); ?></div></a>

		        	<?php $isAdmin = true; //the user is admin
		        		if(!isset($_SESSION['username'])){
		        			$_SESSION['username'] = $_SESSION['user'];
		        			$_SESSION['userid'] = $_SESSION['uid'];
		        			$_SESSION['admin'] = $admin[0]['admin'];
		        		}
					}
				?>

				<a href="settings.php">
					<div class="settings"><?php echo @lang('SETTINGS'); ?></div>
				</a>

				<a href="logout.php">
					<div class="logout"><?php echo @lang('LOGOUT'); ?></div>
				</a>

				<?php }?>

			</div>
			<ul class="parent-title-con list-unstyled">
				<?php
					// Get The Titles From The Database
					$parentTitles = selectItems('id, name', 'titles', 'parent = ?', array(0), 'ordering DESC'); //parent Titles
					$childTitles = selectItems('id, name, parent', 'titles', 'parent != ?', array(0));  //Get Child Titles

					foreach ($parentTitles as $parentTitle) {?>
						<li class="parent-title">
							<span>
								<?php echo $parentTitle['name'] ?>
								<i class="fa fa-angle-right fa-sm fa-fw"></i>
							</span>
							<ul class="child-title-con list-unstyled">
								<?php
									//check if this parent title has childs or not if exist diplay it
									foreach ($childTitles as $childTitle) {
										if($childTitle['parent'] == $parentTitle['id']){?>
											<a href="titles.php?titleid=<?php echo $childTitle['id']; ?>">
												<li class="child-title"><?php echo $childTitle['name'] ?></li>
											</a>
										<?php }
									}
								?>
							</ul>
						</li>
					<?php }

				?>
			</ul>

			<!-- language -->
			<div class="lang">
				<a id = "arabicBtn" href = "?changeLang=arabic" data-value = "arabic.css">عربى </a>
				<a id = "englishBtn" href = "?changeLang=english" data-value = "frontend.css">English</a>
			</div>
		</div>

		<!-- ***** End The Menu ************ -->

		<!-- ****** Start The Body -->

		<!-- this section will hold the whole body of the page and use it when click on menu btn -->
		<section class="body-container">

			<!-- Overllay just used to overlay the body when click on the list menu -->
			<div class="overlay"></div>

			<!-- Start New Message Section -->
			<div class="new-msg-field">
				<!-- Overlay of the section -->
				<span class="msg-overlay"></span>

				<div class="new-msg-con">
					<form class="form-group" method="POST" action="query.php">
						<div class="new-msg-header">
							<span><i class="fa fa-edit fa-sm"></i> <?php echo @lang('NEW_MESSAGE') ?></span>
							<span id="new-msg-close"><i class="fa fa-times fa-md"></i></span>
						</div>
						<div class="new-msg-tags">
							<span><i class="far fa-user fa-sm"></i> <?php echo @lang('TO') ?></span>
							<span>
								<!-- holds the friends tags -->
								<div id="friends-tags"></div>

								<input class="form-control search-input" type="text" autocomplete="off" placeholder="<?php echo @lang('NAME') ?>">
								<button class="btn btn-default btn-sm" type="button"><?php echo @lang('SEARCH') ?></button>
								<!-- holds the result search -->
								<div id="friendResult"></div>
							</span>
						</div>
						<div class="new-msg-text">
							<textarea class="form-control" name="msg" placeholder="<?php echo @lang('WRITE_MSG') ?>"></textarea>
						</div>
						<div class="new-msg-footer">
							<button class="btn btn-primary btn-sm" type="submit"><?php echo @lang('SEND'); ?></button>
							<button class="btn btn-success btn-sm" style="display: none;" type="hidden" disabled="disabled"><i class="fa fa-check fa-xs"></i> <?php echo @lang('SENT'); ?></button>
						</div>
					</form>
				</div>
			</div>
			<!-- End New Message Section -->

			<nav class="navbar navbar-inverse navbar-fixed-top">
				<div class="container">
				    <div class="navbar-header">
				      <a class="navbar-brand" href="index.php">
				      	<span><?php echo @lang('WEBSITE_NAME'); ?></span>
				      </a>
				    </div>

				    <!-- Menu button -->
				    <div class="menu nav navbar-nav navbar-right"><i class="fa fa-ellipsis-v fa-lg"></i></div> <!-- this span in <li> used for making the bars of the menu -->

				    <!-- Start The Profile DropDown Menu -->
			      	<ul class="dropdown-pro nav navbar-nav navbar-right">
			      		<?php if(isset($_SESSION['user'])){ ?>
			      			<!-- Start Mesages Menu -->
			      			<li class="msg dropdown  custom-dropdown">
			      				<?php $lastMsg = getMessages('messages.reciever_id = ?', array(@$_SESSION['uid']), 'messages.update_date DESC'); ?>
					        	
					        	<i class="fab fa-facebook-messenger fa-md fa-fw" data-toggle="dropdown" title="<?php echo @lang('NAV_MSG') ?>"></i>
					        	<ul class="dropdown-menu">
					        		<li class="msg-header">
						        		<span><?php echo @lang('MESSAGE') ?></span>
						        		<span id="new-msg"><?php echo @lang('NEW_MESSAGE') ?></span>
						        	</li>
					        		<?php if(!empty($lastMsg)){ ?>
						        		<li>
						        			<ul class="dropdown-menu-con list-unstyled">
							        			<?php foreach($lastMsg as $msg){
							        				//get the last conversation between me and my frind if i wrote it or he wrote it
								        			$lastTalk = selectItems('add_date, sentence', 
								        							'conversation', '(sender_id = ? AND reciever_id = ?) OR (sender_id = ? AND reciever_id = ?)', 
								        							array($msg['sender_id'], $_SESSION['uid'], $_SESSION['uid'], $msg['sender_id']), 'id DESC', 1);
								        		?>
							        			<?php if(!empty($lastTalk)){ ?>
							        				<li>
							        					<a class = "<?php if($msg['reciever_read'] == 0) echo 'unread'; ?>" href = "msg.php?sender_id=<?php echo $msg['sender_id'] ?>&msgID=<?php echo $msg['id'] ?>" target 	= "_blank">
								        					<span class="sender-img"><img src="layout/images/profiles/avatar/<?php echo empty($msg['image']) ? 'default.png' : $msg['image'] ?>"></span>
								        					<span class="sender-p">
								        						<div><strong><?php echo $msg['FullName'] ?></strong></div>
								        						<div class="p-txt"><?php echo filter_var(@$lastTalk[0]['sentence'], FILTER_SANITIZE_STRING) ?></div>
								        						<div>
								        							<span class="file-date"><i class="far fa-clock"></i> <bdi><?php echo date('h:ma', strtotime(@$lastTalk[0]['add_date'])) ?></bdi></span>
										        					<span class="file-date"><i class="far fa-calendar"></i> <bdi><?php echo date('d M', strtotime(@@$lastTalk[0]['add_date'])) ?></bdi></span>
								        						</div>
								        								
								        					</span>
								        					
								        				</a>
								        			</li>
							        			<?php } } ?>
							        		</ul>
						        		</li>
						        	<?php }else{ ?>
						        		<li class="empty-msg"><?php echo @lang('EMPTY_MSG_MENU') ?></li>
						        	<?php }?>
						        	<li class="msg-footer">
						        		<span class="msg-as-read" data-id="<?php echo @$_SESSION['uid'] ?>"><?php echo @lang('MARK_AS_READ') ?></span>
						        		<span><!-- <a href="msg.php" target="_blank"><?php echo @lang('SEE_IN_MESSENGER') ?></a> --></span>
						        	</li>
					        	</ul>
					        </li>
			      			<!-- End Mesages Menu -->

			      			<!-- Start Notification Menu -->

			      			 <li class="notify custom-menu  custom-dropdown">
					        	<?php 
					        		//Get the Last 10 Notifivcations
					        		$lastNotify = getNotification('notify.reciever = ?', array(@$_SESSION['uid']), 'notify.id DESC', 10);
					        	?>
					        	<i class="far fa-bell fa-md fa-fw" data-toggle="dropdown" title="<?php echo @lang('NAV_NOTIFY') ?>"></i>
					        	
					        	<?php 
					        		$unreadCount = selectItems('COUNT(is_read) AS unread_count', 'notify', 'reciever = ? AND is_read = ?', array($_SESSION['uid'], 0));
					        		if(!empty($unreadCount) && @$unreadCount[0]['unread_count'] != 0){ ?>
					        		<span class="unread-notify"><?php echo @$unreadCount[0]['unread_count'] > 9 ? '9+' : @$unreadCount[0]['unread_count']; ?></span> <!-- use it to dis[ply the number of unread notifications -->
					        	<?php }?>
					        	
					        	<ul class="dropdown-menu">
					        		<li class="mark-as-read"><span data-id="<?php echo @$_SESSION['uid'] ?>"><?php echo @lang('MARK_AS_READ') ?></span></li>
					        		<li>
					        			<ul class="dropdown-menu-con list-unstyled">
					        				<?php if(!empty($lastNotify)){ 
					        					$minID = INF; //initialize the last id of notification
						        				foreach($lastNotify as $notify) { 
						        					//get the last id DESC to set it into show-more btn
							        				$minID = $notify['id'] < $minID ? $notify['id'] : $minID; 

						        					// <!-- Start Display the Comments notifications -->
						        					$senderImg = !empty($notify['image']) ? $notify['image'] : 'default.png';

						        					if($notify['type'] == 'comment'){ ?>
									        			<li class="lesson-con <?php if($notify['is_read'] == 0) echo 'unread'; ?>">
									        				<a href="lessons.php?lessonid=<?php echo $notify['lesson_id'] ?>&notification_id=<?php echo $notify['id'] ?>">
									        					<span class="sender-img"><img src="layout/images/profiles/avatar/<?php echo $senderImg ?>"></span>
									        					<span class="sender-p">
									        						<p><?php echo @lang('COMMENTER_NAME', $notify['FullName']) . '<strong>' . $notify['lessonName'] . '</strong>' ?></p>
									        						
									        						<div>
									        							<span class="file-date"><i class="far fa-clock"></i> <?php echo date('h:m', strtotime($notify['add_date'])) ?></span>
									        							<span class="file-date"><i class="far fa-calendar"></i> <?php echo date('d M', strtotime($notify['add_date'])) ?></span>
									        						</div>
									        					</span>
										        			</a>
									        			</li>

									        		<!-- Start Display the lessons notifications -->
						        					<?php }elseif($notify['type'] == 'lesson'){ ?>
									        			<li class="lesson-con <?php if($notify['is_read'] == 0) echo 'unread'; ?>">
									        				<a href="lessons.php?lessonid=<?php echo $notify['lesson_id'] ?>&notification_id=<?php echo $notify['id'] ?>">
									        					<span class="sender-img"><img src="layout/images/profiles/avatar/<?php echo $senderImg ?>"></span>
									        					<span class="sender-p">
									        						<p><?php echo @lang('SENDER_NAME', $notify['FullName'], $notify['lessonName']) . '<strong>' . $notify['titleName'] . '</strong>' ?></p>
									        						
									        						<div>
									        							<?php $fileExtension = strtolower(pathinfo($notify['file'], PATHINFO_EXTENSION)); ?>
									        							<span class="file-img"><img src="layout/images/icons/<?php echo getFileIcon($fileExtension); ?>"></span>
									        							<span class="file-date"><i class="far fa-clock"></i> <?php echo date('h:m', strtotime($notify['add_date'])) ?></span>
									        							<span class="file-date"><i class="far fa-calendar"></i> <?php echo date('d M', strtotime($notify['add_date'])) ?></span>
									        						</div>
									        					</span>
										        			</a>
									        			</li>
							        		<?php } 

							        		} ?>
							        			<!-- show more notifications data-member="<?php echo $_SESSION['uid']; ?>" -->
							        			<li 
							        				class="show-more" 
							        				data-id="<?php echo $minID ?>" 
							        			>
							        				<?php echo @lang('SHOW_MORE') ?>		
							        			</li>

							        		<?php }else{
							        		?>
							        			<li><a class="empty-notify"><?php echo @lang('EMPTY_NOTIFY'); ?> </a></li>
							        		<?php }?>
					        			</ul>
					        			
					        		</li>
					        	</ul>
					        </li>
			      			<!-- End Notification Menu -->

			      			<!-- Start Profile Menu  -->
					        <li class="dropdown custom-dropdown">
					          <img class="img-responsive img-profile" src="<?php echo $myImg; ?>" data-toggle="dropdown">

					          <ul class="dropdown-menu">
					            <li>
					            	<?php $fullname = selectItems('FullName', 'users', 'id = ?', array($_SESSION['uid'])); 

					            	if(isset($isAdmin) && $isAdmin == true){ ?>
						            	<a href="profile.php?do=Manage&uid=<?php echo $_SESSION['uid']; ?>"> <?php echo $fullname[0]['FullName']; ?> </a>
						            <?php }else{ ?>
						            	<a> <?php echo $fullname[0]['FullName']; ?></a>
						            <?php }?>
					            </li>

					            <?php
						        	if(isset($isAdmin) && $isAdmin == true){  //the user is admin
						        		echo '<li><a class="dashboard" href="admin/index.php">' . @lang('DASHBOARD') . '</a></li>';
									}
								?>

					            <li><a href="settings.php"><?php echo lang('SETTINGS'); ?></a></li>
					            <li class="navbar-lang">
					            	<a href="search.php?changeLang=english">English</a> | 
					            	<a href="search.php?changeLang=arabic">العربية</a></li>
					            <li role="separator" class="divider"></li>
					            <li><a href="logout.php"> <?php echo lang('LOGOUT'); ?> </a></li>
					          </ul>
					        </li>
					        <!-- End Profile Menu -->
				        <?php }?>
				        
				        <li class="search-btn visible-xs"><i class="fa fa-search fa-lg fa-fw"></i></li>
				    </ul>
				      <!-- End The Profile DropDown Menu -->

				    <!-- Start Collecting All Menus That Will be in the button when the screen be for mobiles -->
				    <div class="collapse navbar-collapse" id="target-app">
				    	<!-- Start Menu Btn -->
				    	<ul class="nav navbar-nav navbar-right">
				    		<?php if(! isset($_SESSION['user'])){?>
					    		<li class="login-signup">
						        	<!-- show login link if the user dosen't login -->
									<span title="<?php echo @lang('LOGIN_SIGNUP') ?>"><a href="login.php"><i class="fas fa-sign-in-alt fa-sm fa-fw"></i> <?php echo @lang('LOGIN') ?></a></span>
						        </li>
					        <?php }?>

				      	</ul>


				    	<!-- End Menu Btn -->



					    <!-- Start The Titles DropDown Menu -->
				 	     <?php
				 	     	// Get The Titles From The Database							
							$parentTitles = selectItems('id, name', 'titles', 'parent = ? AND visible = ?', array(0, 0), 'ordering DESC'); //parent Categiories
							$childTitles = selectItems('id, name, parent', 'titles', 'parent != ?', array(0));  //Get Child Categories

							//get the id's of the first five parents
							$getIds = array(); //initialize the array
							$counter = 1; //stop the foor loop after 5 iterations
							foreach ($parentTitles as $parentTitle) {
								if($counter > 5) break; else $counter++;
								$getIds[] = $parentTitle['id'];
							?>
								<div class="dropdown navbar-right custom-dropdown">
									 <span id="dLabel" role="button" data-toggle="dropdown" ><?php echo $parentTitle['name']; ?> <i class="fa fa-angle-down"></i></span>

									 <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									    <?php foreach($childTitles as $childTitle){
									    	if($childTitle['parent'] == $parentTitle['id']){
									    ?>
									    		<li><a href="titles.php?titleid=<?php echo $childTitle['id'] ?>"><?php echo $childTitle['name']; ?></a></li>
									    <?php }}?>
									 </ul>
								</div>

						<?php }
							//display More Menu that holds the rest parent titles
							if(@count($parentTitles) > 5){ ?>
								<div class="dropdown navbar-right custom-dropdown">
									<span id="dLabel" role="button" data-toggle="dropdown" ><?php echo @lang('MORE'); ?> <i class="fa fa-angle-down"></i></span>

									<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									    <?php foreach($parentTitles as $parentTitle){
									    	if(in_array($parentTitle['id'], $getIds)) continue;
									    ?>
									    		<li><a href="titles.php?titleid=<?php echo $parentTitle['id'] ?>"><?php echo $parentTitle['name']; ?></a></li>
									    <?php }?>
									</ul>
								</div>
						<?php }?>

						

				    	<!-- End The Titles DropDown Menu -->

				    	 <!-- Start The Dashboard Link -->
					    <ul class="nav navbar-nav navbar-right">

					    	<li class="search-btn"><i class="fa fa-search fa-lg fa-fw"></i></li>

					        <?php
					        	if(isset($isAdmin) && $isAdmin == true){
					        		echo '<li><a class="dashboard nav-dashboard" href="admin/index.php">' . @lang('DASHBOARD') . '</a></li>';
								}
							?>
					    </ul>
					    <!-- End The Dashboard Link -->

				    </div>
				    <!-- End Collecting All Menus That Will be in the button when the screen be for mobiles -->
				</div>
			</nav>

			<!-- Start lower-nav section -->
			<div class="lower-nav">
				<!-- Search Section -->
				<div class="search">
					<div class="inneroverlay"></div>
					<div class="container">
						<form class="form-group" action="search.php" method="GET">
							<div class="custom-input">
								<input 
									class 		 = "form-control" 
									type 		 = "search" 
									name 		 = "search"
									autocomplete = "off"
									placeholder = "<?php echo @lang('PLACE_SEARCH') ?>" 
								>
								<span class="style"></span>
							</div>
						</form>
					</div>
					
				</div>
			</div>
			<!-- Ens lower-nav section -->
