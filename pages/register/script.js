//when the user uploads a file to the site
function uploaded(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#prof')
                    .attr('src', e.target.result)
                    .width(180)
                    .height(180);

                $('#prof').css('border-radius','50%')

            };

            reader.readAsDataURL(input.files[0]);
        }
}

//check whether or not the user has finished filling out information
function testInfo() {
    if ($('#user').val().length > 0 && $('#email').val().length > 0 &&
        $('#fname').val().length > 0 && $('#surname').val().length > 0 &&
        $('#password').val().length > 0 && $('#confirm').val().length > 0 &&
        !$('#confirmdivinput').hasClass('has-warning') && !$('#userdivinput').hasClass('has-error') &&
        !$('#emaildivinput').hasClass('has-error')) {
        //user has filled out all fields
        $('#submit').prop('disabled',false)
    } else {
        $('#submit').prop('disabled',true)
    }
}

//JQuery from here on out (AJAX especially)
$(document).ready(function() {
    //check when the user stops typing their username
    $('#user').blur(function() {
        //grab the string from the user input
        var username = $('#user').val();
        //setup the http request
        var xhttp;

        //create a new HTTP request object to use in AJAX
        xhttp = new XMLHttpRequest();

        //when any change in the ready state occurs, call the function
        xhttp.onreadystatechange = function() {
            //make sure that the request is COMPLETE/READY and that everything is OK
            if (this.readyState == 4 && this.status == 200) {
                usernameCheck(this.responseText);
            };
        };

        //open a HTTP request using get. Make sure it's asynchronous rather than synchronous.
        xhttp.open("post", "check.php", true);   //un
        //determine what's being "posted"
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        //send the request through
        xhttp.send('un='+username);

        //determine what to do with the username
        function usernameCheck(response) {
            //check if the username is taken
            if (response == 'taken') {
                //modify the glyph
                $('#userglyph').removeClass('glyphicon glyphicon-user')
                $('#userglyph').addClass('glyphicon glyphicon-warning-sign')
                $('#userdivinput').addClass('has-error')
            } else {
                //modify the glyph
                $('#userglyph').removeClass('glyphicon glyphicon-warning-sign')
                $('#userglyph').addClass('glyphicon glyphicon-user')
                $('#userdivinput').removeClass('has-error')
                
            }



        }
    })

    //when the user hovers over the user danger glyph
    $('#userglyphcontainer').hover(function() {
        //check if the glyph is in warning mode
        if ($('#userdivinput').hasClass('has-error')) {
            //display the error message
            $('#warning-text').text('This username is already taken');  
            $('#warning-text').fadeIn(80);
        } 
        }, function() {
            $('#warning-text').css('display','none');
        })

    //check when the user stops typing their email
    $('#email').blur(function() {
        //grab the string from the user input
        var email = $('#email').val();
        //setup the http request
        var xhttp;

        //create a new HTTP request object to use in AJAX
        xhttp = new XMLHttpRequest();

        //when any change in the ready state occurs, call the function
        xhttp.onreadystatechange = function() {
            //make sure that the request is COMPLETE/READY and that everything is OK
            if (this.readyState == 4 && this.status == 200) {
                emailCheck(this.responseText);
            };
        };

        //open a HTTP request using get. Make sure it's asynchronous rather than synchronous.
        xhttp.open("post", "emailcheck.php", true);   //un
        //determine what's being "posted"
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        //send the request through
        xhttp.send('email='+email);

        //determine what to do with the username
        function emailCheck(response) {
            //check if the username is taken
            if (response == 'taken') {
                //modify the glyph
                $('#emailglyph').removeClass('glyphicon glyphicon-envelope')
                $('#emailglyph').addClass('glyphicon glyphicon-warning-sign')
                $('#emaildivinput').addClass('has-error')
            } else {
                //modify the glyph
                $('#emailglyph').removeClass('glyphicon glyphicon-warning-sign')
                $('#emailglyph').addClass('glyphicon glyphicon-envelope')
                $('#emaildivinput').removeClass('has-error')
                
            }



        }
    })

    //when the user hovers over the email danger glyph
    $('#emailglyphcontainer').hover(function() { 
        //check if the glyph is in warning mode
        if ($('#emaildivinput').hasClass('has-error')) {
            //display the error message
            $('#warning-text').text('This email is already in use');  
            $('#warning-text').fadeIn(80);
        } 
        }, function() {
            $('#warning-text').css('display','none');
        })

    $('#confirm').focusout(function(){
        var confirm = $('#confirm').val();
        var pw = $('#password').val();

        testInfo();
        //compare them
        if (pw !== confirm && pw !== '') {
            //modify the glyph
            $('#confirmglyph').removeClass('glyphicon glyphicon-lock')
            $('#confirmglyph').addClass('glyphicon glyphicon-warning-sign')
            $('#confirmdivinput').addClass('has-warning')
        } else {
            //modify the glyph
            $('#confirmglyph').removeClass('glyphicon glyphicon-warning-sign')
            $('#confirmglyph').addClass('glyphicon glyphicon-lock')
            $('#confirmdivinput').removeClass('has-warning')
                
        }
    });
})