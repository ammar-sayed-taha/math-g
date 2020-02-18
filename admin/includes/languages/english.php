<?php

	//get the date
	// $year = date("Y");

	function lang( $phrase , $additional = '') {


		$lang = array(

			//Global Phrases
			'NO_CHANGE'				=> 'either you didn\'t change anything or failed to connect with the database',
			'NAME'					=> 'Name',
			'COMMENT'				=> 'Comment',
			'MEMBERS' 				=> 'Members',
			'LESSONS' 				=> 'Lessons',
			'EMAIL'					=> 'Email',
			'PASSWORD'				=> 'Password',
			'ADD'					=> 'Add',
			'TITLES' 				=> 'Titles',
			'EDIT'					=> 'Edit',
			'SAVE' 					=> 'Save',
			'ORDER'					=> 'order',
			'LINK'					=> 'Link',
			'VISIBLE'				=> 'Visible ?',
			'PARENT_?'				=> 'Parent ?',
			'TABLE_CONTROL'			=> 'Cotrol',
			'MEM_TABLE_IMAGE'		=> 'Image',
			'INVALID_UPLOAD'		=> 'The file did\'t uploaded try again later',
			'EMPTY_NAME_INPUT'		=> 'The Name Field Can\'t Be Empty',
			'SIZE_EXCEEDED'			=> 'The file size is more than 10MB',

			//*********** Start Navbar Links ***********
			'DASHBOARD' 			=> 'Dashboard',
			'COMMENTS' 				=> 'Comments',
			'BAR' 					=> 'bar',
			'CAROUSEL' 				=> 'Carousel',
			'CV' 					=> 'CV',
			'MORE' 					=> 'More',
			'STATISTICS' 			=> 'Statistics',
			'LOGS' 					=> 'Logs',
			'HOMEPAGE'				=> 'Homepage',
			'EDITPROFILE' 			=> "Edit Profile",
			'SETTTINGS'				=> 'Settings',
			'LOGOUT' 				=> 'Logout',
			//*********** End Navbar Links ***********

			//*********** Start Login Page ***********
			'WRONGLOGIN'			=> 'sorry the username or password not correct',
			
			//*********** End Login Page ***********


			//*********** Start dashboard page ***********
			//Global phrases
			'LATEST'				=> 'Latest',

			//Not Global phrases
			'WELCOME'				=> 'Welcome',
			'TOTAL_MEM'				=> 'Total Members',
			'TOTAL_COM'				=> 'Total Comments',
			'TOTAL_TITLES'			=> 'Total Titles',
			'TOTAL_FILES'			=> 'Total Files',
			'FILES'					=> 'Files',
			'COMMENTS'				=> 'Comments',
			'SHOW_COM_OF'			=> 'Show All Comments Of',
			'NO_COM_TO_SHOW'		=> 'There is No Comments To Show',
			'NO_FILES_TO_SHOW'		=> 'There is No Files To Show',
			'LAST_REG_USERS'		=> 'Registered Users',

			//*********** End dashboard page ***********


			// ***** Start Members Page ************** 
			//Globals in this page
			'USERNAME<3' 			=> 'The username must be at least <strong>4 characters</strong>',
			'PASSWORD<5' 			=> 'the password must be at least <strong>5 characters</strong>',
			'USERNAME_EXIST' 		=> 'This username is aleady exist',
			'FULL_NAME' 			=> 'Full Name',
			'USERNAME' 				=> 'Username',
			'ADD_USERNAME' 			=> 'username used for login your account',

			//Edit Page
			'EDIT MEMBERS'			=> 'Edit Members',
			'EDITPASSWORD' 			=> 'You can\'t see the password but can update it',
			'CONPASSWORD' 			=> 'Confirm Password',
			'FULLNAME'				=> 'Full Name',
			'ERR_USERID' 			=> 'The member of this id is not exist',
			'USERUPDATED' 			=> 'The Member Updated Successfully!',

			//Add Page
			'ADD MEMBERS'			=> 'Add New Member',
			'PASSWORD' 				=> 'Password',
			'USER_ADDED' 			=> 'The member <strong>added successfully</strong>',
			'ADMIN_?' 				=> 'Member is ?',
			'MEM_ADD_P' 			=> 'Members | Add New Member',

			//Manage Page
			'MANAGE_MEMBER'			=> 'Manage Members',
			'ADD_NEW_MEMBER'		=> 'Add New Member',
			'MEM_DELETED'			=> 'The member deleted successfully!',
			'MEM_TABLE_USERNAME'	=> 'Username',
			'MEM_TABLE_EMAIL'		=> 'Email',
			'MEM_TABLE_F_NAME'		=> 'Full Name',
			'MEM_TABLE_REG_DATE'	=> 'Register Date',
			'MEM_MANAGE_P'			=> 'Members | Manage Page',
			'SORT_MEM_BY_ID'		=> 'Sort The Members by their ID',
			'SORT_MEM_BY_IMG'		=> 'Sort The Members by their Image',
			'SORT_MEM_BY_USERNAME'	=> 'Sort The Members by their Username',
			'SORT_MEM_BY_EMAIL'		=> 'Sort The Members by their Email',
			'SORT_MEM_BY_F_NAME'	=> 'Sort The Members by their Full Name',
			'SORT_MEM_BY_DATE'		=> 'Sort The Members by their Registeration Date',

			//*********** End Members Page ***********
 
			//*********** Start Titles Page ***********

			//Add Page
			'ADD TITLE'			=> 'Add Title',
			'TITLE NAME'		=> 'Type the title',
			'TITLE ORDER'		=> 'Oerder The Title(optional)',
			'SUCCESS_ADD'		=> 'The title <strong>Added Successfully!</strong>',
			'INSERT_NAME'		=> 'You Must Add Title First',

			//Edit Page
			'TITLE_NOT_EXIST' 	=> 'The title you need to choose is not exist choose valid one',
			'SUCCESS_EDIT' 		=> 'The title <strong>updated successfully!</strong>',

			//Manage Page
			'TITLES_MANAGE'		=> 'Manage Titles',
			'ADD_NEW_TITLE'		=> 'Add new title',
			'NO_TITLES_TO_SHOW'	=> 'There is no Titles To Show',
			'SORT_BY'			=> 'Sort By',
			'TITLE_NAME'		=> 'Title Name',
			'SORT_VISIBILITY'	=> 'Visibility',
			'SORT_ORDER'		=> 'Order',
			'HIDDEN_TITLE'		=> 'Hidden',
			'CNT_DELETE'		=> 'This title contains child titles, delete them first and then back to delete this title',
			'ERR_TITLE_DELETE'	=> 'The Title didn\'t delete try again later',
			'TITLE_SUCESS_DEL'	=> 'The Title deleted successfully!',

			//*********** End Titles Page ***********

			//*********** Start Lessons Page ***********
			//Global Phrases
			'LESSON_ORDER'				=> 'Oerder the lesson(optional)',
			'FILE_NOT_ALLOWED'			=> 'The file extension is not allowed',
			'NOTE_EXTENSION'			=> '<strong>Note: </strong> .png, .jpg, .jpeg, .gif, .mp4, .wmv, .flv, .avi, .swf, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .txt, htm, .html',

			//Add Page
			'ADD LESSON'				=> 'Add New Lesson',
			'LESSON NAME'				=> 'Lesson Name',
			'UPLOAD_FILE'				=> 'Upload Lesson',
			'UPLOAD_LESSON'				=> 'choose the the file you need to upload',
			'MEMBER'					=> 'Teacher',
			'LESSON_TITLE'				=> 'Lesson Title',
			'ERR_ADD_FILE'				=> 'You Must Upload file for this lesson',
			'CHOOSE_MATERIAL'			=> 'You must choose material for this lesson',
			'LESSON_ADDED'				=> 'The Lesson Added Successfully!',
			'ADD_MS_EMBED'				=> 'this file is <strong>microsoft document</strong> file you must add the embed link',
			'EXTERNAL_FILE'				=> 'MS extension',
			'ADD_MS_FILE'				=> 'if this is file was microsoft document then put the embed link',


			//Edit Page
			'EDIT_LESSON'				=> 'Edit Lesson',
			'ERR_LESSONID'				=> 'There is no lesson exist with this id try another id',
			'LESSON_UPDATED'			=> 'The lesson updated successfully!',


			//Manage Page
			'MANAGE_LESSONS'			=> 'Manage Lessons',
			'ADD_NEW_LESSON'			=> 'Add New Lesson',
			'TABLE_NAME'				=> 'Name',
			'TABLE_ORDER'				=> 'order',
			'TABLE_VISIBLE'				=> 'visible',
			'TABLE_DATE'				=> 'Date',
			'TABLE_LESSON'				=> 'Lesson',
			'TABLE_MATERIAL'			=> 'Material',
			'TABLE_USERNAME'			=> 'Username',
			'DELETE_FAILED'				=> 'The lesson didn\'t delete try again later',
			'SUCCESS_DELETE'			=> 'The lesson deleted successfully!',
			'SORT_LESSON_BY_ID'			=> 'Sort the Table by id',
			'SORT_LESSON_BY_NAME'		=> 'Sort the Table by their name',
			'SORT_LESSON_BY_ORDER'		=> 'Sort the Table by their order',
			'SORT_LESSON_BY_VISIBLE'	=> 'Sort the Table by their visibility',
			'SORT_LESSON_BY_DATE'		=> 'Sort the Table by add date',
			'SORT_LESSON_BY_LESSON'		=> 'Sort the Table by lessons',
			'SORT_LESSON_BY_MATERIAL'	=> 'Sort the Table by materials',
			'SORT_LESSON_BY_USERNAME'	=> 'Sort the Table by username',

			//*********** End Lessons Page ***********

			//*********** Start Comments Page ***********
			//Global Phrases			
			//Add Page
			'ADD COMMENT'				=> 'Add Comment',
			'COMMENT FIELD'				=> 'Add The Comment Here ',
			'COMMENT_ADDED_YES'			=> 'The comment added successfully!',
			'ERR_CHOOSE_LESSON'			=> 'you must choose the lesson that you need to comment on it',
			'COM_FIELD_EMPTY'			=> 'The Comment Field Can\'t Be Empty',
			

			//Edit Page
			'EDIT COMMENT'				=> 'Edit Comment',
			'SUCCESS_COM_EDIT'			=> 'The Comment updated successfully!',

			//Manage Page
			'MANAGE_COMMENT'			=> 'Manage Comments',
			'ADD_NEW_COMMENT'			=> 'Add New Comment',
			'SORT_COMMENT_BY_ID'		=> 'Sort the Table by ID',
			'SORT_COMMENT_BY_NAME'		=> 'Sort the Table by The Comment text (this will take long time because of the comment contains many letters to compare)',
			'SORT_COMMENT_BY_DATE'		=> 'Sort the Table by Date of each comment',
			'SORT_COMMENT_BY_USERNAME'	=> 'Sort the Table by username who wrote these comments',
			'SORT_COMMENT_BY_LESSON'	=> 'Sort the Table by lessons of these comments',
			'SORT_COMMENT_BY_PARENT'	=> 'Sort the Table by the parent id of these comments',
			'TABLE_COMMENT'				=> 'Comment',
			'TABLE_DATE'				=> 'Date',
			'TABLE_USERNAME'			=> 'Username',
			'TABLE_LESSON'				=> 'Lesson',
			'COMMENT_APPROVED_YES'		=> 'The comment approved successfully!',
			'DELETE_COMMENT_YES'		=> 'The comment Deleted successfully!',
			'SHOW_MR_ABOUT'				=> 'Show More About',


			//*********** End Comments Page ***********

			//*********** Start Bar Page ***********
			//Global Phrases
			'BOTTOM_BAR'				=> 'Bottom bar',
			'Top_BAR'					=> 'Top bar',
			'BAR_SENTENCE'				=> 'bar sentence',
			'BAR_TYPE'					=> 'Type ?',

			//Add Page 
			'ADD_BAR'					=> 'Add new bar title',
			
			'SENTENCE_FIELD'			=> 'Add Sentence of the bar here ',
			'BAR_TOP'					=> 'top',
			'BAR_BOTTOM'				=> 'bottom',
			'BAR_SEN_EMPTY'				=> 'The sentence field can\'t be empty',
			'BAR_ADDED_YES'				=> 'The bar title added successfully!',

			//Edit Page1
			'EDIT_BAR'					=> 'Edit bar title',
			'SUCCESS_BAR_EDIT'			=> 'The bar title edited successfully',

			//Manage Page
			'MANAGE_BAR'				=> 'Manage Bar',
			'ADD_NEW_BAR'				=> 'Add New Bar title',
			'TABLE_BAR'					=> 'Bar Sentence',
			'TABLE_TYPE'				=> 'Type',
			'SORT_BAR_BY_ID'			=> 'Sort The Table By Its ID',
			'SORT_BAR_BY_SENTENCE'		=> 'Sort The Table By Its Sentence',
			'SORT_BAR_BY_TYPE'			=> 'Sort the table by its type if thanks bar or definition bar',
			'DELETE_BAR_YES'			=> 'The bar deleted successfully!',

			//*********** End Bar Page ***********


			//*********** Start Carousel Page ***********
			//Global Phrases
			'ADD_NEW_CAROUSEL'			=> 'Add New Carousel',
			'CAR_ADD_FILE'				=> 'You Must choose Image for the Carousel',
			'CAR_FILE_INVALID'			=> 'The Image Extension in not allowed',


			//Manage Page
			'MANAGE_CAROUSEL'			=> 'Manage Carousel',
			'SORT_CAROUSEL_BY_ID'		=> 'Sort The Carousel By ID DESC',
			'SORT_CAROUSEL_BY_TITLE'	=> 'Sort The Carousel Using The Title of it',
			'SORT_CAROUSEL_BY_IMAGE'	=> 'Sort the carousel using the image name',
			'SORT_CAROUSEL_BY_LINK'		=> 'Sort the carousel using the Link String',
			'TABLE_TITLE'				=> 'Title',
			'TABLE_LINK'				=> 'Link',
			'DELETE_CAROUSEL_YES'		=> 'The Carousel deleted Successfully!',
			'CAROUSEL_TITLE_TAG'		=> 'Carousel Page | Add New Carousel',

			//Add Page
			'CAROUSEL_TITLE'			=> 'Title',
			'PLACE_TITLE'				=> 'Enter The Title of the Carousel',
			'CAROUSEL_LINK'				=> 'Link',
			'PLACE_LINK'				=> 'if the title from external website put its link here!',
			'CAROUSEL_IMAGE'			=> 'Image',
			'CAROUSEL_TIT_EMPTY'		=> 'You must enter title for thr carousel',
			'CAR_ADDED_YES'				=> 'The Carousel added successfully!',

			//Edit Page
			'CAR_EDITED_YES'			=> 'The Carousel Edited Successfully!',
			'EDIT_CAR'					=> 'Edit Carousel',
			

			//*********** End Carousel Page ***********
			

			//*********** Start CV Page ***********
			//Global Phrases
			'CV_INVAILD_EXTENSION'		=> 'The file extension ' . $additional . ' is not allowed',

			//Add Page
			'ADD_CV'					=> 'Add New CV',
			'CV_NAME'					=> 'CV name',
			'PALCE_NAME_FIELD'			=> 'Enter the name of the cv',
			'UPLOAD_CV'					=> 'Upload the cv file',
			'UPLOAD_CV_FILE'			=> 'CV file',
			'NOTE_CV_EXTENSION'			=> '<strong>Note: </strong> allowed .pdf or .swf',
			'BAR_ID'					=> 'bar id',
			'CV_NAME_EMPTY'				=> 'You have to insert file name',
			'FILE_HAS_ERR'				=> 'Something wrong happened while uploading the file',
			'CV_SIZE_EXCEEDED'			=> 'The file maximum size <strong>10MB</strong>',
			'CV_ADDED'					=> 'The CV added successfully!',


			//Edit Page
			'ERR_CVID'					=> 'There is no CV file whith id: ' . $additional,
			'CV_UPDATED'				=> 'The CV file updated successfully!',
			'EDIT_CV'					=> 'Edit CV file',

			//Manage Page
			'MANAGE_CV'					=> 'Manage CV',
			'ADD_NEW_CV'				=> 'Add New CV',
			'SORT_CV_BY_NAME'			=> 'Sort CV by Name',
			'SORT_CV_BY_BARID'			=> 'Sort CV by its bar_id',
			'TABLE_BAR_ID'				=> 'bar id',
			'DELETE_CV_YES'				=> 'The CV file deleted successfully!',
			'DELETE_CV_NO'				=> 'The CV file didn\'t deleted please try again',

			//*********** End CV Page ***********


			
			// Start Footer Page

			'CONTACT_US'				=> 'CONTACT US',
			'FOOTER_LESSONS'			=> 'LESSONS',
			'DEV_INFO'					=> 'CONTACT DEVELOPER',
			'DEV_NAME'					=> 'Ammar Sayed',
			'FOO_MSG'					=> 'Do you have any question ? ',
			'FOO_EMAIL'					=> 'Enter your email ',
			'MADE_WITH'					=> 'Made with',
			'BY'						=> 'by',
			'ALL_RIGHTS'				=> 'All rights reserved',
			'EMPTY_FOO_MSG'				=> 'You Must Enter Message you need to send',
			'EMPTY_FOO_MAIL'			=> 'You Must Enter The Email that will send to',
			'FOO_MSG_SENT'				=> 'Thank you :) Your Message sent successfully, it will be replaied as soon as the admin see your message!',
			'FOO_MSG_ERR'				=> 'Sorry you message didn\'t send please try again',

			// End Footer Page

		);

		return $lang[$phrase];

	}


?>