<?php

require "../connect.php";

//grab the username received from the AJAX query
$email = stripslashes(trim(htmlspecialchars($_POST['email'])));

//send the query off to the database
$result = DB::query("SELECT * FROM users WHERE email = %s",$email);

//check if a result exists
if ($result) {
	//someone already has this username, therefore it is taken
	echo 'taken';
	//username is available
} else if ($user !== "") {
	echo 'available';
} 



?>