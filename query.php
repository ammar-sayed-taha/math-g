<?php 
	session_start();
	$noHeader = '';
	include_once 'init.php';

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		// Start Lessons Page
		if(isset($_POST['comment'])){
			$comment 	= filter_var(trim($_POST['comment']), FILTER_SANITIZE_STRING);
			//when the user add new comment then we will need this variable
			$parent 	= isset($_GET['parent']) && is_numeric($_GET['parent'])? intval($_GET['parent']) : 0;
			$member_id 	= isset($_GET['member_id']) && is_numeric($_GET['member_id'])? intval($_GET['member_id']) : 0;
			$lessonid 	= isset($_GET['lessonid']) && is_numeric($_GET['lessonid'])? intval($_GET['lessonid']) : 0;


			$formErrors = array();  //initialize ForErrors array
			if(empty($comment)) $formErrors[]  = @lang('COM_FIELD_EMPTY');
			
			if(empty($formErrors)){

				$approve = $parent != 0 ? 1 : 0; //if the parent comment is approved then the child comment also approved
				$addComment = insertItems('comments', 'comment, add_date, member_id, lesson_id, parent, approve',
								'?, NOW(), ?, ?, ?, ?',
								array($comment, $member_id, $lessonid, $parent, $approve));
				if($addComment > 0){
					/* Start Sending notification for the member of the parent comment */
					$reciver = selectItems('member_id', 'comments', 'id = ?', array($parent)); //get the reciver of the notification
					
					//check first the user who replied not the owner of the parent comment
					if($member_id != $reciver[0]['member_id'])
						insertItems('notify', 'sender, reciever, lesson_id, type', '?, ?, ?, "comment"', array($member_id, $reciver[0]['member_id'], $lessonid));
					/* End Sending notification for the member of the parent comment */
				?>
					<div class="child-com">
						<span class="inner-con">
							<?php 
								$childComment = selectItemsComments('comments.approve = ? AND lessons.id = ? AND parent != ?', array(1, $lessonid, 0), 'comments.id DESC', 1);								
								
								$memberImg = empty($childComment[0]['memberImg']) ? 'default.png' : $childComment[0]['memberImg']; 
								$adminLink = $childComment[0]['admin'] == 1 ? 'profile.php?do=Manage&uid=' . $member_id : '#';
							?>
							<a href="<?php echo $adminLink; ?>">
								<img class="img-thumbnail" src="layout/images/profiles/avatar/<?php echo @$memberImg; ?>">
							</a>												
						</span>
						<span class="inner-con">
							<div class="com-header">
								
								<span class="com-n">
									<a href="<?php echo $adminLink; ?>">
										<div class="name <?php if($childComment[0]['admin'] != 0) echo 'admin'; ?>"><?php echo $childComment[0]['FullName'] ?></div>
									</a>
									<div class="date"><?php echo @date('j M Y'); ?></div>
								</span>
							</div>
							<div class="com-footer">
								<p data-id="<?php echo $childComment[0]['id'] ?>"><?php echo nl2br($comment); ?></p>

								<!-- appear when the childComment is on its lessons of that session -->
								<div class="under-com">
									<?php  if(isset($_SESSION['uid'])){ 
										//if the user is admin or the owner of the comment then he can delete it
										if($childComment[0]['admin'] != 0 || $member_id == $_SESSION['uid']) {?>
											<a class="confirm-delete delete-com" href="query.php?lessonid=<?php echo @$lessonid; ?>&deleteCom=true&comid=<?php echo $childComment[0]['id']; ?>"
												data-parent="<?php echo $childComment['parent']; ?>"
											>
												<span><?php echo @lang('DELETE_COM') ?></span>
											</a>

											<!-- Edit btn -->
											<a class="edit-btn" data-value="_<?php echo $parentComment['id'] ?>" href="#">
												<span><?php echo @lang('EDIT') ?></span>
											</a>
										<?php }	
										 } ?>
										<a class="reply-btn" data-value="_<?php echo $parent ?>" href="#">
											<span><?php echo @lang('REPLY') ?></span>
										</a>
								</div>
							</div>
						</span>
					</div>
				<?php }else{?>
					<div class="com-no-added"><?php echo @lang('COMMENT_ADD_FAILD')?></div>;

				<?php }?>
					<!-- add new child comment -->
					<div class="<?php echo $parent ?>"></div>
			<?php }
		}
		// End Lessons Page

		// Start emogi query 
		elseif(isset($_POST['emoji']) && isset($_GET['member_id']) && isset($_GET['comment_id'])){

			$emoji 		= filter_var($_POST['emoji'], FILTER_SANITIZE_NUMBER_INT);
			$member_id 	= filter_var($_GET['member_id'], FILTER_SANITIZE_NUMBER_INT);
			$comment_id = filter_var($_GET['comment_id'], FILTER_SANITIZE_NUMBER_INT);

			//check if the user reacted to this comment before
			$emoExist = selectItems('id', 'emoji_comments', 'member_id = ? AND comment_id = ?', array($member_id, $comment_id));

			if(empty($emoExist)){
				//add the react as new one
				$addEmo = insertItems('emoji_comments', 'emoji, member_id, comment_id', '?, ?, ?', array($emoji, $member_id, $comment_id));
			}else{
				//update the react
				$updateEmo = updateItems('emoji_comments', 'emoji = ?', array($emoji, $emoExist[0]['id']), 'id = ?');
			}
		}
		// End emogi query 

		// Start Mark All As Read Notification 
		elseif(isset($_GET['markAsRead']) && $_GET['markAsRead'] == 'all'){
			$uid = filter_var(@$_POST['uid'], FILTER_SANITIZE_NUMBER_INT);
			updateItems('notify', 'is_read = ?', array(1, $uid), 'reciever = ?');
		}
		// End Mark All As Read Notification 

		// Start Show More Notifications
		elseif(isset($_POST['notifyID'])){
			$lastID = is_numeric($_POST['notifyID']) ? intval($_POST['notifyID']) : 0;
			$lastNotify = getNotification('notify.reciever = ? AND notify.id < ?', array(@$_SESSION['uid'], $lastID), 'notify.id DESC', 10);
		
			// Start Displaying the Notifications
			if(!empty($lastNotify)){ 
				$minID = INF; //initialize the last id of notification
				foreach($lastNotify as $notify) { 
					//get the last id DESC to set it into show-more btn
    				$minID = $notify['id'] < $minID ? $notify['id'] : $minID; 

					// <!-- Start Display the Comments notifications -->
					$senderImg = !empty($notify['image']) ? $notify['image'] : 'default.png';

					if($notify['type'] == 'comment'){ ?>
	        			<li class="lesson-con <?php if($notify['is_read'] == 0) echo 'unread'; ?>">
	        				<a href="lessons.php?lessonid=<?php echo $notify['lesson_id'] ?>&notification_id=<?php echo $notify['id'] ?>">
	        					<span class="sender-img"><img src="layout/images/profiles/avatar/<?php echo $senderImg ?>"></span>
	        					<span class="sender-p">
	        						<p><?php echo @lang('COMMENTER_NAME', $notify['FullName']) . '<strong>' . $notify['lessonName'] . '</strong>' ?></p>
	        						
	        						<div>
	        							<!-- <span class="file-img"><i class="far fa-comments"></i></span> -->
	        							<span class="file-date"><i class="far fa-clock"></i> <?php echo date('h:m', strtotime($notify['add_date'])) ?></span>
	        							<span class="file-date"><i class="far fa-calendar"></i> <?php echo date('d M', strtotime($notify['add_date'])) ?></span>
	        						</div>
	        					</span>
		        			</a>
	        			</li>

	        		<!-- Start Display the lessons notifications -->
					<?php }elseif($notify['type'] == 'lesson'){ ?>
	        			<li class="lesson-con <?php if($notify['is_read'] == 0) echo 'unread'; ?>">
	        				<a href="lessons.php?lessonid=<?php echo $notify['lesson_id'] ?>&notification_id=<?php echo $notify['id'] ?>">
	        					<span class="sender-img"><img src="layout/images/profiles/avatar/<?php echo $senderImg ?>"></span>
	        					<span class="sender-p">
	        						<p><?php echo @lang('SENDER_NAME', $notify['FullName']) . '<strong>' . $notify['lessonName'] . '</strong>' ?></p>
	        						
	        						<div>
	        							<?php $fileExtension = strtolower(pathinfo($notify['file'], PATHINFO_EXTENSION)); ?>
	        							<span class="file-img"><img src="layout/images/icons/<?php echo getFileIcon($fileExtension); ?>"></span>
	        							<span class="file-date"><i class="far fa-clock"></i> <?php echo date('h:m', strtotime($notify['add_date'])) ?></span>
	        							<span class="file-date"><i class="far fa-calendar"></i> <?php echo date('d M', strtotime($notify['add_date'])) ?></span>
	        						</div>
	        					</span>
		        			</a>
	        			</li>
    		<?php } 

    		} ?>
    			<!-- show more notifications data-member="<?php echo $_SESSION['uid']; ?>" -->
    			<li class="show-more" data-id="<?php echo $minID ?>" >
    				<?php echo @lang('SHOW_MORE') ?>		
    			</li>

    		<?php }
			// End Displaying the Notifications
		}
		// End Show More Notifications
		

		// Start search frinds to Message with them
		elseif(isset($_POST['friend']) && !empty($_POST['friend'])){
			$search = filter_var(trim($_POST['friend']), FILTER_SANITIZE_STRING);
			$search = explode(' ', $search);
			$search = array_filter($search);
		
			// Start exploding the string and make it as shap of sql (LIKE %"str"%) to search
			$newSearch = '';
			$searchSize = count($search);
			$counter = 1;
			foreach ($search as $val) {
				$newSearch .= ' FullName LIKE "%' . $val . '%" ';
				if($counter < $searchSize) {
					$newSearch .= ' OR';
					$counter++;
				}
			}
			$results = selectItems('id, FullName, image', 'users',  $newSearch . ' AND id != ?', array($_SESSION['uid'])); ?>
			
				<?php if(!empty($results) && $search != ''){ ?>
					<div class="result-search">
						<?php foreach($results as $result){ ?>
							<div class="result-search-con" data-id="<?php echo $result['id'] ?>">
								<span><img src="layout/images/profiles/avatar/<?php echo empty($result['image']) ? 'default.png' : $result['image'] ?>"></span>
								<span class="friend-name"><?php echo $result['FullName']; ?></span>
							</div>
						<?php }?>
					</div>
				<?php } 
		}
		// End search frinds to Message with them

		// Start sending the msg to the freinds tags
		elseif(isset($_POST['tags']) && isset($_POST['sendMsg'])) {
			$friends_id = filter_var_array($_POST['tags'], FILTER_SANITIZE_NUMBER_INT);
			$msg 		= filter_var($_POST['sendMsg'], FILTER_SANITIZE_STRING);

			//execute the following statement for all tags
			if(!empty($friends_id)){
				foreach($friends_id as $friend_id){
					//check if there is row with me as sender and friend as reciever
					$sender = selectItems('id', 'messages', 'sender_id = ? AND reciever_id = ?', array(@$_SESSION['uid'], $friend_id));
					if(!empty($sender)){ //if not empty then update the is_read make it 0 
						updateItems('messages', 'sender_read = ?, reciever_read = ?, update_date = NOW()', array(1, 0, $sender[0]['id']), 'id = ?'); //make his msg unread and mine is read
					}
					else{  //this is first time to msg with this friend the add new message
						insertItems('messages', 'sender_id, reciever_id, sender_read', '?, ?, ?', array($_SESSION['uid'], $friend_id, 1));
					}

					//now repeat the operation again but as reciever not sender
					$reciever = selectItems('id', 'messages', 'sender_id = ? AND reciever_id = ?', array($friend_id, @$_SESSION['uid']));
					if(!empty($reciever)){  //that means the row is already exist then update the read status
						updateItems('messages', 'sender_read = ?, reciever_read = ?, update_date = NOW()', array(1, 0, $reciever[0]['id']), 'id = ?'); //make his msg unread and mine is read
					}
					else{  //this is first time my friend messages me
						insertItems('messages', 'sender_id, reciever_id, sender_read', '?, ?, ?', array($friend_id, $_SESSION['uid'], 1));
					}


					//insert the message to conversation table
					insertItems('conversation', 'sentence, sender_id, reciever_id', '?, ?, ?', array($msg, @$_SESSION['uid'], $friend_id));

				}
			}
			
		}
		// End sending the msg to the freinds tags

		// Start Mark All Messages As read
		elseif(isset($_POST['markMsgAsRead']) && $_POST['markMsgAsRead'] == true && isset($_POST['uid'])){
			$uid =   !empty($_POST['uid']) ? filter_var($_POST['uid'], FILTER_SANITIZE_NUMBER_INT) : 0;
			updateItems('messages', 'reciever_read = ?', array(1, $uid), 'reciever_id = ?'); //make all messages as read
		}
		// End Mark All Messages As read
	}
	elseif($_SERVER['REQUEST_METHOD'] == 'GET'){

		//Start Delete Comment script
		if(isset($_GET['deleteCom']) && $_GET['deleteCom'] == true){
			//Get the comment id which will be deleted
			$comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0;

			$delete = deleteItems('comments', 'id = ?', array($comid));
		}
		//End Delete Comment script

		//Start Edit Comment script
		if(isset($_GET['edit_comment']) && $_GET['edit_comment'] == true){

			$comment 	= isset($_GET['comment']) ? filter_var($_GET['comment'], FILTER_SANITIZE_STRING) : 0;
			$comment_id = isset($_GET['comment_id']) && is_numeric($_GET['comment_id']) ? intval($_GET['comment_id']) : 0; 

			$editComment = updateItems('comments', 'comment = ?', array($comment, $comment_id), 'id = ?');

		}
		//End Edit Comment script

		// Start Messages Page

		// Start Adding New Text Chat Msg in the chat
		if(isset($_GET['NewMsgChat']) && $_GET['NewMsgChat'] == true){
			$msg 			= trim($_GET['msg']);
			$reciever_id 	= isset($_GET['reciever_id']) && is_numeric($_GET['reciever_id']) ? intval($_GET['reciever_id']) : 0;


			//update the date of this message to show this chat first in messages menu
			updateItems('messages', 'reciever_read = ?, update_date = NOW()', array(0, $_SESSION['uid'], $reciever_id), 'sender_id = ? AND reciever_id = ?');

			$addMsg = insertItems('conversation', 'sentence, sender_id, reciever_id', '?, ?, ?', array($msg, @$_SESSION['uid'], $reciever_id,));
			//if the chat added successfully then show it
			if($addMsg > 0){ ?>
				<div class="one-chat-con">
					<div class="one-chat-right">
						<div class="inner-right">
							<span class="txt"><?php echo $msg ?></span>
							<span class="date"><?php echo @date('h:ma'); ?></span>

							<div class="options-right">
								<i class="fas fa-ellipsis-h fa-md"></i>
								<div class="opations-con">
									<?php $msgid = selectItems('id', 'conversation', 'sender_id = ? AND reciever_id = ?', array($_SESSION['uid'], $reciever_id), 'id DESC', 1); ?>
									<div class="delete-msg" data-chatid="<?php echo $msgid[0]['id'] ?>"><?php echo @lang('DELETE_MSG') ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } 

			$maxID = selectItems('id', 'conversation', 'sender_id = ? AND reciever_id = ?', array($reciever_id, $_SESSION['uid']), 'id DESC', 1); ?>
			<div class="one-chat-con" id="new-chat" data-earlierChatID="<?php echo $min ?>" <?php if(!empty($maxID)) { ?> data-lastID="<?php echo $maxID[0]['id']; ?>" <?php } ?> ></div>

		<?php }
		// End Adding New Text Chat Msg in the chat

		// Start When the member choose another frien to chat with him
		elseif(isset($_GET['anotherChat']) && $_GET['anotherChat'] == true){
			$sender_id = isset($_GET['sender_id']) && is_numeric($_GET['sender_id']) ? intval($_GET['sender_id']) : 0;
			$msgid = isset($_GET['msgid']) && is_numeric($_GET['msgid']) ? intval($_GET['msgid']) : 0;

			//mark the msgs as read
			updateItems('messages', 'reciever_read = ?', array(1, $msgid), 'id = ?');

			$chats = selectItems('id, add_date, sentence, reciever_id', 
						'conversation', '(sender_id = ? AND reciever_id = ?) OR (sender_id = ? AND reciever_id = ?)', 
						array($sender_id, $_SESSION['uid'], $_SESSION['uid'], $sender_id), 'id DESC', 10);
			$chats = array_reverse($chats); //reverse the messages to display from bottom to top

			if(!empty($chats)){
				//get the minimum id from the array
				$minChatID = $chats[0]['id'];
				foreach ($chats as $chat) $minChatID = $minChatID > $chat['id'] ? $chat['id'] : $minChatID; ?>
				<div id="get-leatest-chats" <?php if(isset($minChatID)){ ?> data-minChatId="<?php echo $minChatID ?>" <?php }?> ></div>
				<?php 
				foreach($chats as $chat){ 
					$check = $chat['reciever_id'] == @$_SESSION['uid'] ? true : false;
				?>
					<div class="one-chat-con">
						<div class="<?php echo $check == true ? 'one-chat-left' : 'one-chat-right'; ?>">
							<div class="<?php echo $check == true ? 'inner-left' : 'inner-right'; ?>">
								<span class="txt"><?php echo nl2br($chat['sentence']); ?></span>
								<span class="date"><?php echo @date('h:ma', strtotime(@$chat['add_date'])) ?></span>

								<div class="<?php echo $check == true ? 'options-left' : 'options-right'; ?>">
									<i class="fas fa-ellipsis-h fa-md"></i>
									<div class="opations-con">
										<div class="delete-msg" data-chatid="<?php echo $chat['id'] ?>"><?php echo @lang('DELETE_MSG') ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php }?>
		
				<?php $maxID = selectItems('id', 'conversation', 'sender_id = ? AND reciever_id = ?', array($sender_id, $_SESSION['uid']), 'id DESC', 1); ?>
					<!-- <div class="one-chat-con" id="friendReply" data-lastID="<?php echo $maxID[0]['id']; ?>"></div> -->
				<div class="one-chat-con" id="new-chat" <?php if(!empty($maxID)) { ?> data-lastID="<?php echo $maxID[0]['id']; ?>" <?php } ?> ></div>

			<?php }

		}
		// End When the member choose another frien to chat with him

		// Start Getting the earlier chats when the member scroll up
		elseif(isset($_GET['earlier']) && $_GET['earlier'] == true){
			$lastMsgId = isset($_GET['lastMsgId']) && is_numeric($_GET['lastMsgId']) ? intval($_GET['lastMsgId']) : 0;
			$sender_id = isset($_GET['sender_id']) && is_numeric($_GET['sender_id']) ? intval($_GET['sender_id']) : 0;

			$chats = selectItems('id, add_date, sentence, reciever_id', 
						'conversation', '((sender_id = ? AND reciever_id = ?) OR (sender_id = ? AND reciever_id = ?)) AND id < ?', 
						array($sender_id, $_SESSION['uid'], $_SESSION['uid'], $sender_id, $lastMsgId), 'id DESC', 10);
			$chats = array_reverse($chats); //reverse the messages to display from bottom to top

			if(!empty($chats)){
				//get the minimum id from the array
				$minChatID = $chats[0]['id'];
				foreach ($chats as $chat) $minChatID = $minChatID > $chat['id'] ? $chat['id'] : $minChatID; ?>
				<div id="get-leatest-chats" <?php if(isset($minChatID)){ ?> data-minChatId="<?php echo $minChatID ?>" <?php }?> ></div>

			<?php 
				foreach($chats as $chat){ 
					$check = $chat['reciever_id'] == @$_SESSION['uid'] ? true : false;
				?>
					<div class="one-chat-con">
						<div class="<?php echo $check == true ? 'one-chat-left' : 'one-chat-right'; ?>">
							<div class="<?php echo $check == true ? 'inner-left' : 'inner-right'; ?>">
								<span class="txt"><?php echo nl2br($chat['sentence']); ?></span>
								<span class="date"><?php echo @date('h:ma', strtotime(@$chat['add_date'])) ?></span>

								<div class="<?php echo $check == true ? 'options-left' : 'options-right'; ?>">
									<i class="fas fa-ellipsis-h fa-md"></i>
									<div class="opations-con">
										<div class="delete-msg"><?php echo @lang('DELETE_MSG') ?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

			<?php }else{ ?>
				<div class="end-of-top-chat text-center"><?php echo @lang('REACH_LAST_CHAT'); ?></div>
			<?php }
		}
		// End Getting the leatest chats when the member scroll up

		// Start Delete single msg from chat
		elseif(isset($_GET['delMsgInChat']) && $_GET['delMsgInChat'] == true){
			$msgid = isset($_GET['msgid']) && is_numeric($_GET['msgid']) ? intval($_GET['msgid']) : 0;
			deleteItems('conversation', 'id = ?', array($msgid));
		}
		// End Delete single msg from chat 

		// Start getting the last reply online of the friend
		elseif(isset($_GET['checkReplyChat']) && $_GET['checkReplyChat'] == true){
			$lastMsgId   = isset($_GET['lastid']) && is_numeric($_GET['lastid']) ? intval($_GET['lastid']) : 0;
			$sender_id 	 = isset($_GET['sender_id']) && is_numeric($_GET['sender_id']) ? intval($_GET['sender_id']) : 0;
		
			$chats = selectItems('id, add_date, sentence, reciever_id', 
						'conversation', 'sender_id = ? AND reciever_id = ? AND id > ?', 
						array($sender_id, $_SESSION['uid'], $lastMsgId), 'id DESC');
			$chats = array_reverse($chats); //reverse the messages to display from bottom to top

			//print_r($chats);

			if(!empty($chats)){ 
				foreach($chats as $chat){ ?>
				<div class="one-chat-left">
					<div class="inner-left">
						<span class="txt"><?php echo nl2br($chat['sentence']); ?></span>
						<span class="date"><?php echo @date('h:ma', strtotime(@$chat['add_date'])) ?></span>
					
						<div class="options-left">
							<i class="fas fa-ellipsis-h fa-md"></i>
							<div class="opations-con">
								<div class="delete-msg" data-chatid="<?php echo $chat['id'] ?>"><?php echo @lang('DELETE_MSG') ?></div>
							</div>
						</div>
					</div>
				</div>
				<?php }  
			}?>

			<!-- used just temporary to hold the last reply id -->
			<?php 
				$maxID = selectItems('id', 'conversation', 'sender_id = ? AND reciever_id = ?', array($sender_id, $_SESSION['uid']), 'id DESC', 1);
				if(!empty($maxID)){ ?>
					<div id="tempReplyMsgID" data-id="<?php echo $maxID[0]['id'] ?>"></div>

			<?php }

		}
		// End getting the last reply online of the friend

		// Start Messages Page


	}

?>

<script>
	$('.reply-btn').on('click', function() {
		var reply = $('#' + $(this).attr('data-value'));
		$('html, body').animate({
			scrollTop: reply.offset().top - $('.navbar-inverse').outerHeight() * 1.5
		});
		reply.find('textarea').focus();
	});

	//When Any Button Of Delete is clicked then confirm that item will delete already
	$('.confirm-delete').click(function () {
		return confirm('Are You Sure You Want To Delete ?');
	});

	//delete the cooment that user click on it
	$('.com .com-body .com-footer .under-com a.delete-com').click(function(e) {
		e.preventDefault();

		var url 	= $(this).attr('href'),
			delCom 	= $(this);

		$.get(url, function() {

			if(delCom.attr('data-parent') == 0){
				delCom.closest(".com-container").fadeOut(function() {$(this).remove()});
			}else{
				delCom.closest(".child-com").fadeOut(function() {$(this).remove()});
			}
		});

	});

	// Start Edit The Comment Script

	//edit the comments when the user click on the comment that need to edit
	$('.com .com-body .com-footer .under-com a.edit-btn').click(function(e) {
		e.preventDefault();

		var com = $(this).closest(".com-footer").find('p'),
			editField = $('.edit-com-field'),
			editArea = $('.edit-com-field textarea');

		editArea.val(com.html().replace(/<br>/g, ''));  //replace <br> to empty string
		editArea.attr('data-id', com.attr('data-id'));   //get the id of the comment
		editField.fadeIn();
		
	});

	//hide the edit comment field
	$('.edit-com-field .cancel').click(function() {
		$(this).closest('.edit-com-field').fadeOut();
	});

	//execute the edit when the user edit the comment
	$('.edit-com-field .edit').click(function() {
		var Comment = $(this).siblings('textarea').val(),
			com_id 	= $(this).siblings('textarea').attr('data-id'),
			commentField = $(this).closest('.edit-com-field');

		//edit the comment
		$.get('query.php', {comment: Comment, comment_id: com_id, edit_comment: 'true'}, function(data) {

			commentField.fadeOut();

			$('p').each(function () {
				if($(this).attr('data-id') == com_id){
					$(this).html(Comment);
				}
			});

		});

	});
	// End Edit The Comment Script

	// Start Notification Script
	//when the user click on show more notifications
	var showMore = $('.navbar .notify .show-more');

	showMore.click(function() {
		var lastNotifyID = $(this).attr('data-id');
			// reciever 	 = $(this).attr('data-member');

		$.post('query.php', {notifyID: lastNotifyID}, function(data) {
			showMore.after(data).remove();

		});
	});
	// // End Notification Script

	// Start Search Friends To Make Message Beteewn us

	//chhose the friend who need to message him
	$('.new-msg-field .result-search .result-search-con').click(function() {
		var tags 		= $('.new-msg-field .new-msg-tags #friends-tags'),
			searchCon 	= $(this),
			check 		= true;

		//check if the friend tag is already chosen before
		tags.find('input').each(function(){
			if($(this).val() == searchCon.attr('data-id')){
				check = false;
			}
		});

		//execute this script if the frind tag name is not chosen before
		if(check == true){
			tags.html(
					tags.html() + '<span class="friends-con"><span><i class="fa fa-times fa-xs close"></i> ' + 
					$(this).find('.friend-name').text() + '</span>' +
					'<input type="hidden" name="friends[]" value="' + $(this).attr('data-id') + '"></span>'
				);
		}
		$(this).parent().remove();
		
	});
	
	//remove tag name when click on close btn
	setInterval(function(){
		$('#friends-tags .friends-con i.close').click(function(){
			$(this).closest('.friends-con').fadeOut(function(){ $(this).remove(); });
		});
	}, 1000);


	// End Search Friends To Make Message Beteewn us

	// Show Delete msg btn when click on options menu
	$('.chat-body .options-left, .chat-body .options-right').hover(function() {
		$(this).find('.opations-con').fadeIn();
	}, function() {
		$(this).find('.opations-con').fadeOut();
	});

	// //Start showing the options menu of the chat msg
	// $('.chat-body .one-chat-con').hover(function() {
	// 	$(this).find('.options-left').show();
	// 	$(this).find('.options-right').show();
	// }, function() {
	// 	$(this).find('.options-left').hide();
	// 	$(this).find('.options-right').hide();
	// });

	//delete the message which click on delete of its btn
	$('.chat-body .opations-con .delete-msg').click(function() {
		var msgid 	= $(this).attr('data-chatid'),
			msg 	= $(this).closest('.one-chat-con');
		$('.delete-data').fadeIn();  //show the delete form

		$('.delete-data .yes-del').click(function() {
			$.get('query.php', {msgid: msgid, delMsgInChat: true}, function(data){
				msg.slideUp();
				$('.delete-data').fadeOut();
			});
		});

	});

	// End Messages Page

</script>