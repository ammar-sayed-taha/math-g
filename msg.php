<?php
	ob_start();
	session_start();

	//redirect the url if there is no session
	if(!isset($_SESSION['uid'])){
		header('location:index.php');
		exit();
	}
	$pageTitle = 'Messages';
	include_once 'init.php';

	$sender_id = isset($_GET['sender_id']) && is_numeric($_GET['sender_id']) ? intval($_GET['sender_id']) : 0;
	$msgID = isset($_GET['msgID']) && is_numeric($_GET['msgID']) ? intval($_GET['msgID']) : 0;

	if($msgID == 0 || $sender_id == 0){
		header('location:index.php');
		exit();
	}

	//mark the message as read if sender_id is exist
	updateItems('messages', 'reciever_read = ?', array(1, $msgID), 'id = ?');

	$msgMembers = getMessages('messages.reciever_id = ?', array(@$_SESSION['uid']), 'messages.update_date DESC');
	$chats = selectItems('id, add_date, sentence, reciever_id', 
					'conversation', '(sender_id = ? AND reciever_id = ?) OR (sender_id = ? AND reciever_id = ?)', 
					array($sender_id, $_SESSION['uid'], $_SESSION['uid'], $sender_id), 'id DESC', 10);
	$chats = array_reverse($chats); //reverse the messages to display from bottom to top

	/*
	** get the minimun id from the chats array to set it in id=get-leatest-chats div
	** used when the user make scoll top to get the earlier messages
	*/
	if(!empty($chats)){
		$minChatID = $chats[0]['id'];
		foreach ($chats as $chat)
			$minChatID = $minChatID > $chat['id'] ? $chat['id'] : $minChatID;
	}
	//print_r($chats);
?>

<!-- Set The Page title in title Tag -->
<span id="pageTitle" hidden><?php echo @lang('TITLE_MSG_PAGE') ?></span>

<section class="messages">
	<div class="show-mem visible-xs"> <!-- used to show the members of msgs in mobiles -->
		<i class="fas fa-user-plus fa-lg fa-gw"></i>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-4 hidden-xs">
				<div class="msg-members">
					<div class="members-head">
						<span><?php echo @lang('MESSAGE') ?></span>
						<span id="new-chat-btn"><i class="far fa-edit fa-lg" title="<?php echo @lang('NEW_MSG') ?>"></i></span>
					</div>
					<div class="search-members">
						<form class="form-group">
							<!-- <input class="form-control" type="search" name="search" placeholder="<?php echo @lang('SERCH_FRIEND') ?>" autocomplete="off"> -->
						</form>
					</div>

					<div class="members-con">
						<?php foreach($msgMembers as $msg){ 
							$lastTalk = selectItems('add_date, sentence', 
			        							'conversation', '(sender_id = ? AND reciever_id = ?) OR (sender_id = ? AND reciever_id = ?)', 
			        							array($msg['sender_id'], $_SESSION['uid'], $_SESSION['uid'], $msg['sender_id']), 'id DESC', 1);

						?>
							<div 
								class="one-member <?php if($msg['reciever_read'] == 0) echo 'unread'; ?> <?php if($msg['sender_id'] == $sender_id) echo 'selected' ?>" 
								data-id="<?php echo $msg['sender_id'] ?>" 
								data-msg_id="<?php echo $msg['id'] ?>"
							>
								<span class="mem-img"><img src="layout/images/profiles/avatar/<?php echo empty($msg['image']) ? 'default.png' : $msg['image'] ?>"></span>
								<span class="mem-info">
									<div class="name-date">
										<span class="mem-name"><?php echo @$msg['FullName']; ?></span>
										<span class="mem-date">
											<bdi><?php echo @date('h:ma', strtotime(@$lastTalk[0]['add_date'])) ?></bdi>
											<bdi><?php echo @date('M', strtotime(@$lastTalk[0]['add_date'])) ?></bdi>
										</span>
									</div>
									<div class="mem-msg"><?php echo filter_var(@$lastTalk[0]['sentence'], FILTER_SANITIZE_STRING); ?></div>
								</span>
							</div>
						<?php }?>
					</div>
					
				</div>
			</div>
			<div class="col-sm-8 col-xs-12">
				<div class="chat">
					<div class="chat-con">
						<?php if(!empty($chats)){ ?>
							<div class="chat-body">
								<!-- use this div to get the earlier msgs of chat when scroll up -->
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

								<!-- this div will hold the new reply of friend ervery time he writes anything -->
								<?php $maxID = selectItems('id', 'conversation', 'sender_id = ? AND reciever_id = ?', array($sender_id, $_SESSION['uid']), 'id DESC', 1); ?>
								<div class="one-chat-con" id="new-chat" <?php if(!empty($maxID)) { ?> data-lastID="<?php echo $maxID[0]['id']; ?>" <?php } ?> ></div>
								
							</div>
							<div class="chat-footer">
								<span><img src="layout/images/icons/send.png" title="<?php echo @lang('CHAT_SEND_BTN') ?>"></span>
								<span>
									<span class="chat-placeholder"><span><?php echo @lang('PLACE_CHAT') ?></span></span>
									<div class="text-input" contenteditable="true" tabindex="0" data-id="<?php echo $sender_id ?>" data-msgid="<?php echo @$msgID ?>"></div>
								</span>
								
							</div>
						<?php }?>
					</div>
				</div>
			</div>
			
		</div>
		
	</div>
	
