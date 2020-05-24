<?php
//connect up with other pages
session_start();

require '../connect.php';

$emailfound = 'true';
unset($_SESSION['email']);
unset($_SESSION['changeverify']);
unset($_SESSION['changeuser']);

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

//check if the user has submitted their email
if (isset($_POST['emailsubmit'])) {
	//grab the email
	$email = trim(stripslashes(htmlspecialchars($_POST['email'])));
	//check if it's in the DB
	$emailinfo = DB::query('SELECT email FROM users WHERE email = %s',$email);
	if (count($emailinfo) > 0) {
		$_SESSION['email'] = $email;
		//go to the next page
		header('Location: ../forgotpass2');
	} else {
		$emailfound = 'false';
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
  	<link rel="stylesheet" type="text/css" href="stylesheet_new.css">
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
											<td><h4>What's your email?&nbsp&nbsp</h4></td>
											<td><input type="email" name="email" class="form-control" required></td>
										</tr>
									</table>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-3 col-xs-offset-4">
									<input type="submit" name="emailsubmit" class="form-control" value="Start Recovering"></td>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-5 col-xs-offset-3">
									<?php
									if ($emailfound == 'false') {
										printf('<center><div class="alert alert-warning">
											<strong>Technical difficulties...</strong> Email not found!
											</div></center>');
									}
									?>
								</div>
							</div>
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
}





?>
</body>
</html>