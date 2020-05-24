<?php
//connect up with other pages
session_start();

require '../connect.php';

$salt = "SpaghettiOnRiceNotVeryNice"; 

$emailfound = 'true';
$notsame = false;
unset($_SESSION['email']);

//check if they are meant to be here
if (!isset($_SESSION['changeverify'])) {
	header('Location: ../homepage');
}

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

//grab the username
$username = $_SESSION['changeuser'];

//grab the variables
if (isset($_POST['changepass'])) {
	$p_one = sha1($_POST['p1']);
	$p_two = sha1($_POST['p2']);
	//compare to make sure they match
	if ($p_one == $p_two) {
		//make the necessary change to the DB
		DB::update('users',array(
			'password' => $p_one.$salt
		), 'username = %s',$username);
		//fix all session variables
		unset($_SESSION['email']);
		unset($_SESSION['changeverify']);
		unset($_SESSION['changeuser']);
		//send away back to the login page
		header('Location: ../login/index.php?change=1');
	} else {
		$notsame = true;
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
  	<link rel="stylesheet" type="text/css" href="stylesheet_new2.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script_pass.js"></script>
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
											<td><h4>Username: </h4></td>
											<td><input type="text" name="email" value="<?php echo $username; ?>"class="form-control" readonly required></td>
										</tr>
									</table>
								</div>
							</div>

							<br>

							<div class="row">
								<div class="col-xs-6 col-xs-offset-3">
									<table style="background-color: white" class="emailtable">
										<tr>
											<td><h4>New Password: </h4></td>
											<td><input type="password" name="p1" id="p1" class="form-control" required onkeyup="passCheck();"></td>
										</tr>
									</table>
								</div>
							</div>

							<br>

							<div class="row">
								<div class="col-xs-6 col-xs-offset-3">
									<table style="background-color: white" class="emailtable">
										<tr>
											<td><h4>Confirm Password:: </h4></td>
											<td><input type="password" name="p2" class="form-control" id="p2" required onkeyup="passCheck();"></td>
										</tr>
									</table>
								</div>
							</div>

							<br>

							<div class="row">
								<div class="col-xs-3 col-xs-offset-4">
									<input type="submit" name="changepass" class="form-control" value="Change Password"></td>
								</div>
							</div>
							<?php
							if ($notsame == true) {
							printf('<br>

							<div class="row">
								<div class="col-xs-5 col-xs-offset-3">
									<div class="alert alert-warning">
										  <center><strong>Warning!</strong> Passwords were not the same.</center>
									</div>
								</div>
							</div>');
							};
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
}




?>
</body>
</html>