</section>

<!-- Start New Message Section -->
<div class="new-msg-field">
	<!-- Overlay of the section -->
	<span class="msg-overlay"></span>

	<div class="new-msg-con">
		<form class="form-group" method="POST" action="query.php">
			<div class="new-msg-header">
				<span><i class="fa fa-edit fa-sm"></i> <?php echo @lang('NEW_MESSAGE') ?></span>
				<span id="new-chat-close"><i class="fa fa-times fa-md"></i></span>
			</div>
			<div class="new-msg-tags">
				<span><i class="far fa-user fa-sm"></i> <?php echo @lang('TO') ?></span>
				<span>
					<!-- holds the friends tags -->
					<div id="friends-tags"></div>

					<input class="form-control search-input" type="text" autocomplete="off" placeholder="<?php echo @lang('NAME') ?>">
					<button class="btn btn-default btn-sm" type="button"><?php echo @lang('SEARCH') ?></button>
					<!-- holds the result search -->
					<div id="friendResult"></div>
				</span>
			</div>
			<div class="new-msg-text">
				<textarea class="form-control" name="msg" placeholder="<?php echo @lang('WRITE_MSG') ?>"></textarea>
			</div>
			<div class="new-msg-footer">
				<button class="btn btn-primary btn-sm" type="submit"><?php echo @lang('SEND'); ?></button>
				<button class="btn btn-success btn-sm" style="display: none;" type="hidden" disabled="disabled"><i class="fa fa-check fa-xs"></i> <?php echo @lang('SENT'); ?></button>
			</div>
		</form>
	</div>
</div>
<!-- End New Message Section -->

<!-- Start Confirm Delete Field -->
<div class="delete-data">
	<div class="delete-data-con">
		<div class="del-title"><?php echo @lang('CONFIRM_DELETE_TITLE') ?></div>
		<div class="del-txt"><?php echo @lang('CONFIRM_DELETE_TXT') ?></div>
		<div class="del-op">
			<span class="cancel-del"><?php echo @lang('CANCEL'); ?></span> |
			<span class="yes-del"><?php echo @lang('DELETE_MSG'); ?></span>
		</div>
	</div>
</div>
<!-- End Confirm Delete Field -->




<?php
	include_once $tpt_path . 'footer.php';
	ob_end_flush(); 
?>