function init() {
	//find height of window
	var height = Math.max($(document).height(), $(window).height());
	//set the height of the image
	$('#div-back').css('height',height);
}

//this is where all the tags are kept for the time being
tags = [];

//update the contents the array on the HTML page to communicate with PHP
function update(id) {
	//setting up the boolean
	let bool = true;
	//check if a TAG even exists in the tags array
	if (tags.length > 0) {
		//check if the tag is already in the tag list
		for (var i = 0; i < tags.length; i++) {
			//go through all the tags
			if (id == tags[i]) {
				//not in there, let it go in
				bool = false;
			}
		}

		//add the id to the tags list
		if (bool) {
			tags.push(id);
		} else {
			//remove the id from the tags list
			var idIndex = tags.indexOf(id);
			tags.splice(idIndex,1);
		}

	} else {
		//nothing in there, just add straight to
		tags.push(id);
	}

	//update the hidden input on the page
	$('#selected').val(JSON.stringify(tags)); //store the array as JSON text. Converted using PHP later

	//change around the display using straight up JQUERY
	$('#'+id).toggleClass("perm")
} 

function newtag() {
	alert('WOW')
}