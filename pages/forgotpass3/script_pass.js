function init() {
	//find height of window
	var height = Math.max($(document).height(), $(window).height());
	//set the height of the image
	$('#div-back').css('height',height);
}

function passCheck() {
	//grab the passwords
	let passone = $('#p1').val();
	let passtwo = $('#p2').val();
}