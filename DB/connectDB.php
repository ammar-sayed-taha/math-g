<?php
	$dsn = "mysql:host=localhost;dbname=math-g";
	$user = "root";
	$pass = 'ammar12345';

	$options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
	);

	try{
		$con = new PDO($dsn, $user, $pass, $options);
		
		//Check If The Users Table Is Exist Or Not
		if(!tableExist('users')){
			// Start Creating The Users Table
			$query = 'CREATE TABLE users(
				id 			int(11) 	 											NOT NULL AUTO_INCREMENT,
				username 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL UNIQUE,
				`password` 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				Email 		VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				FullName 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				image 	 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				cover 	 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				`Date` 		DATETIME 	 											NOT NULL,
				phone 		VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				location 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				bio 	    text 		 CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				occupation 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				rememberMe 	VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				birthdate 	date 		 											NOT NULL, 
				admin 		tinyint(1) 	 											NOT NULL DEFAULT 0,
				owner 		tinyint(1) 	 											NOT NULL DEFAULT 2,

				PRIMARY KEY(id) 
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Users Table

			//initalize the admin username and password automatic
			$stmt = $con->prepare('INSERT INTO users(username, password, FullName, admin, owner) VALUES (?, ?, ?, 1, 1)');
			$stmt->execute(array('ayman', password_hash('ayman', PASSWORD_DEFAULT), 'أيمن سالم'));
		}

		//Check If The Titles Table Is Exist Or Not
		if(!tableExist('titles')){
			// Start Creating The Titles Table
			$query = 'CREATE TABLE titles(
				id 			int(11) 	 											NOT NULL AUTO_INCREMENT,
				name 		VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				visible		tinyint(1) 												NOT NULL,
				ordering 	INT(11) 	 											NOT NULL DEFAULT 0,
				parent 		INT(11) 	 											NOT NULL DEFAULT 0,
				member_id 	INT(11) 	 											NOT NULL,

				PRIMARY KEY(id),
				CONSTRAINT FK_member_id
				FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Titles Table
		}

		//Check If The Lessons Table Is Exist Or Not
		if(!tableExist('lessons')){
			// Start Creating The Lessons Table
			$query = 'CREATE TABLE lessons(
				id 				int(11) 	 											NOT NULL AUTO_INCREMENT,
				name 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				file 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				external_file 	VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				add_date 		datetime 	 											NOT NULL,
				title_id		INT(11) 	 											NOT NULL,
				member_id 		INT(11) 	 											NOT NULL,
				ordering 		INT(11) 	 											NOT NULL DEFAULT 0,
				visible 		tinyint(1) 	 											NOT NULL DEFAULT 0,


				PRIMARY KEY(id),
				CONSTRAINT const_title
				FOREIGN KEY (title_id) REFERENCES titles(id) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT const_member
				FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Lessons Table
		}

		//Check If The Comments Table Is Exist Or Not
		if(!tableExist('comments')){
			// Start Creating The Comments Table
			$query = 'CREATE TABLE comments(
				id 			int(11) 	 											NOT NULL AUTO_INCREMENT,
				comment 	blob 													NOT NULL,
				add_date 	datetime 	 											NOT NULL,
				lesson_id	INT(11) 	 											NOT NULL,
				member_id 	INT(11) 	 											NOT NULL,
				approve 	tinyint(1) 	 											NOT NULL DEFAULT 0,
				parent 		int(11) 	 											NOT NULL DEFAULT 0,


				PRIMARY KEY(id),
				CONSTRAINT lesson_comment
				FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT member_comment
				FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Comments Table
		}

		//Check If The emoji_comments Table Is Exist Or Not
		if(!tableExist('emoji_comments')){
			// Start Creating The emoji_comments Table
			$query = 'CREATE TABLE emoji_comments(
				id 			int(11) 	 											NOT NULL AUTO_INCREMENT,
				emoji 		tinyint(1)												NOT NULL,
				comment_id 	INT(11) 	 											NOT NULL,
				member_id 	INT(11) 	 											NOT NULL,

				PRIMARY KEY(id),
				CONSTRAINT FK_emoji_member_id
				FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT FK_emoji_comment_id
				FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The emoji_comments Table
		}

		/*
		** this table used for the notifications of the users 
		** (adding lesson, reply to comment , make react emoji etc...)
		*/
		//Check If The Notification Table Is Exist Or Not
		if(!tableExist('notify')){
			// Start Creating The Notification Table
			$query = 'CREATE TABLE notify(
				id 			int(11) 	 											 NOT NULL AUTO_INCREMENT,
				sender 		int(11) 	 											 NOT NULL,
				reciever 	int(11) 	 											 NOT NULL,
				lesson_id 	int(11) 	 											 NOT NULL,
				type 		varchar(100)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				is_read		tinyint(1) 	 											 NOT NULL DEFAULT 0,
				add_date 	datetime 												 NOT NULL DEFAULT NOW(),
				
				PRIMARY KEY(id)
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Notification Table
		}

		/* 
		** this table is used to holds the links of social media 
		** such as facebook, youtube, twitter ....
		*/
		//Check If The Social Table Is Exist Or Not
		if(!tableExist('social_links')){
			// Start Creating The social_links Table
			$query = 'CREATE TABLE social_links(
				id 					int(11) 											    NOT NULL AUTO_INCREMENT,
				facebook 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				youtube 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				twitter 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				instagram 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				pintrest 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				googleplus 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				linkedin 			VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				
				member_id 	int(11) 	NOT NULL,

				PRIMARY KEY(id),
				CONSTRAINT const_social 
				FOREIGN KEY(member_id)		REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE

			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The social_links Table
		}

		//Check If The bar Table Is Exist Or Not
		if(!tableExist('bar')){
			// Start Creating The Bar Table
			$query = 'CREATE TABLE bar(
				id 			int(11) 	NOT NULL AUTO_INCREMENT,
				sentence 	blob 	 	NOT NULL,
				is_thanks 	tinyint(1) 	NOT NULL DEFAULT 0,

				PRIMARY KEY(id)
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Bar Table
		}

		//Check If The Carousel Table Is Exist Or Not
		if(!tableExist('carousel')){
			// Start Creating The Carousel Table
			$query = 'CREATE TABLE carousel(
				id 			int(11) 	 											 NOT NULL AUTO_INCREMENT,
				title 		blob  		  											 NOT NULL,
				link 		varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				image 		varchar(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,

				PRIMARY KEY(id)
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Bar Table
		}

		/* 
		** this table is used to holds the id's of the members who messages
		** holds the id's for the conversation between them
		*/
		//Check If The Messages Table Is Exist Or Not
		if(!tableExist('messages')){
			// Start Creating The Messages Table
			$query = 'CREATE TABLE messages(
				id 					int(11) 												 NOT NULL AUTO_INCREMENT,
				sender_id 			int(11) 												 NOT NULL,
				reciever_id 		int(11) 												 NOT NULL,
				color 				varchar(50)   CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				sender_read 		tinyint(1) 												 NOT NULL DEFAULT 0,
				reciever_read 		tinyint(1) 												 NOT NULL DEFAULT 0,
				update_date			datetime 												 NOT NULL DEFAULT NOW(),

				PRIMARY KEY(id),
				CONSTRAINT const_sender
				FOREIGN KEY(sender_id)			REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT const_reciever 
				FOREIGN KEY(reciever_id)		REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The Messages Table
		}

		/* 
		** this table is used to hold the conversation between members text, images and files
		*/
		//Check If The conversation Table Is Exist Or Not
		if(!tableExist('conversation')){
			// Start Creating The conversation Table
			$query = 'CREATE TABLE conversation(
				id 					int(11) 												 NOT NULL AUTO_INCREMENT,
				sender_id 			int(11) 												 NOT NULL,
				reciever_id 		int(11) 												 NOT NULL,
				sentence 			blob  		  											 NOT NULL,
				file 				varchar(255)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				seen				tinyint(1) 												 NOT NULL DEFAULT 0,
				add_date 			datetime 												 NOT NULL DEFAULT NOW(),


				PRIMARY KEY(id),
				CONSTRAINT const_sender_id
				FOREIGN KEY(sender_id)			REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
				CONSTRAINT const_reciever_id
				FOREIGN KEY(reciever_id)		REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE = INNODB;';

			$stmt = $con->prepare($query);
			$stmt->execute();
			// End Creating The conversation Table
		}



	}catch(Exception $e){
		// header('location:../DB/FailedConnectDB.php');

		echo 
			'<h3 
				style="
					color:#FFF;
					font-weight:bold;
					text-align:center;
					background:#ca1e1e;
					padding:20px;
					margin:100px auto;
					font-family: Tahoma;
				" >

				We are sorry, there a problem to connect with the database try again later ;( 
			</h3>';
		exit();
	}

	//Function Checks if the table exist ot not
	function tableExist($table){
		global $con;
		$stmt = $con->prepare('SHOW TABLES LIKE "'.  $table . '"');
		$stmt->execute();
		return $stmt->rowCount();
	}