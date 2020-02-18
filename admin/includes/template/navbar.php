<nav class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#target-app" aria-expanded="false">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="../index.php">
      	<span>عباقرة الرياضيات</span>
      </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="target-app">
      <ul class="nav navbar-nav">
        <li><a href="dashboard.php"><?php echo lang('DASHBOARD'); ?></a></li>
        <li><a href="titles.php"><?php echo lang('TITLES'); ?></a></li>
        <li><a href="lessons.php"><?php echo lang('LESSONS'); ?></a></li>
        <?php if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1) { //show the member bar just for the admin?>
          <li><a href="members.php"><?php echo lang('MEMBERS'); ?></a></li> 
        <?php }?>
        <li><a href="comments.php"><?php echo lang('COMMENTS'); ?></a></li>

        <?php if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1) { //show the member bar just for the admin?>
        <li class="dropdown custom-dropdown">
          <a href="#" id="dLabel" role="button" data-toggle="dropdown" ><?php echo lang('MORE'); ?><i class="fa fa-angle-down"></i></a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <li><a href="bar.php"><?php echo lang('BAR'); ?></a></li> 
            <li><a href="carousel.php"><?php echo lang('CAROUSEL'); ?></a></li> 
          </ul>
        </li>
        <?php }?>

      </ul>

      
      
      <?php if(isset($_SESSION['username'])){ ?>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <?php 
                $myprofile = selectItems('image', 'users', 'id = ?', array($_SESSION['userid']));
                $image =  empty($myprofile[0]['image']) ? 'default.png' : $myprofile[0]['image']; ?>
            <img class="img-responsive img-profile" src="../layout/images/profiles/avatar/<?php echo @$image; ?>" data-toggle="dropdown">

            <ul class="dropdown-menu">
              <li><a href="../index.php"><i class="fa fa-home fa-fw fa-xs"></i> <?php echo lang('HOMEPAGE'); ?></a></li>
              <li><a href="../settings.php"><i class="fa fa-edit fa-fw fa-xs"></i> <?php echo lang('EDITPROFILE'); ?></a></li>
              <li><a href="../settings.php"><i class="fas fa-cog fa-fw fa-xs"></i> <?php echo lang('SETTTINGS'); ?></a></li>
              <li role="separator" class="divider"></li>
              <li><a href="logout.php"><i class="fas fa-sign-out-alt fa-fw fa-xs"></i> <?php echo lang('LOGOUT'); ?> </a></li>
            </ul>
          </li>
        </ul>
    <?php } ?>

    </div>
  </div>
</nav>
