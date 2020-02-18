<?php
	foreach ($parentTitles as $parentTitle) {
		$title = $parentTitle;
		include 'titlesManagePage.php';

		foreach($childTitles as $childTitle1){
			if($childTitle1['parent'] == $parentTitle['id']){
				displayManagePageTitles($parentTitle, $childTitle1);
				
				foreach($childTitles as $childTitle2){
					if($childTitle2['parent'] == $childTitle1['id']){
						displayManagePageTitles($childTitle1, $childTitle2);

						foreach($childTitles as $childTitle3){
							if($childTitle3['parent'] == $childTitle2['id']){
								displayManagePageTitles($childTitle2, $childTitle3);

								foreach($childTitles as $childTitle4){
									if($childTitle4['parent'] == $childTitle3['id']){
										displayManagePageTitles($childTitle3, $childTitle4);

										foreach($childTitles as $childTitle5){
											if($childTitle5['parent'] == $childTitle4['id']){
												displayManagePageTitles($childTitle4, $childTitle5);

												foreach($childTitles as $childTitle6){
													if($childTitle6['parent'] == $childTitle5['id']){
														displayManagePageTitles($childTitle5, $childTitle6);

														foreach($childTitles as $childTitle7){
															if($childTitle7['parent'] == $childTitle6['id']){
																displayManagePageTitles($childTitle6, $childTitle7);

																foreach($childTitles as $childTitle8){
																	if($childTitle8['parent'] == $childTitle7['id']){
																		displayManagePageTitles($childTitle7, $childTitle8);

																		foreach($childTitles as $childTitle9){
																			if($childTitle9['parent'] == $childTitle8['id']){
																				displayManagePageTitles($childTitle8, $childTitle9);

																				foreach($childTitles as $childTitle10){
																					if($childTitle10['parent'] == $childTitle9['id']){
																						displayManagePageTitles($childTitle9, $childTitle10);
																					}
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>