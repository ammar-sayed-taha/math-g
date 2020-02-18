<?php
	

	// used to display the page title dynamically
	function pageTitle(){
		global $pageTitle;
		echo isset($pageTitle) ? $pageTitle : "Default";
	}

	/*
	** insertItems Function
	** Used To Insert Date In Database
	** Parameters []
	** $table => the table which insert the data into
	** $colomn => the table and the colomns which need to be inserted
	** $values => holds the bind(?) sign which binds the $value with $array variable
	** $array => Contains the values of The values Variable
	*/
	function insertItems($table, $colomn, $value, $array = array()){
		global $con;
		$stmt = $con->prepare('INSERT INTO ' . $table . '(' . $colomn . ')' . ' VALUES (' . $value . ')');
		$stmt->execute($array);
		return $stmt->rowCount();
	}

	/*
	** updateItems Function
	** Used To Update Date In Database
	** Parameters []
	** $table => the table which update the data into
	** $colomn => contains the colomns and the values
	** $value => Hold The Value Of The Colomn Variable To Update In DB
	** $where => the condition of the update
	*/
	function updateItems($table, $colomn, $value = array(), $where = 1){
		global $con;

		$stmt = $con->prepare('UPDATE '  . $table . ' SET ' . $colomn . ' WHERE ' . $where);
		$stmt->execute($value);
		return $stmt->rowCount();

	}

	/*
	** selectItems Function v1.0
	** Used To Select the data from database and return an array with the data
	** Parameters [$select, $from, $where, $value]
	** $select => used to select items from DB
	** $from => indicates the table
	** $where => the cobndition of selecting
	** $value => holds the value of the colomns in array to execute
	** $order => the ordering of representing the data
	** $limit => used to select specific number of rows based on this number
	*/
	function selectItems($select, $from, $where = 1, $value = array(), $order = 1, $limit = NULL){
		global $con;
		$limit = $limit != NULL ? ' LIMIT ' . $limit : '';

		$stmt = $con->prepare('SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where . ' ORDER BY ' . $order . ' ' . $limit);
		$stmt->execute($value);
		return $stmt->fetchAll();
	}


	/*
	** deleteItems Function
	** Used To Delete data from Database
	** Parameters []
	** $table => the table which delete the data from
	** $value => hold the array which execute the delete
	** $where => the condition of the delete
	*/
	function deleteItems($table, $where, $value = array()){
		global $con;

		$stmt = $con->prepare('DELETE FROM '. $table .' WHERE '. $where);
		$stmt->execute($value);
		return $stmt->rowCount();
	}

	/*
	** selectLessonsComments Function
	** Used To Select The Comments Of The Lessons In Comments Page
	** Paremeters[]
	** $where The Condition Of Selecting Comments Based On It
	** $value => The Array Value Of Where Variable array(?,?,?)..
	** $limit => The Limit Number Of Selects Of Comments that will Select From DB
	*/

	function selectItemsComments($where = 1, $value = array(), $order = NULL, $limit = NULL){
		global $con;
		$limit = $limit != NULL ? ' LIMIT ' 	. $limit : '';
		$order = $order != NULL ? ' ORDER BY ' 	. $order : '';

		$stmt = $con->prepare('
					SELECT 		
								comments.*, users.id AS userID, users.username, users.image AS memberImg, users.admin, users.FullName,
								lessons.id AS id_lesson, lessons.name AS lesson_name
					FROM 		comments
					INNER JOIN 	users
					ON 			comments.member_id = users.id
					INNER JOIN  lessons
					ON 			comments.lesson_id = lessons.id WHERE ' . $where .
					' ' . $order . $limit
				);

		$stmt->execute($value);
		return $stmt->fetchAll();
	}

	/*
	** displayMsg Function
	** Used To Display The Success And Error Messages 
	** Paremeters[]
	** $formErrors => The array contains the errors messages 
	** $successMsg => the string contains success message
	*/
	function displayMsg($formErrors = array(), $successMsg = ''){
								//echo wrong msg if something wrong happened
		if(isset($formErrors) && !empty($formErrors)){
			foreach ($formErrors as $error) {
				echo '<div class="error-msg">' . $error . '</div>';
			}
		}
		//echo success msg if the comment added to DB
		if(isset($successMsg) && !empty($successMsg)){
			echo  '<div class="success-msg text-center">' . $successMsg . '</div>';
		}
	}


	/*
	** encFileName Function
	** Used To encrypt the name of the file (images, txt, ...)
	** Paremeters[]
	** $fileName => the name of the file which make it encrypted
	*/
	function encFileName($fileName){
		return @rand(0, 9999999) . @uniqid(NUll,true) . @rand(0, 8888888) . $fileName;
	}

		/*
	** uploadImg Function
	** Used To Upload the cover and avatar images
	** Paremeters[]
	** $_files => holds the image data
	** $myprofile => holds the data of the user from users table
	** $colomn => used to determines if the cover or the image will uploaded to determine the colomn from the users table
	** $userID => holds the id of the $_SESSION['uid']
	*/
	function uploadImg($_files, $myprofile, $colomn, $userID){

		$colomn = $colomn == 'cover' ? $colomn : 'image'; //check if the image of cover or avatar
		$folder = $colomn == 'cover' ? 'cover' : 'avatar';  //get the name of the folder based on the image if it is of cover or avatar

		//get the image data
		$imgName 		= $_files['image']['name'];
		$imgSize 		= $_files['image']['size'];
		$imgType 		= $_files['image']['type'];
		$imgTemp 		= $_files['image']['tmp_name'];

		// print_r(getimagesize($imgTemp));
		// exit();

		$imgExtension 	= strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

		$allowedExtensions = array("png", "jpg", "jepg", "gif");  //Allowed Images

		$formErrors = array(); //initialize the errors array

		if(empty($imgName))									$formErrors[] = @lang('EMPTY_IMG');
		if($imgSize > 10*1024*1024)							$formErrors[] = @lang('IMG_EXCEEDED'); //IF IMAGE MORE THAN 10MB
		if(! in_array($imgExtension, $allowedExtensions)) 	$formErrors[] = @lang('ERR_EXTENSION');
	
		if(empty($formErrors)){

			//delete the old image
			$oldImg = $colomn == 'cover' ? @$myprofile[0]['cover'] : @$myprofile[0]['image'];

			if(!empty($oldImg)){
				@unlink('layout/images/profiles/' . $folder . '/' . $oldImg);
			}

			//Upload the New Image
			$newName = encFileName($imgName); //encrypt the name of the image to stor it in DB
			$imgPath = "layout/images/profiles/" . $folder . "/" . $newName; // The Path Which Upload The Image To

			if(@move_uploaded_file($imgTemp, $imgPath)){   //upload the image

				$update = updateItems('users', $colomn . ' = ?' , array($newName, $userID), 'id = ?');

				if($update <= 0)
					$formErrors[] = @lang('ERR_UPLOAD_IMG');

			}else{
				$formErrors[] = @lang('ERR_IMG_NAME');
			}
		}

		return $formErrors; //return the form errors to check if the upload done or error happened ?
	}

	/*
	** deleteImg Function
	** Used To Delete the cover and avatar images
	** Paremeters[]
	** $myprofile => holds the data of the user from users table
	** $colomn => used to determines if the cover or the image will deleted to determine the colomn from the users table
	** $userID => holds the id of the $_SESSION['uid']
	*/
	function deleteImg($myprofile, $colomn, $userID){

		$folder = $colomn == 'cover'? 'cover' : 'avatar'; 
		$formErrors = array(); //initialize the errors array

		if(!empty($myprofile[0][$colomn])){
			$oldImg = $myprofile[0][$colomn]; //get the image string before deleting

			$update = updateItems('users', $colomn . ' = ?', array('', $userID), 'id = ?');
			if($update <= 0) $formErrors[] = @lang('ERR_DEL_PRO_IMG');
			else{
				//delete the image from the folder					
				if(!empty($oldImg)){
					@unlink('layout/images/profiles/' . $folder . '/' . $oldImg);
				}
			}
		}
		return $formErrors;
	}


	/*
	** getFileIcon function v1.0
	** used in lessons.php page
	** used to get the correct icon for the file with the correct extension
	** if exist then return the icon if this extension not extis then return default icon
	** Parameters[]
	** $extension
	*/
	$imgIcon 	= 'image.png';
	$videoIcon 	= 'video.png';
	$chrome		= 'chrome.png';
	$lessonExtension = array(
							'pdf' 	=> 'pdf.png',  			'doc' 	=> 'word.png', 
							'docx' 	=> 'word.png',  		'xls' 	=> 'excel.png', 
							'xlsx' 	=> 'excel.png',  		'ppt' 	=> 'powerpoint.png', 
							'pptx' 	=> 'powerpoint.png',  	'txt' 	=> 'txt.png',
							'mp4'	=> 'mp4.png',  			'wmv'	=> $videoIcon,
							'flv' 	=> $videoIcon, 			'swf' 	=> 'swf.png',
							'avi'  	=> $videoIcon, 			'png'	=> $imgIcon, 
							'jpg'	=> $imgIcon,  			'jpeg'	=> $imgIcon,
							'gif'	=> $imgIcon,
							'htm'	=> $chrome,				'html' 	=> $chrome
						);

	function getFileIcon($extension){
		global $lessonExtension; //to see the array
		$fileName = 'unknown.svg'; //initialize the file name if this extension not exist then diplay unknown.svg
		
		foreach($lessonExtension as $key => $value){
			if($extension == $key){
				$fileName = $value;
				break;
			}
		}
		return $fileName;
	}


	/*
	** getLessonsFiles Function
	** Used To Select the lessons with join with othere tables
	** parameters[]
	** $where 	=> the condition which choose based ion it
	** $value the array which holds the value of $where variable
	** $order 	=> the order of the result to show
	** $limit => limit the result counts
	*/
	function selectLessonsFiles($where = 1, $value = array(), $order = NULL, $limit = NULL){
		global $con;
		$limit = $limit != NULL ? ' LIMIT ' 	. $limit : '';
		$order = $order != NULL ? ' ORDER BY ' 	. $order : '';

		$stmt = $con->prepare('
					SELECT 		
								lessons.*, users.id AS userID, users.FullName,
								titles.id AS id_title, titles.name AS title_name
					FROM 		lessons
					INNER JOIN 	users
					ON 			lessons.member_id = users.id
					INNER JOIN  titles
					ON 			lessons.title_id = titles.id WHERE ' . $where .
					' ' . $order . ' ' . $limit
				);

		$stmt->execute($value);
		return $stmt->fetchAll();
	}


	/*
	** checkChars Function used to check the input fields are (a-z) and (A-Z) and (0-9) otherewise return false
	** Parameters[]
	** $input => the field that need to check its value
	*/
	function checkChars($input) {
		for ($i = 0; $i < strlen($input); $i++) {
			$char = ord($input[$i]);
			if(!($char == 32 || ($char >= 97 && $char <= 122) || ($char >= 65 && $char <= 90) || ($char >= 48 && $char <= 57)))
				return 0;
		}
		return 1;
	}

	/*
	** get_emoji_details function v1.0
	** used to fet the count of the reactions of each emoji
	** parameters []
	** $commentid => the id of the comment that need to get the count of its reactions
	*/
	function get_emoji_details($commentid){
		$emojies = array();
		//get the count of all types of reactions
		$emoji['angry'] = selectItems('COUNT(emoji) AS count', 'emoji_comments', 'emoji = ? AND comment_id = ?', array(1, $commentid));
		$emoji['sad'] 	= selectItems('COUNT(emoji) AS count', 'emoji_comments', 'emoji = ? AND comment_id = ?', array(2, $commentid));
		$emoji['wow'] 	= selectItems('COUNT(emoji) AS count', 'emoji_comments', 'emoji = ? AND comment_id = ?', array(3, $commentid));
		$emoji['haha'] 	= selectItems('COUNT(emoji) AS count', 'emoji_comments', 'emoji = ? AND comment_id = ?', array(4, $commentid));
		$emoji['love'] 	= selectItems('COUNT(emoji) AS count', 'emoji_comments', 'emoji = ? AND comment_id = ?', array(5, $commentid));
		$emoji['like'] 	= selectItems('COUNT(emoji) AS count', 'emoji_comments', 'emoji = ? AND comment_id = ?', array(6, $commentid));
		
		return $emoji;		
	}

	/*
	** getParentTitle Function V1.0
	** Used to get the parent of lesson or title based on the parent ot them
	** parameters[]
	** $childID => is the id of the child which need to get its parent
	** $parentID => the id of the parent which need to get its data 
	** $table => the table which need to work on it 
	*/
	$getParentTitles = array();  // used to hold the name and id of all parent titles
	$testname = '';
	function getParentTitle($childID, $parentID, $table){
		global $getParentTitles;

		$parent = selectItems('id, name, parent', $table, 'id = ?', array($childID));
		$getParentTitles[] = array(
							'id'   => $parent[0]['id'], 
							'name' => $parent[0]['name'],
							'parent' => $parent[0]['parent']
						);

		if(!empty($parent)){
			if($parent[0]['parent'] == $parentID || $childID == $parentID)
				return $getParentTitles;

			return getParentTitle($parent[0]['parent'], $parentID, $table);
		}else{
			return array();
		}
	}

	/*
	** getNotification Function v1.0
	** used to return the notification of the member
	** Parameters []
	** $where => the id of the member that return his notifications
	** $value => the value of where variable
	** $order => get the order of the row ASC or DESC
	** $$limit => how many rows to fetch
	*/
	function getNotification($where, $value = array(), $order = NULL, $limit = NULL){
		global $con;
		$limit = $limit != NULL ? ' LIMIT ' 	. $limit : '';
		$order = $order != NULL ? ' ORDER BY ' 	. $order : '';

		$stmt = $con->prepare('
							SELECT 	notify.*, users.FullName, users.image, lessons.file, lessons.name AS lessonName,
									titles.name AS titleName
							FROM 	notify
							INNER JOIN users
							ON users.id = notify.sender
							INNER JOIN lessons
							ON lessons.id = notify.lesson_id
							INNER JOIN titles
							ON lessons.title_id = titles.id
							WHERE ' . $where . ' ' . $order . $limit
						);
		$stmt-> execute($value);
		return $stmt->fetchAll();
	}

	/*
	** getMessages Function v1.0
	** used to return the Messages of the member
	** Parameters []
	** $where => the condition to return the messages through
	** $value => the value of where variable
	** $order => get the order of the row ASC or DESC
	** $$limit => how many rows to fetch
	*/
	function getMessages($where, $value = array(), $order = NULL, $limit = NULL){
		global $con;
		$limit = $limit != NULL ? ' LIMIT ' 	. $limit : '';
		$order = $order != NULL ? ' ORDER BY ' 	. $order : '';

		$stmt = $con->prepare('
					SELECT users.FullName, users.image, messages.sender_id, messages.reciever_read, messages.id
					FROM users
					INNER JOIN messages
					ON users.id = messages.sender_id
					
					WHERE ' . $where . ' ' . $order . $limit
				);
		$stmt->execute($value);
		return $stmt->fetchAll();
	}