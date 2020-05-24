<?php
//connect up with other pages
session_start();

$error = false;
unset($_SESSION['changeverify']);
unset($_SESSION['changeuser']);

if (!isset($_SESSION['email'])) {
	header('Location: ../forgotpass');
}

//grab the email
$email = $_SESSION['email'];

//check if person is logged in
if (isset($_SESSION['user']) && !isset($_GET['signout'])) {
	//a user is already logged in, redirect them away from this apge
	header('Location: ../homepage');
};

//check if the user has clicked the sign out button
if (isset($_GET['signout'])) {
	//destroy any session of the user
	session_destroy();
}

if (isset($_POST['submit'])) {

	require '../connect.php';

	//grab the variables
	$firstname = trim(stripslashes(htmlspecialchars($_POST['firstname'])));
	$surname = trim(stripslashes(htmlspecialchars($_POST['surname'])));
	$username = trim(stripslashes(htmlspecialchars($_POST['username'])));
	//grab user information from the email
	$userinfo = DB::query('SELECT * FROM users WHERE email = %s',$email);
	//grab all verified user info
	$fname_v = $userinfo[0]['firstname'];
	$surname_v = $userinfo[0]['lastname'];
	$username_v = $userinfo[0]['username'];

	//compare all the information
	if (($firstname != $fname_v) or ($surname != $surname_v) or ($username != $username_v)) {
		//something was NOT right
		$error = true;
	} else {
		//all good, send away
		$_SESSION['changeverify'] = true;
		$_SESSION['changeuser'] = $username;
		header('Location: ../forgotpass3');
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Forgot details</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_off.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script.js"></script>
</head>
<body>
	<!-- Every piece of content is stored ON the background image-->
	<div class="view first_back" style="background-image: url('../../resources/background_updated.png'); background-repeat: repeat-y; background-size: cover; background-position: center center;">
		 <!-- navigation bar (if you need to mess with the nav talk to me first) -->
		<nav class="navbar navbar">
		 	<!--image w/ dropdown-->
		 	<div class="dropdown">
		 		<?php
		 		//check whether or not the user is logged in
		 		if (isset($_SESSION['user']) && !isset($_GET['signout'])) {
		 			//a user exists
		 			printf('<table><tr><td><img class="navbar-brand profile dropbtn" src="%s"></td></tr></table>',$_SESSION['user'][3]);
		 			//using get to determine what the users' choice is
		 			echo '<div class="dropdown-content">
		 				  	<a href="../myrecipes"><span><i class="fa fa-book"></i></span>&nbspMy Recipes</a>
		 				  	<a href="../homepage/index.php?signout=1"><span><i class="fa fa-sign-out"></i></span>&nbspSignout</a>
		 				  </div>';

		 			} else {
		 				//user does not exist
		 				echo '<table><tr><td><img class="navbar-brand profile dropbtn" src="../../resources/guest.png"></td></tr></table>';
		 				//user choice for drown-down
		 				echo '<div class="dropdown-content">
		 					 	<a href="../login"><span class="glyphicon glyphicon-log-in"></span>&nbspLogin</a>
		 					 	<a href="../register"><span class="glyphicon glyphicon-user"></span>&nbspRegister</a>
		 					 	
		 					 </div>';
		 			}
		 		?>
		 	</div>
		 	<!-- the header of the navigation bar-->
		 	<div class="navbar-header">
		 		<h3 class="title"><a href="../homepage">Many Meals</a></h3>
		 	</div>
		 	<!--The main links to go to-->
		 	<ul class="nav navbar-nav">
		    	<li><a id="search" href="../search">Search</a></li>
		    	<?php
		    	//if the user is logged in, allow them to create a recipe
		    	if (isset($_SESSION['user']) && !isset($_GET['signout'])) {
		    		echo '<li><a id="create" href="../createrecipe">Create a Recipe</a></li>';
		    	}
		    	?>
		    	<li><a id="categories" href="../categories">Categories</a></li>
		 	</ul>
		</nav>
		
		<!-- Content div required to fix some CSS styling issues with links -->
		<div class="content container">
			<!-- insert html content below this line -->
			<!-- background repeat doesn't work so try to keep your content to the page you see -->
			
			<div class="row">
				<div class="col-xs-10 col-xs-offset-1">
					<div class="well">
						<center><h3>Forgot your details? We can help!</h3></center>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-10 col-xs-offset-1">
					<div class="well">
						<form name="main" method="post" action="index.php">
							<div class="row">
								<div class="col-xs-6 col-xs-offset-3">
									<table style="background-color: white" class="emailtable">
										<tr>
											<td><h4>First Name:</h4></td>
											<td><input type="text" name="firstname" class="form-control" required></td>
										</tr>
									</table>
								</div>
							</div>

							<br>

							<div class="row">
								<div class="col-xs-6 col-xs-offset-3">
									<table style="background-color: white" class="emailtable">
										<tr>
											<td><h4>Last Name:</h4></td>
											<td><input type="text" name="surname" class="form-control" required></td>
										</tr>
									</table>
								</div>
							</div>
							
							<br>

							<div class="row">
								<div class="col-xs-6 col-xs-offset-3">
									<table style="background-color: white" class="emailtable">
										<tr>
											<td><h4>Username:</h4></td>
											<td><input type="text" name="username" class="form-control" required></td>
										</tr>
									</table>
								</div>
							</div>

							<br>

							<div class="row">
								<div class="col-xs-4 col-xs-offset-4">
									<input type="submit" name="submit" class="form-control" value="Continue Recovery"></td>
								</div>
							</div>

							<br>
							<?php
							if ($error == true) {
								printf('<div class="row">
											<div class="col-xs-4 col-xs-offset-4">
												<div class="row">
													<div class="col-xs-10 col-xs-offset-1">
														<center><div class="alert alert-danger">
															  <strong>Error!</strong> Inaccurate information.
															</div></center>
													</div>
												</div>
											</div>
										</div>');
							}
							?>
						</form>	
					</div>
				</div>
			</div>

			<!-- Going past this line risks messing up some CSS styling-->
		</div>
		<!-- content ^^^ going past this line means doing stuff without a background -->
	</div>	

<?php

//check if the user has clicked the sign out button
if (isset($_GET['signout'])) {
	//destroy any session of the user
	session_destroy();
};




?>
</body>
</html>