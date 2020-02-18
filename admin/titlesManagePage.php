
<!-- This File displayed in Manage Titles Page To Display The Parent And its Childs  -->
<div class="lesson-body">
	<h4>
		<strong><bdi><?php echo $title['name'];?></bdi></strong>
		<span class="parent-lesson">
			<!-- Display The Parent Of This Category If It is Child Category -->
			<?php 
				if($title['parent'] != 0)
					if($title['parent'] == $parent['id']) { echo '<i class="fa fa-arrow-right fa-sm fa-fw"></i>' . $parent['name']; }
			?>
		</span>
	</h4>

	<?php
	//if the title hiodden then print hidden span
	if($title['visible'] == '1'){echo '<div><span class="visible">' . @lang('HIDDEN_TITLE') . '</span></div>';}
	?>

	<div class="hidden-btn">
		<!-- this span will display the order of each title to make the admin can sort them easily -->
		<span class="order"><i class="fa fa-sort fa-fw fa-xs"></i> <?php echo $title['ordering']; ?></span>

		<a href="?do=Edit&titleid=<?php echo $title['id']; ?>" >
			<span class="btn btn-success"><i class="fa fa-edit fa-fw fa-xs"></i> Edit</span>
		</a>
		<a class="confirm-delete" href="?delete=delete&titleid=<?php echo $title['id']; ?>" >
			<span class="btn btn-danger"><i class="fa fa-times fa-fw fa-xs"></i> Delete</span>
		</a>
	</div>
</div>
<hr>
