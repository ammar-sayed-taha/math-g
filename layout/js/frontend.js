$(function(){

	'use strict';

	var win 		= $(window),
		winWidth 	= win.outerWidth(),
		winHeight 	= win.outerHeight();

	// ************** Start Global Scripts **************

	//Turn on The SelectBoxIt Library
	$("select").selectBoxIt({
		theme: "jqueryui",

	    showEffect: "fadeIn",
	    showEffectSpeed: 200,
	    hideEffect: "fadeOut",
	    hideEffectSpeed: 200
	});

	//When the user click on Back Btn then go back to the prevois page
	$('#goBack').click(function() {
		window.history.back();
	});

	//if the input tag is required then do the following

	$('[required]').each(function () {
		var str = $(this).attr('placeholder');
		if(str.search('Required') == -1){
			$(this).attr('placeholder', str + ' (Required) ');
		}
	});

	/* any id with #pageTitle Chane the title tag with its content */
	if($('#pageTitle')){
		$('title').html($('#pageTitle').html());
	}

	//When Any Button Of Delete is clicked then confirm that item will delete already
	$('.confirm-delete').click(function () {
		return confirm('Are You Sure You Want To Delete ?');
	});

	//when cancel delete form
	$('.delete-data .cancel-del').click(function() {$(this).closest('.delete-data').fadeOut()});

	/* hide error and success maessages after a specific time from displaying */
	$('.error-msg').delay(15000).slideUp(2000);
	$('.success-msg').delay(10000).slideUp(2000);

	// Start Back to Top Scripting
	var backToTop = $('.back-to-top');

	win.scroll(function () {
		if($(this).scrollTop() > 1000)
			backToTop.fadeIn();
		else
			backToTop.fadeOut();
	});

	backToTop.click(function () {
		$('body, html').animate({scrollTop: 0}, 1000);
	});

	/* Start Navbar Scripting */

	//Make the body padding-top of the same px of the navbar
	var 
		navbar 			= $('.navbar-inverse'),
		navHeight 		= $('.navbar-inverse').outerHeight();

	 $('body, .body-container').css('margin-top', navHeight);

	var searchBtn 			= $('.navbar .search-btn'),
		searchField 		= $('.lower-nav .search'),
		searchFieldOverlay 	= $('.lower-nav .search .inneroverlay');

	searchBtn.click(function () {
		searchBtn.toggleClass('active'); //to toggle trhe class from tow seach btns
		searchField.slideToggle();

	});

	searchFieldOverlay.click(function() {
		searchField.slideToggle();
	});

	//when the user click on mark all as read btn
	$('.navbar .notify .dropdown-menu .mark-as-read span').click(function() {
		var userid = $(this).attr('data-id'); //get the id of the session

		$.post('query.php?markAsRead=all', {uid: userid}, function(){
			$('.navbar .notify .lesson-con').each(function(){
				$(this).removeClass('unread'); //remove all unread class from all li's
			});
		});
	});

	//when the user click on show more notifications
	var showMore = $('.navbar .notify .show-more');

	showMore.click(function() {
		var lastNotifyID = $(this).attr('data-id');
			// reciever 	 = $(this).attr('data-member');

		$.post('query.php', {notifyID: lastNotifyID}, function(data) {
			showMore.after(data).remove();

		});
	});

	//when click on the notification icon hide unread span
	var notifyIcon 			= $('.navbar .notify > i'),
		unreadNotifyCount 	= $('.navbar .notify .unread-notify');

	//check if there is session for unread notification
	if(sessionStorage['unreadNotifyCount'] == 'off'){
		unreadNotifyCount.hide();
	}else{
		unreadNotifyCount.show();
	}

	notifyIcon.click(function() {
		unreadNotifyCount.hide(); //hide the number
		sessionStorage['unreadNotifyCount'] = 'off';
		
	});

	//open the new message field
	var newMsgField = $('.body-container .new-msg-field');

	$('#new-msg, #new-chat-btn').click(function() {
		newMsgField.fadeIn();
		console.log('here now');
	});
	//close the new message field when click on clise btn
	$('#new-msg-close, #new-chat-close').click(function() {
		newMsgField.fadeOut();
	});

	//find frind from the database that message with them
	// var searchFriendBtn = $('.new-msg-field .new-msg-tags button');
	$('.new-msg-field .new-msg-tags button').on('click', function() {
		var input 	= $(this).siblings('.search-input'),
			btn 	= $(this);

		$(this).next('#friendResult').html('<div class="text-center"><span id="loading"></span><div>Loading</div></div>'); //show 

		$.post('query.php', {friend: input.val()}, function(data) {
			btn.next('#friendResult').html('').html(data);
		});
	});	


	//when click on send btn of new msg field make sure all things are ok
	$('.body-container .new-msg-field .new-msg-con form').submit(function (e){
		e.preventDefault();

		var url 		= $(this).attr('action'),
			msg 		= $(this).find('textarea').val(),
			sendingBtn 	= $(this).find('button[type=submit]'),
			sentBtn 	= $(this).find('button[type=hidden]'),
			tags 		= [],
			form 		= $(this);

			//collect the id's of the friends who send msg to them 
			$(this).find('#friends-tags input').each(function(){
				tags.push($(this).val());
			});

		//make sure the member choosed the friend tag and typed the msg 
		if($(this).find('textarea').val() != '' && $('#friends-tags').html() != ''){
			sendingBtn.attr('disabled', 'diabled'); //disable the send btn
			
			$.post(url, {sendMsg: msg, tags: tags}, function(data) {
				//empty the data
				

				sendingBtn.hide();
				sentBtn.show().delay(1500).hide(function() {
					sendingBtn.removeAttr('disabled').show();

					//empty the fields
					$('#friends-tags').html('');
					form.find('textarea').val('').end().find('.search-input').val(''); 
				});
				
			});


		}
	});

	//when click on Mark all Message as read then execute this script
	$('.navbar .msg .msg-footer span.msg-as-read').click(function () {
		var uid = $(this).attr('data-id'),
			unread = $('.navbar .notify .unread, .navbar .msg li a.unread');

		$.post('query.php', {uid: uid, markMsgAsRead: true}, function() {
			unread.each(function() {
				$(this).removeClass('unread');
			})
		});
	});

	/* End Navbar Scripting */


	// ************** End Global Scripts **************

	// ******** Start Scripting of Titles Page ***********

	
	var gridIcon 	 		= $('.folder-file .grid .grid-icons i'),
	 	file_folder 		= $('.folder-file a.folder, .folder-file a.file'),
		listView 			= $('.folder-file .list-view'),
		gridView 			= $('.folder-file .grid-view');

	/*
	** when click on the folder or file one click do nothing
	** but when click double click then open the file or folder
	*/
	file_folder.click(function() {
		if(winWidth > 768) return false;
        else return true;
    }).dblclick(function() {
        window.location = this.href;
        return false;
    });

    // *** Start the grid and list view ****
 
 	/* show the grid view of the titles page based on what stored in Cookies */
    if(localStorage['view'] == 'list'){
		$('.folder-file .grid .grid-icons i.list').addClass('active');
		gridView.fadeOut(100, function () { listView.fadeIn(); }); //show list and hide grid

    }else{
    	$('.folder-file .grid .grid-icons i.grid').addClass('active');
		listView.fadeOut(100, function() { gridView.fadeIn(); }); //show grid and hide list
    }

	////when click on grid icons then change the height and width of the files and folders
	gridIcon.click(function() {
		gridIcon.removeClass('active'); //remove the class active from all icons
		if($(this).attr('data-value') == 'gird-view'){			
			$(this).addClass('active');
			listView.fadeOut(100, function() { gridView.fadeIn(); }); //show grid and hide list
			localStorage['view'] = 'grid';
			
		}else{
			$(this).addClass('active');
			gridView.fadeOut(100, function () { listView.fadeIn(); }); //show list and hide grid
			localStorage['view'] = 'list';
		}
	});

	// *** Start the grid and list view ****

	// ******** End Scripting of Titles Page ***********


	/* When the Avatar Or The Cover of The Profile camera clicked 
	   then show the menu of upload or delete the image
	*/
	$('.avatar-container .camera, .pro-cover .camera').on('click', function() {
		$(this).next('.camera-menu').fadeToggle();
	});

	// ************ Start The Scripting of list menu ************

	//open the list menu of the Titles
	var menu 			= $('.navbar .menu'),
		menuLines 		= $('.navbar .menu span'),
		bodyContainer 	= $('.body-container, .navbar-inverse .container, .upper-nav'),
		bodyOverlay		= $('.body-container .overlay'),
		listMenu 		= $('.list-menu');

	//poen the menu when click on the menu btn
	menu.on('click', function () {
		// if(menuLines.hasClass('disable')){
		// 	//do what the overlay calss will do and also remove disable class from themeun to back to its normal shap
		// 	// bodyContainer.animate({right:0});
		// 	listMenu.animate({right: '-330px'});
		// 	bodyOverlay.fadeOut();
		// 	//menuLines.removeClass('disable');
		// }
		// else{
		// 	bodyOverlay.fadeIn();
		// 	listMenu.animate({right: 0});
		// 	//menuLines.addClass('disable');

		// 	// if(winWidth >= 768){
		// 	// 	bodyContainer.animate({right: '315px'});
		// 	// }else{
		// 	// 	bodyContainer.animate({right: '288px'});
		// 	// }
		// }

		bodyOverlay.fadeIn();
		listMenu.animate({right: 0});

		
	});

	//close the menu when click on overlay body
	bodyOverlay.on('click', function() {
		$(this).fadeOut();
		//bodyContainer.animate({right:0}).removeClass('blur');
		listMenu.animate({right: '-330px'});
		//remove the disable class from menu when click on Overlay
		//menuLines.removeClass('disable');
	});

	//change the theme to the color which the user chooses
	$('.list-menu .theme ul li').click(function () {
		$('link[href*="theme"]').attr('href', $(this).attr('data-value'));
        localStorage['theme'] = $(this).attr('data-value');
	});
	


	//open the child Titles when click on the parent
	var parentTitle  = $('.list-menu .parent-title > span:first-child'),
		childTitles  = $('.list-menu .child-title-con');

		parentTitle.on('click', function () {
			childTitles.slideUp(250); //close other childTitles 

			//change the arrow to describe that the menu is closed
			parentTitle.find('i').addClass('fa-angle-right').removeClass('fa-angle-down');

			//open this menu and change the arrow
			$(this).find('i').removeClass('fa-angle-right').addClass('fa-angle-down').end()
					.next('.child-title-con').slideDown(250);
			
		});

	// ************ End The Scripting of list menu ************


	/************* Start Scripting Login And Signup Forms *************/

	// When Click On Signup or login header do the following
	$('.log-regist span').click(function () {
		$(this).css('color', $(this).attr('data-color')).siblings().css('color', '#b5b5b5');

		$('.' + $(this).attr('data-class')).fadeIn().siblings().fadeOut(0);
	});

	var	input_user 	= $('.login-container input[type=text]'),
		input_pass 	= $('.login-container input[type=password]');

	// Check The Validation Of Login Username
	input_user.on('keyup blur', function () {
		if($(this).val().length < 3){
			$(this).css('borderColor', '#c71a1ac7').next('.style').css('background', '#d0494a').end()
				   .parent().parent().find('.errorMsg').css('display', 'block').fadeIn();
		
		}else{
			$(this).css('borderColor', 'green').next('.style').css('background', 'green').end()
				   .parent().parent().find('.errorMsg').fadeOut();
		}

	});

	// Check The Validation Of Login Password
	input_pass.on('keyup blur', function () {
		if($(this).val().length  < 5){
			$(this).css('borderColor', '#c71a1ac7').next('.style').css('background', '#d0494a').end()
				   .parent().parent().find('.errorMsg').css('display', 'block').fadeIn();
		}else{
			$(this).css('borderColor', 'green').next('.style').css('background', 'green').end()
				   .parent().parent().find('.errorMsg').fadeOut();
		}

	});

	/************ End Scripting Login And Signup Forms *************/



	/* Start Scripting Profile Page */

	//Add The Image To The Avatar image when change the avatar
	$('.avatar-container input[type=file]').on('change', function (event) {
		$("#change-avatar-img").attr('src',URL.createObjectURL(event.target.files[0]));
	});

	//cahnge the cover of the profile when changign the cover
	$('.pro-cover input[type=file]').on('change', function (event) {
		$("#change-cover-img").attr('src',URL.createObjectURL(event.target.files[0]));
	});

	/* End Scripting Profile Page */

	/* Start Scripting of Lessons Page */

	$('.reply-btn').on('click', function() {
		var reply = $('#' + $(this).attr('data-value'));
		$('html, body').animate({
			scrollTop: reply.offset().top - $('.navbar-inverse').outerHeight() * 1.5
		});
		reply.find('textarea').focus();
	});

	var likeBtn = $('.com .com-body .com-footer .under-com .like'),
		emoImg 	= $('.com .com-body .com-footer .under-com .like .float img');

	likeBtn.hover(function() {
		$(this).find('.float').fadeIn();
	}, function() {
		$(this).find('.float').delay(300).fadeOut();
	});

	//when click on the like btn to choose image
	emoImg.click(function (){

		//get the member and comment id
		var url 		= $(this).parent().attr('data-url'),
			activeEmo 	= $(this).parent().parent().find('.inner-like img'),
			emoName 	= $(this).parent().parent().find('.inner-like .emo-name'),
			reactCount 	= $(this).parent().parent().find('.like-count .num'),
			emoImgSrc 	= $(this).attr('src'),
			emonImgName = $(this).attr('data-value');

		$.post(url, {
			emoji: $(this).attr('id')
		}, function(){
			//increase the number of react if the it is new react
			if(activeEmo.hasClass('newReact'))
				reactCount.text(parseInt(reactCount.text()) + 1);
			//change the react icon
			activeEmo.attr('src', emoImgSrc);
			emoName.text(emonImgName);
		});
	});	

	/* End Scripting of Lessons Page */

	/* Start Index Page */

		//Start The Definition Bar Scripting
	// var parentDef 		= $('.def-bar'),
	// 	defBar 			= $('.def-bar .bar-word'),
	// 	innerDefBar 	= $('.def-bar .bar-word  ul li:first-of-type'),
	// 	rightDefBar 	= parentDef.outerWidth(),
	// 	timerDefBar 	= null; //used when the user hover on the bar then stop the bar and move otherwise

	// defBar.css('right', rightDefBar); //initialize the position of the definition bar


 //    function checkDefHover() {
      
 //      	//check the definition bar
 //      	if(parentDef.offset().left + parentDef.outerWidth() < innerDefBar.offset().left){
			
	// 		rightDefBar = parentDef.outerWidth();
	// 		defBar.css('right', rightDefBar);
	// 	}else{
	// 		rightDefBar -= 1;
	// 		defBar.css('right', rightDefBar);
	// 	}

 //        startDefBar();        // restart the timerDefBar
 //    };

 //    function startDefBar() {  // use a one-off timerDefBar
 //        timerDefBar = setTimeout(checkDefHover, 10);
 //    };

 //    function stopDefBar() {
 //        clearTimeout(timerDefBar);
 //    };

 //    defBar.on('mouseenter', stopDefBar);
 //    defBar.on('mouseleave', startDefBar);

 //    startDefBar();  // if you want it to auto-start

    //End The Definition Bar Scripting

    //Start The News Bar Scripting 
    var parentNews 		= $('.news-bar'),
		newsBar 		= $('.news-bar .bar-word'),
		innerNewsBar 	= $('.news-bar .bar-word  ul li:first-of-type'),
		rightNewsBar 	= parentNews.outerWidth(),
		timerNewsBar    = null;//used when the user hover on the bar then stop the bar and move otherwise
		newsBar.css('right', rightNewsBar); //initialize the position of the definition bar


    function checkNewsBar() {

		//check the news bar
		if(parentNews.offset().left + parentNews.outerWidth() < innerNewsBar.offset().left){
			
			rightNewsBar = parentNews.outerWidth();
			newsBar.css('right', rightNewsBar);
		}else{
			rightNewsBar -= 1;
			newsBar.css('right', rightNewsBar);
		}

        startNewsBar();        // restart the timerNewsBar
    };

    function startNewsBar() {  // use a one-off timerNewsBar
        timerNewsBar = setTimeout(checkNewsBar, 10);
    };

    function stopNewsBar() {
        clearTimeout(timerNewsBar);
    };

    newsBar.on('mouseenter', stopNewsBar);
    newsBar.on('mouseleave', startNewsBar);

    startNewsBar();  // if you want it to auto-start)

    //set time in the logo of news bar
    // get the local time to print in navbar
	var now 	= new Date(),
		$amORpm = 'pm',
		hours 	= ['12', '01', '02','03','04','05','06','07','08','09','10','11','12', /* night */
					'01', '02','03','04','05','06','07','08','09','10','11'],
		getMin = now.getMinutes() < 10 ? '0' + now.getMinutes(): now.getMinutes() ;;

	$('#time').text(hours[now.getHours()] + ':' + getMin + ' ' + $amORpm);


	setInterval(function() {
		if(now.getHours() < 12) $amORpm = 'am'; //fro 0 to 11 indexes
		getMin = now.getMinutes() < 10 ? '0' + now.getMinutes(): now.getMinutes() ;

		now = new Date();
		$('#time').text(hours[now.getHours()] + ':' + getMin + ' ' + $amORpm);
	}, 1000)

    //End The News Bar Scripting 



	/* End Index Page */

	// Start Comments Script

	//Send the child comment
	$('.inner-com form').submit(function(e){
		e.preventDefault();
		var Comment = $(this).find('textarea').val(),
			url 	= $(this).attr('action'),
			dataVal = $(this).attr('data-value');

			$.post(url, {
				comment: Comment
			}, function(data){
				$('.' + dataVal).html(data).attr('class', '');
			});

		//empty the textarea
		$(this).find('textarea').val('');
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
			com_id = $(this).siblings('textarea').attr('data-id'),
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

	//End Comments Script

	// Start Messages Page
	var chatBody = $('.messages .chat .chat-body');
	$('html, body').ready(function() {
		chatBody.animate({
			scrollTop: chatBody.offset().top + chatBody.outerHeight()
		});
	});

	var placeChat 		= $('.messages .chat .chat-footer .chat-placeholder'),
		chatField		= $('.messages .chat .chat-footer .text-input'),
		innerPlaceChat 	= placeChat.find('span');

	chatField.on('keypress keyup', function(e) {
		if(e.which === 13 && e.shiftKey){ //send the data when press shift button
			if($.trim($(this).text()) != '') sendMsg();
		}
		//hide the placeholder
		if($(this).text() != '') innerPlaceChat.css('visibility', 'hidden');
		else innerPlaceChat.css('visibility', 'visible');
		//take the same hieht
		placeChat.outerHeight($(this).outerHeight());

	});
	$('.messages .chat .chat-footer > span:first-of-type').click(function() {
		if($.trim(chatField.text()) != '')
			sendMsg();
	});

	//function used to send the msg to DB
	var newHeiht	 = 100; //used to scroll down when add new message
	function sendMsg(){
		var recieverID 	= chatField.attr('data-id'),
			chatTxt		= chatField.html(),
			msgid       = chatField.attr('data-msgid');

			chatField.html(''); //empty the chatField
			innerPlaceChat.css('visibility', 'visible'); //show the place holder of chatField
			placeChat.outerHeight(chatField.outerHeight()); //take the same height
			$('.members-con .one-member.selected .mem-msg').html(chatTxt); //update the msg in the sidebar

		$.get('query.php', {reciever_id: recieverID, msg: chatTxt, msgid: msgid, NewMsgChat: true}, function(data) {

			$('#new-chat').html(data).removeAttr('id').
					after('<div class="one-chat-con" id="new-chat" data-lastID="' + 
							$('#tempReplyMsgID').attr('data-id') + '""></div>');
			$('#tempReplyMsgID').remove(); //remove the temporary div after getting the last chat id from it			

			//chatField.html(''); //empty the chatField
			// innerPlaceChat.css('visibility', 'visible'); //show the place holder of chatField
			// placeChat.outerHeight(chatField.outerHeight()); //take the same hieht
			// $('.members-con .one-member.selected .mem-msg').html(chatTxt); //update the msg in the sidebar

			// //scroll the chat down
			var chatBody = $('.messages .chat .chat-body');
			chatBody.animate({
				scrollTop: chatBody.offset().top + chatBody.outerHeight() + newHeiht
			});
			newHeiht += 100;


		});
	}

	// Start when choose friend show his chat 
	var oneMem = $('.members-con .one-member'),
		onlyOneTime = true; // used to call the database only one time ;
	oneMem.click(function() {
		onlyOneTime = true; //change the variable to make the user when scroll uo can search more msgs

		if(!$(this).hasClass('selected')){ //if the selected member not already selected
			//focus on the selected member
			oneMem.removeClass('selected');
			$(this).addClass('selected').removeClass('unread');
			//change the msg id of the input field with the new msg
			chatField.attr('data-msgid', $(this).data('msg_id')).attr('data-id', $(this).attr('data-id'));

			//empty the chat body and show loading icon until get the data
			chatBody.html('').html('<div class="spinner"><i class="fas fa-spinner"></i></div>');

			console.log($(this).attr('data-id'));
			console.log($(this).attr('data-msg_id'));

			$.get('query.php', {sender_id: $(this).attr('data-id'), msgid: $(this).attr('data-msg_id'), anotherChat: true}, function(data) {
				chatBody.html(data);

				//var chatBody = $('.messages .chat .chat-body');
				chatBody.animate({
					scrollTop: chatBody.offset().top + chatBody.outerHeight() + newHeiht
				});
				newHeiht += 100;
			});
		}
		
	});
	// End when choose friend show his chat 

	// Show Delete msg btn when click on options menu
	$('.chat-body .options-left, .chat-body .options-right').hover(function() {
		$(this).find('.opations-con').fadeIn();
	}, function() {
		$(this).find('.opations-con').fadeOut();
	});

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

	// Start Showing More Messages when scolling top

	chatBody.scroll(function() {
		if($(this).scrollTop() == 0){
			if(onlyOneTime == true){
				onlyOneTime = false;  //close operation until getting the data from DB

				var leatestChat = $('#get-leatest-chats'),
					lastMsgId 	= leatestChat.attr('data-minChatId'),
					senderID 	= $('.members-con .one-member.selected').attr('data-id');
				//show loading icon until get the data
				leatestChat.html('<div class="spinner"><i class="fas fa-spinner"></i></div>');
				//console.log(senderID);

				if(!$(this).hasClass('end-of-top-chat')){
					$.get('query.php', {lastMsgId: lastMsgId, sender_id: senderID, earlier:true}, function(data) {
						leatestChat.after(data).remove();
						onlyOneTime = true;
						
						// chatBody.animate({
						// 	scrollTop: 500
						// }, 0);
					});
				}
				
			}
		}
		// console.log();
	});

	// check if the friend in the chat added msg every 20 sec
	function checkReply(){
		var lastID 		= parseInt($('#new-chat').attr('data-lastID')),
			senderID 	= parseInt($('.members-con .one-member.selected').attr('data-id'));

		if(typeof lastID == 'number' && typeof senderID == 'number'){
			$.get('query.php', {lastid: lastID, sender_id: senderID, checkReplyChat: true}, function(data) {

				$('#new-chat').html(data).removeAttr('id').
						after('<div class="one-chat-con" id="new-chat" data-lastID="' + 
								$('#tempReplyMsgID').attr('data-id') + '"></div>');

				if(lastID != $('#tempReplyMsgID').attr('data-id')){ //scroll down if friend sent msg
					// //scroll the chat down
					//var chatBody = $('.messages .chat .chat-body');
					chatBody.animate({
						scrollTop: chatBody.offset().top + chatBody.outerHeight() + newHeiht
					});
					newHeiht += 100;
				}
				$('#tempReplyMsgID').remove(); //remove the temporary div after getting the last chat id from it

				setTimeout(checkReply, 1000); //call the reply function after 20s
			});
		}
	}
	setTimeout(checkReply, 5000); //call the reply function after 20s



	//show the members of the messages in mobile screens
	$('.messages .show-mem').click(function() {

		$(this).find('i').toggleClass('fa-user-plus fa-user-times')

		var memCon = $('.messages .msg-members').parent();
		memCon.toggleClass('hidden-xs visible-xs');
	});
	// End Messages Page



	


});