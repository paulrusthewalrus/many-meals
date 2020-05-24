<?php
session_start();

// UID, Username, EditLevel, Email, ProfilePicture
$_SESSION['user'] = array('1831827','Linguini','1','../../resources/festivefilez2017.png')

//should we store user information as an array???

?>

<!DOCTYPE html>
<html>
<head>
	<title>redirecting...</title>
</head>
<body onload='window.location.replace("homepage");'>

	<!--<form method="post" action="index.php" name="main">
		<input type="submit" name="login" value="Login">
		<input type="submit" name="destroy" value="Destroy Session">
	</form>-->
</body>
</html>