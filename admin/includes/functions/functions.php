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
	** SelectWithJoin Function V1.0
	** Used To Select Data From Database Depending On Join Between Two Tables
	** The Parameters
	** $select  => select the colomns from the table
	** $from 	=> the table which select from
	** $join 	=> the table which make join with
	** $on 		=> the condition which tie the two tables together
	** $where 	=> the condtion which select depending on it
	** $order   => how you need to show the data depending on the order ASC or DESC 
	**
	*/

	function selectWithJoin($select, $from, $join, $on, $where = 1, $order = 1){
		global $con;

		$stmt = $con->prepare('SELECT ' . $select . ' FROM '. $from . 
								' INNER JOIN '. $join . ' ON ' . $on . 
								' WHERE ' . $where . ' ORDER BY ' . $order);
		$stmt->execute();
		return $stmt->fetchAll();
	}


	/*
	** getParentTitle Function V1.0
	** Used to get the parent of lesson or title based on the parent ot them
	** parameters[]
	** $childID => is the id of the child which need to get its parent
	** $parentID => the id of the parent which need to get its data 
	** $table => the table which need to work on it 
	*/
	function getParentTitle($childID, $parentID, $table){
		$parent = selectItems('id, name, parent', $table, 'id = ?', array($childID));

		if(!empty($parent)){
			if($parent[0]['parent'] == $parentID)
				return $parent;
			return getParentTitle($parent[0]['parent'], $parentID, $table);
		}else{
			return array();
		}
	}


	/*
	** encFileName Function
	** Used To encrypt the name of the file (images, txt, ...)
	** Paremeters[]
	** $fileExtension => the name of the file which make it encrypted
	*/
	function encFileName($fileExtension){
		return rand(0, 999999) . uniqid(NUll,true) . rand(0, 88888) . '.' . $fileExtension;
	}

	/*
	** allowedExtension function v1.0
	** Userde to check if the files uploaded are in these type of allowed files
	** Parameters[]
	** $extension => the extension wich need to check if it is allwoed
	**
	*/
	function allowedExtension($extension){
		$allowedExentions = array('swf', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'html', 'htm', /*for files*/
				'mp4', 'wmv', 'flv', 'avi', /*for videos*/
				'png', 'jpg', 'jpeg', 'gif', /*for images*/
				'' /*if the admin choosed embeded link and didn't upload file then the extension will be empty*/);
		return in_array($extension, $allowedExentions) ? true : false;
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
								comments.*, users.id AS userID, users.username, users.image AS memberImg ,
								lessons.id AS id_lesson, lessons.name AS lesson_name
					FROM 		comments
					INNER JOIN 	users
					ON 			comments.member_id = users.id
					INNER JOIN  lessons
					ON 			comments.lesson_id = lessons.id WHERE ' . $where .
					' ' . $order . ' ' . $limit
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
	** displayManagePageTitles Function v1.0
	** Used to include the titleManagePage.php file every time call this page
	** parameters[]
	** $title  => the title array which display the data of it
	** $parent => ths parent array which will display the dirct parent of the $title variable
				  and this parent will be displayed besides the child (eg. child -> parent)
	*/
	function displayManagePageTitles($parent, $title){
		include 'titlesManagePage.php';
	}




	/*\
	** redirectHTTP  v1.0
	** used to redirect to the link which sent in the klink parameter
	** this function has parameters
	**[msg, url, seconds]
	*/
	function redirectHTTP($msg, $url = null, $seconds = 1.5){
		$link = 'The Page';

		if($url == null){
			$url = 'index.php';
			$link = 'Home Page';
		}
		elseif($url == 'back'){
			if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
				$url = $_SERVER['HTTP_REFERER'];
				$link = 'Previos Page';
			}else{
				$url = 'index.php';
				$link = 'Home Page';
			}
		}

		echo $msg;

		echo '<div class="alert alert-info" role="alert">this page will be directed automatically To '. $link .' in ' . $seconds .' Seconds </div>';

		header('Refresh:' . $seconds . ';URL=' . $url);
		exit();
	}





	









//******************************** Not Used Here ********************************
//******************************** Not Used Here ********************************
//******************************** Not Used Here ********************************
//******************************** Not Used Here ********************************
//******************************** Not Used Here ********************************
//******************************** Not Used Here ********************************
//******************************** Not Used Here ********************************



	/*
	** subString Function v1.0
	** Used To Cut substring from a string depending on the $flag parameter
	** Parameters [$string, $flag]
	*/
	function subString($string, $flag){
		$substr = '';
		for($i=0; $i<strlen($string); $i++){
			if($string[$i] != $flag){
				$substr .= $string[$i];
			}
			else{
				return $substr;
			}
		}
		return $substr;

	}


	
	/*
	** getLatest Function v1.0
	** Used to get the latest Data from Database
	** $select => select the colomns from DB
	** $from => the name of the table
	** $where => the condition of the select
	** $order => the colomn that will order about it
	*/
	function getLatest($select, $from, $where, $order, $limit){
		global $con;
		$stmt = $con->prepare('SELECT ' . $select . ' FROM ' . $from . ' WHERE ' . $where . 
							  ' ORDER BY ' . $order . ' DESC LIMIT ' . $limit);
		$stmt->execute();
		return $stmt->fetchAll();
	}

