<div class="selectBox">
	<select name = "material">
		<option value="0"> None</option>
		<?php
			$childTitles = selectItems('id, name, parent, ordering', 'titles', 'parent != ?', array(0), 'ordering DESC');
			//Get All Parent Titles Parent Their Names Next To Their Children
			if($_SESSION['admin'] == 1){ //if the user is the admin
				$parentTitles = selectItems('id, name, ordering', 'titles', 'parent = ?', array(0), 'ordering DESC');
			}elseif($_SESSION['admin'] == 2){ //if the user is supervisor
				$parentTitles = selectItems('id, name, ordering', 'titles', 'parent = ? AND member_id = ?', array(0, 2), 'ordering DESC');
			}

			foreach ($parentTitles as $parentTitle) {?>
				<option class="parent-option" value = " <?php echo $parentTitle['id']; ?>" <?php if(@$selected == $parentTitle['id']) echo 'selected'; ?>>
					<?php echo $parentTitle['ordering'] . ': '  . $parentTitle['name'];  //display the category 
						
						foreach($childTitles as $childTitle){
							if($childTitle['parent'] == $parentTitle['id']) {?>
								<option value = "<?php echo $childTitle['id'] ?>" <?php if(@$selected == $childTitle['id']) echo 'selected'; ?>>   
									<?php echo $childTitle['ordering'] . ': '  . $childTitle['name']; 

									foreach($childTitles as $childTitle2){
										if($childTitle2['parent'] == $childTitle['id']) {?>
											<option value = "<?php echo $childTitle2['id'] ?>" <?php if(@$selected == $childTitle2['id']) echo 'selected'; ?>>   
												<?php echo '&emsp;' .  $childTitle2['ordering'] . ': '  . $childTitle2['name']; 

												foreach($childTitles as $childTitle3){
													if($childTitle3['parent'] == $childTitle2['id']) {?>
														<option value = "<?php echo $childTitle3['id'] ?>" <?php if(@$selected == $childTitle3['id']) echo 'selected'; ?>>   
															<?php echo '&emsp;&emsp;' . $childTitle3['ordering'] . ': '  . $childTitle3['name']; 

															foreach($childTitles as $childTitle4){
																if($childTitle4['parent'] == $childTitle3['id']) {?>
																	<option value = "<?php echo $childTitle4['id'] ?>" <?php if(@$selected == $childTitle4['id']) echo 'selected'; ?>
																		<?php if(@$parent == $childTitle4['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle4['id']) echo 'disabled';?> >   
																		<?php echo '&emsp;&emsp;&emsp;' . $childTitle4['ordering'] . ': '  . $childTitle4['name']; 

																			foreach($childTitles as $childTitle5){
																				if($childTitle5['parent'] == $childTitle4['id']) {?>
																					<option value = "<?php echo $childTitle5['id'] ?>"  <?php if(@$selected == $childTitle5['id']) echo 'selected'; ?>
																						<?php if(@$parent == $childTitle5['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle5['id']) echo 'disabled';?> >   
																						<?php echo '&emsp;&emsp;&emsp;&emsp;' . $childTitle5['ordering'] . ': '  . $childTitle5['name'];

																							foreach($childTitles as $childTitle6){
																								if($childTitle6['parent'] == $childTitle5['id']) {?>
																									<option value = "<?php echo $childTitle6['id'] ?>" <?php if(@$selected == $childTitle6['id']) echo 'selected'; ?>
																										<?php if(@$parent == $childTitle6['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle6['id']) echo 'disabled';?> >   
																										<?php echo '&emsp;&emsp;&emsp;&emsp;&emsp;' . $childTitle6['ordering'] . ': '  . $childTitle6['name']; 

																											foreach($childTitles as $childTitle7){
																												if($childTitle7['parent'] == $childTitle6['id']) {?>
																													<option value = "<?php echo $childTitle7['id'] ?>" <?php if(@$selected == $childTitle7['id']) echo 'selected'; ?>
																														<?php if(@$parent == $childTitle7['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle7['id']) echo 'disabled';?> >   
																														<?php echo '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;' . $childTitle7['ordering'] . ': '  . $childTitle7['name']; 

																															foreach($childTitles as $childTitle8){
																																if($childTitle8['parent'] == $childTitle7['id']) {?>
																																	<option value = "<?php echo $childTitle8['id'] ?>"  <?php if(@$selected == $childTitle8['id']) echo 'selected'; ?>
																																		<?php if(@$parent == $childTitle8['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle8['id']) echo 'disabled';?> >   
																																		<?php echo '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;' . $childTitle8['ordering'] . ': '  . $childTitle8['name']; 

																																			foreach($childTitles as $childTitle9){
																																				if($childTitle9['parent'] == $childTitle8['id']) {?>
																																					<option value = "<?php echo $childTitle9['id'] ?>"   <?php if(@$selected == $childTitle9['id']) echo 'selected'; ?>
																																						<?php if(@$parent == $childTitle9['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle9['id']) echo 'disabled';?> >   
																																						<?php echo '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;' . $childTitle9['ordering'] . ': '  . $childTitle9['name']; 

																																							foreach($childTitles as $childTitle10){
																																								if($childTitle10['parent'] == $childTitle9['id']) {?>
																																									<option value = "<?php echo $childTitle10['id'] ?>"   <?php if(@$selected == $childTitle10['id']) echo 'selected'; ?>
																																										<?php if(@$parent == $childTitle10['id']) echo 'selected'; if(@$title[0]['id'] == $childTitle10['id']) echo 'disabled';?> >   
																																										<?php echo '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;' . $childTitle10['ordering'] . ': '  . $childTitle10['name']; ?>
																																									</option>
																																								<?php }
																																							}
																																						?>
																																					</option>
																																				<?php }
																																			}
																																		?>
																																	</option>
																																<?php }
																															}
																														?>
																													</option>
																												<?php }
																											}
																										?>
																									</option>
																								<?php }
																							}
																						?>
																					</option>
																				<?php }
																			}	
																		?>																		
																	</option>
																<?php }
															}	
															?>
														</option>
													<?php }
												}
											?>
											</option>
										<?php }
									}
								?>
								</option>
							<?php }
						}
					?>		
				</option>

		<?php } ?>
	</select>
</div>