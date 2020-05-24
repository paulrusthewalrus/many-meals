//variables for storage
let rating = 0;
//boolean for reply
let replystate = 0;

function init() {
	//find height of window
	var height = Math.max($(document).height(), $(window).height());
	//set the height of the image
	$('#div-back').css('height',height);
	console.log('New!')
}

//user wants to delete a comment
function delComment(fullid) {
	if (confirm('Are you sure you want to delete your comment?')) {
		//yes, they are sure, grab the comment id
		let idlength = fullid.length;
		let id = fullid.substring(5,idlength);
		//create an input on the form
		let input = '<input name="deletecomment" type="hidden" value="'.concat(id,'">')
		$('#delcomment').append(input);
		//force submit the form with the delete information
		document.getElementById('commentForm').submit();
	} 
}

//user wants to delete a comment
function delRecipe(fullid) {
	if (confirm('Are you sure you want to delete your recipe? This cannot be undone.')) {
		//yes, they are sure, grab the comment id
		let idlength = fullid.length;
		let id = fullid.substring(5,idlength);
		//create an input on the form
		let input = '<input name="deleterecipe" type="hidden" value="'.concat(id,'">')
		$('#delcomment').append(input);
		//force submit the form with the delete information
		document.getElementById('commentForm').submit();
	} 
}

function update(id) {
	//cross the step out once it has been completed
	$('.'+id).toggleClass('completed');
}

function starEnter(id) {
	var num = parseInt(id);
	for (var i = num; i > 0; i--) {
		$('#'+i.toString()).attr("src","../../resources/yellow.png")
	}
}

function starLeave(id) {
	var num = parseInt(id);
	//check if a rating does not exist
	if (rating !== 0) {
		for (var i = 5; i > 0; i--) {
			if (i > rating) {
				$('#'+i.toString()).attr("src","../../resources/gray.png")
			}
		}
	} else {
		for (var i = 5; i > 0; i--) {
			$('#'+i.toString()).attr("src","../../resources/gray.png")
		}
	}
}

function starClick(id) {
	//change the rating bool around
	if (rating === 0) {
		rating = parseInt(id);
		//setup the new rating on the hidden input
		$('#rating').val(id);
	} else {
		rating = 0;
	}	
}

function cancelReply() {

	//works ----
	$('#cancel').remove();
	$('#inputtitle').removeAttr('disabled');
	$('#inputcontent').attr('placeholder','Your comment...');
	$('#finalpost').attr('value','Post comment');
	$('.stars').removeAttr('hidden');
	$('#inputtitle').attr('type','text');
	$('#reply').val('');

	replystate = 0;
}

function replyClick(id) { //id = comment ID = reply ID
	//check if the person can make a reply
	if (replystate == 0) {
		//scroll up
		$('html,body').animate({
	        scrollTop: $(".commentcreator").offset().top},
	        200);

		//give the id to the reply
		$('#reply').val(id);

		//change the comment area 
		$('#inputtitle').attr('disabled','disabled');

		$('#inputtitle').attr('type','hidden')

		$('#inputcontent').attr('placeholder','Your reply...');
		$('#finalpost').attr('value','Post reply');
		$('.submitrow').append(`<div id="cancel" class="col-xs-12 submitrow"><br>			
		<strong><input type="submit" class="new-comment form-control" value="Cancel Reply" onclick="cancelReply();"></strong>
		</div>`)

		//turn off the stars
		$('.stars').attr('hidden','hidden');

		//change reply state
		replystate = 1;
	}
}
