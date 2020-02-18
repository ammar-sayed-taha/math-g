<?php

	//get the date
	// $year = date("Y");

	function lang($phrase = '', $additional = '', $additional2 = '') {


		$lang = array(

			//Globals
			'WEBSITE_NAME'				=> 'MATH GENIUSES',
			'NO_CHANGE'					=> 'either you didn\'t change anything or failed to connect with the database',
			'HOME'						=> 'HOME',
			'PHONE'						=> 'Phone',
			'F_NAME'					=> 'Full Name',
			'NAME'						=> 'Name',
			'USERNAME'					=> 'Username',
			'PASSWORD'					=> 'Password',
			'EMAIL'						=> 'Email',
			'MALE'						=> 'Male',
			'BACK'						=> 'Back',
			'FEMALE'					=> 'Female',
			'NAV_NOTIFY'				=> 'Notifications',
			'BIRTHDATE'					=> 'Birthdate',
			'IMAGE'						=> 'Image',
			'LOCATION'					=> 'Location',
			'WEBSITE'					=> 'Website',
			'OCCUPATION'				=> 'Occupation',
			'DELETE_MSG'				=> 'Delete',
			'BIO' 						=> 'Bio',
			'FACEBOOK' 					=> 'Facebook',
			'TWITTER' 					=> 'Twitter',
			'YOUTUBE' 					=> 'Youtube',
			'INSTA' 					=> 'Instgram',
			'PINTEREST' 				=> 'Pinterst',
			'GOOGLE+' 					=> 'Google+',
			'LINKEDIN' 					=> 'Linkedin',
			'DELETE_COM'				=> 'Delete',
			'REPLY'						=> 'Reply',
			'EDIT'						=> 'Edit',
			'CANCEL'					=> 'cancel',
			'SEND'						=> 'Send',
			'SENT'						=> 'Sent',
			'TO'						=> 'To:',
			'LOGIN'						=> 'Login',
			'SIGNUP'					=> 'Signup',
			'COMMENT'					=> 'Comment',
			'SEARCH'					=> 'Search',
			'MORE'						=> 'More',
			'ANGRY'						=> 'angry',
			'SAD'						=> 'sad',
			'WOW'						=> 'wow',
			'HAHA'						=> 'haha',
			'LOVE'						=> 'love',
			'SHOW_MORE'					=> 'ٍShow more',
			'LIKE'						=> 'like',
			'VISIBLE'					=> 'show info',
			'REMEMBER_ME'				=> 'Remember Me',
			'IMG_EXCEEDED'				=> 'The image size larger than 10MB',
			'PLACE_SEARCH'				=> 'Type what you wnat and click enter',
			'ERR_DELETE_COM'			=> 'The Comment wasn\'t deleted, oftern probelm of connection with database, try again',
			'META_DESCRIPTION'			=> 'delicious physics, you will find all physics materials of primary, preparatory, secondary years,  very simple explanation for physics material ',
			'META_KEYWORDS'				=> 'physics, materials, video, videos, explanation, explain, primary, preparatory, secondary, lesson, lessons, unit, units, physics material, delicious, delicious physics, good video, good lesson',
			'EMPTY_F_NAME'				=> 'The Fullname field can\'t be empty',
			'INVALID_CHARS'				=> 'only allowed characters (a-z), (A-Z) and (0-9) check your fields and tyr again',


			// Start Navbar Phrases

			'DASHBOARD'					=> 'Dashboard',
			'SETTINGS'					=> 'Settings',
			'LOGOUT' 					=> 'Logout',
			'MY_ACCOUNT' 				=> 'My Account',
			'MESSAGE' 					=> 'Messages',
			'EMPTY_NOTIFY' 				=> 'There is no notifications for now',
			'SENDER_NAME'				=> '<strong>' .$additional . '</stron> uploaded new file <strong>' . $additional2 . '</strong> in folder of',
			'COMMENTER_NAME'			=> '<strong>' .$additional . '</stron> replied to your comment in ',
			'MARK_AS_READ'				=> 'Mark all as read',
			'EMPTY_MSG_MENU'			=> 'There is no messages to show',
			'NEW_MESSAGE'				=> 'New Message',
			'SEE_IN_MESSENGER'			=> 'Show All Messages',
			'WRITE_MSG'					=> 'Write a Message...',
			'NAV_MSG'					=> 'Messages',
			
			// End Navbar Phrases


			// Start Login Page

			//Global Phrases
			'USER_LESS_3_CHARS'			=> 'the username Must be at Least 3 Characters',
			'PASS_LESS_5_CHARS'			=> 'The Password Must be at Least 5 Characters',
			'EMPTY_USERNAME'			=> 'The Username Can\' be Empty',
			'EMPTY_PASS'				=> 'The Password Can\' be Empty',
			'EMPTY_EMAIL'				=> 'The Email Can\' be Empty',
			'LOGIN_SIGNUP'				=> 'login/Signup',
			'PLACE_FULLNAME'			=> 'fullname appears to users',
			'LOGIN_PLACE_PASS'			=> 'Enter the password',
			'LOGIN_PLACE_UNAME'			=> 'Enter the username - used in login account',
			'ERR_SIGNUP'				=> 'Something wrong happened, please try again in few minuets',

			//Not Global Phrases
			'USER_NOT_RIGTH'			=> 'This Username is not correct',
			'PASS_NOT_RIGHT'			=> 'This password is not correct',
			'EXIST_USERNAME'			=> 'Can\'t Signup This Username is Already Exist',
			'EXIST_USERNAME'			=> 'can\'t insert the account to database please, try again in a few seconds',
			'PHYSICS'					=> 'Physics',


			// End Login Page

			// Start Settings Page
			//Glopal Phrases
			'SETTING_PAGE'				=> 'Settings Page',

			//personal Info
			'PERSONAL_INFO'				=> 'personal info',
			'PLACE_F_NAME'				=> 'Full Name',
			'PLACE_PHONE'				=> 'Type your Phone number',
			'PLACE_F_NAME'				=> 'Type You Full Name',
			'PLACE_USERNAME'			=> 'Username Used to Login Account',
			'PLACE_PASS'				=> 'you can\'t see your password but you can update it',
			'PLACE_EMAIL'				=> 'Email used for notifications and more',
			'INVALID_PASS'				=> 'The password must br larger than <strong>5 characters</strong>',
			'EMPTY_USERNAME'			=> 'The username field can\'t be empty',
			'EMPTY_EMAIL'				=> 'The Email field can\'t be empty',
			'P_INFO_UPDATED'			=> 'The personal information updated successfully!',

			//Additional Info
			'DDITIONAL_INFO'			=> 'Additional Info',
			'PLACE_LOCATION'			=> 'Where are you from ?',
			'PLACE_WEBSITE'				=> 'Do you have website ?',
			'PLACE_BIO'					=> 'Share something about you',
			'PLACE_OCCUPATION'			=> 'Your current job ?',
			'ADD_INFO_UPDATED'			=> 'The Additional information updated successfully!',
			'SOCIAL_INFO_UPDATED'		=> 'The Social information updated successfully!',

			//Social Links
			'SOCIAL_LINKS'				=> 'Social links',
			'PLACE_FACEBOOK'			=> 'Facebook link',
			'PLACE_TWITTER'				=> 'Twitter link',
			'PLACE_YOUTUBE'				=> 'Youtube link',
			'PLACE_INSTA'				=> 'Instagram link',
			'PLACE_PINTEREST'			=> 'Pinterst link',
			'PLACE_GOOGLE+'				=> 'google+ link',
			'PLACE_LINKEDIN'			=> 'Linkedin link',

			// End Settings Page

			// Start Profile Page
			//Global Phrases

			//this phrases from function in functions.php of avatar and cover
			'EMPTY_IMG'					=> 'You Must Add Valid Image',
			'ERR_EXTENSION'				=> 'The Image Extension <strong>' . $additional . '</strong> is not Allowed',
			'ERR_UPLOAD_IMG'			=> 'Something Wrong Happened When Uploading The Image Please Try Again',
			'ERR_IMG_NAME'				=> 'either the image name is too long or faild to connect with the database',

			//Single Phrases
			'PRO_PAGE'					=> 'Profile Page',
			'PRO_PERSONAL_INFO'			=> 'Personal Info',
			'NO_ADDED'					=> 'Nothing added yet!',
			'PRO_ADDITIONAL_INFO'		=> 'Additional info',
			'LAST_COMMENTS'				=> 'Some comments of ' . $additional,
			'NO_COMMENTS_EXIST'			=> 'There is no Comments to Show',
			'SHOW_LESSON_PG'			=> 'Click to Show The Details of This Lesson',
			'ERR_DEL_PRO_IMG'			=> 'Something Wrong Happened When Deleting The Image Please Try Again',

			// End Profile Page

			// Start Titles Page

			'EMPTY_TITLE'				=> 'No lessons added in this folder yet :)',
			'LIST_NAME'					=> 'Name',
			'LIST_TYPE'					=> 'Type',
			'LIST_OWNER'				=> 'Uploader',
			'LIST_DATE'					=> 'Last modified',
			'FOLDER_TITLE'				=> 'Folders ',
			'FILE_TITLE'				=> 'Files',

			// End Titles Page


			// Start Lessons Page

			'NO_SUPPORT_FRAME'			=> 'Sorry your browser does not support inline frames.',
			'NO_SUPPORT_VIDEO'			=> 'Your browser does not support the video tag.',
			'LESSON_COMS'				=> 'Comments',
			'DOWN_FLASH'				=> 'downloading here',
			'UPDATE_FLASH'				=> 'The Camtasia Studio video content presented here requires a more recent version of the Adobe Flash Player. If you are you using a browser with JavaScript disabled please enable it now. Otherwise, please update your version of the free Flash Player by ',
			'NOT_VALID_FILE'			=> 'the file is no longer exsist or its extension is Not supported',
			'ADD_COMMENT'				=> 'Add Comment',
			'PLACE_COM'					=> 'Your Comment Will Not Appear Untill Approved By The Admin',
			'PLACE_CHILD_COM'			=> 'reply to ' . $additional . '...  ',
			'LOGIN_FIRST'				=> 'Login</a> Or <a href="login.php">Register</a> To Add Comment',
			'COM_FIELD_EMPTY'			=> 'The Comment Field Can\'t Be Empty',
			'COMMENT_ADDED_YES'			=> '<strong>The comment added successfully! But will not appear untill the admin approve on it</strong>',
			'COMMENT_ADD_FAILD'			=> 'fild to add the comment try again',

			// End Lessons Page

			// Start Index Page

			'READ_MORE'					=> 'See More',
			'WHO_AM_I'					=> 'Who am i ?',
			'LAST_LESSONS'				=> 'LESSONS ADDED RECENTLY',
			'HOME_MEM'					=> 'Members',
			'HOME_COM'					=> 'Comments',
			'HOME_LESSON'				=> 'Lessons',
			'INDEX_LINE1'				=> 'MATH-G THE BEST AND EASIEST WEBSITE IN MATHIMATICS',
			'INDEX_LINE2'				=> 'IT MADE FOR MIDDLE SCHOOL...HIGH SCHOOL AND ALSO UNIVERSITY STAGE',
			'INDEX_LINE3'				=> 'OUT MOTTO IS THINK...DEVELOPE AND MAKE IT EASY',
			'SEE_MORE'					=> 'SEE MORE',

			// End Index Page

			// Start Footer Page

			'DEV_NAME'					=> 'Ammar Sayed',
			'MADE_WITH'					=> 'Made with',
			'BY'						=> 'by',
			'ALL_RIGHTS'				=> 'All rights reserved',

			// End Footer Page

			//Start Search Page
			'NO_RESULT_SEARCH'			=> 'Ooopps, there is no result for you search',
			
			//End Search Page

			// Start Message Page

			'MESSAGE'					=> 'Messages',
			'SERCH_FRIEND'				=> 'Search for friend',
			'NEW_MSG'					=> 'New Message',
			'TITLE_MSG_PAGE'			=> 'MATH-GENIUS | MESAGES',
			'PLACE_CHAT'				=> 'Type a Message...',
			'EMPTY_CHAT'				=> 'chhose friend to show your messages',
			'CHAT_SEND_BTN'				=> 'Press Enter to send - press shift+Enter for new line',
			'REACH_LAST_CHAT'			=> 'There is no another Messages',
			'CONFIRM_DELETE_TXT'		=> 'Are you sure you want to delete this message ?',
			'CONFIRM_DELETE_TITLE'		=> 'Delete Message',
			
			// End Message Page


		);

		return $lang[$phrase];

	}


?>
