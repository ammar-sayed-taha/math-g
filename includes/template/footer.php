

	<!-- The End Of body-container section which holds the whole body of the page -->
		</section>

		<!-- Start Footer Section -->

		<section class="footer">
			<div class="container">
				<div class="copyright">
					<span><?php echo @lang('MADE_WITH') ?> <i class="fa fa-heart fa-lg"></i> <?php echo @lang('BY'); ?> 
						<a href="https://www.facebook.com/ammar.romancic" target="_blank"><?php echo @lang('DEV_NAME'); ?></a>
						<span><i class="fa fa-mobile-alt fa-fw fa-md"></i> 01018466725 - </span>
					</span>
					<span><?php echo @lang('ALL_RIGHTS') ?> &copy; <?php echo @date('Y'); ?></span>
				</div>
			</div>
		</section>

		<!-- End Footer Section -->

		<!-- Start back to top span -->
		<div class="back-to-top"><i class="fa fa-arrow-up fa-lg"></i></div>
		<!-- End back to top span -->


		
		<script src="<?php echo $admin_js_path; ?>jquery-ui.min.js"></script>
		<script src="<?php echo $admin_js_path; ?>jquery.selectBoxIt.min.js"></script>
		<script src="<?php echo $admin_js_path; ?>bootstrap.min.js"></script>
		<script src="<?php echo $js_path; ?>frontend.js"></script>
	</body>
</html>