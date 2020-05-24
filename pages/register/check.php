<?php

require "../connect.php";

//grab the username received from the AJAX query
$user = stripslashes(trim(htmlspecialchars($_POST['un'])));

//send the query off to the database
$result = DB::query("SELECT * FROM users WHERE username = %s",$user);

//check if a result exists
if ($result) {
	//someone already has this username, therefore it is taken
	echo 'taken';
	//username is available
} else if ($user !== "") {
	echo 'available';
} 



?>