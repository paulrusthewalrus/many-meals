let rating = 0;
let uploaded_bool = 0; //boolean
let picture = 0;

function init() {
	//find height of window
	var height = Math.max($(document).height(), $(window).height());
	//set the height of the image
	$('#div-back').css('height',height);
}

/*
//check if all inputs are good
function checkInputs() {
    //grab values
    hrs = $('#hrsinput').val();
    min = $('#mininput').val();
    steps = $('#stepinput').val().length;
    name = $('#recipename').val().length;
    //rating = $('#rating').val();
    taglist = $('#taglist').val().length;
    
    if (hrs != '' && min > 0 && steps > 0 && name > 0 && taglist > 0) {
        $('#submit').removeAttr('disabled');
    }
}*/

//check if the button should be renabled
function btnCheck() {
    //assume that it should be enabled
    enabled = 1

    rating = document.getElementById('rating').value;

    //rating
    if (rating === 0) {
        enabled = 0;
    };

    //total time
    let totaltime = parseInt($('#mininput').val())+parseInt($('#hrsinput').val());

    //minutes
    if (totaltime === 0) {
        enabled = 0;
    }

    if ($('#stepinput').val() === '0') {
        enabled = 0;
    }

    //picture
    if (picture === 0) {
        enabled = 0;
    }

    //name and description
    if ($('#recipename').val() === '' || $('#recipedesc').val() === '') {
        enabled = 0;
    }

    //make a decision on the button
    if (enabled === 1) {
        $('#submit').removeAttr('disabled');
    } else {
        $('#submit').attr('disabled','disabled');
    }
}

//check if the number is valid
function checkNum(id) {
    //grab the value
    let num = $('#'+id).val();
    if (num < 0) {
        $('#'+id).val(0);
    }
    btnCheck();
}

//when the user uploads a file to the site
function uploaded(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#recipepic')
                .attr('src', e.target.result)
                .width(180)
                .height(180);

           $('#recipetask').remove();

        };

        reader.readAsDataURL(input.files[0]);

        //update the uploaded boolean
        uploaded_bool = 1;
        btnCheck();
    }
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
        console.log('asd');
        rating = parseInt(id);
        //setup the new rating on the hidden input
        $('#rating').val(id);
    } else {
        rating = 0;
        console.log(rating);
    } 

    btnCheck();  
}

//this is where all the tags are kept for the time being
tags = [];
temptags = [];

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

    //change around the display of the tag using straight up JQUERY
    $('#'+id).toggleClass("perm")
    //console.log(tags);

    tagnames = [];

    for (var i=0; i < tags.length; i++) {
        let tag_id = tags[i];
        let tag_name = document.getElementById(tag_id).innerText
        tagnames.push(tag_name);
    }

    //export it as a big HTML input
     $('#tagnames').val(JSON.stringify(tagnames));

    //console.log(tagnames);
    //console.log('Tags: '+tags);
    //console.log('Temp Tags: '+temptags);

    //reset temp tags
    temptags = [];
    //update the tag list with THE ACTUAL id's
    for (var i=0; i < tags.length; i++) {
        //grab the variable
        var tag = tags[i];
        //remove the first letter of it
        var newtag = tag.substring(1,tag.length);
        //add it to a temp array
        temptags.push(newtag);
    }
    //make it the official tag output
    $('#taglist').val(JSON.stringify(temptags)); //store the array as JSON text. Converted using PHP later

    //check button condition
    btnCheck();
} 

function newtag() {
    alert('WOW')
}