function init() {
	//find height of window
	var height = Math.max($(document).height(), $(window).height());
	//set the height of the image
	$('#div-back').css('height',height);
}

function order(option) {
	console.log(option);
}