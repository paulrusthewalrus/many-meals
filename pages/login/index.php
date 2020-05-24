<?php
//connect up with other pages
session_start();

//check if a user is logged in
if (isset($_SESSION['user']) && !isset($_GET['signout'])) {
	//a user is already logged in, redirect them away from this apge
	header('Location: ../homepage');
};
$error = 0;

$salt = "SpaghettiOnRiceNotVeryNice";

//passwords:
//xfilez | itgod
//chris | chris
//hellow | aidenHAvu17
//admin | tomato

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Login</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_L.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
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
		 			printf('<table><tr><td><img class="navbar-brand profile dropbtn" src="%s"></td></tr></table>',$_SESSION['user']);
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
		    	<li><a id="categories" href="../catalogue">Catalogue</a></li>
		 	</ul>
		</nav>
		
		<!-- Content div required to fix some CSS styling issues with links -->
		<div class="content container">
			<!-- insert html content below this line -->
			<!-- background repeat doesn't work so try to keep your content to the page you see -->
			

			<div class="well well-sm">
				<h3 style="text-align: center">Welcome Back! Sign in below to get started</h3>
			</div>

			<?php
			//check if a user has been redirected via a registration
			if (isset($_GET['r'])) {
				printf('
				<div class="row">
					<div class="col-xs-4 col-xs-offset-4">
						<div class="alert alert-success" style="text-align: center">
							<strong>Success!</strong> Your account is now registered with our foodies. Login below to start your cooking journey
						</div>
					</div>
				</div>
				');
			} else {
				echo '<br>';
			}
			?>

			<div class="row content">
				<div class="col-lg-8 col-lg-offset-2">
					<div class="well well-sm login-stuff">

						<form method="post" action="index.php" name="login" id="form">
							<h4>Enter your details here:</h4>
							<hr>
							<br>
							<!-- Where all the information gathering begins-->
							<div class="row">
								<!-- Text questions -->

								<div class="textq col-lg-20 col-lg-offset-2">
									<?php
									if (isset($_GET['change'])) {
										printf('<div class="row">
											<div class="col-xs-6 col-xs-offset-2">
												<div class="alert alert-success">
													<strong>Success!</strong> Password has been changed.
											    </div>
											</div>
										</div>');
									}
									?>
									<!-- Login information -->
									<div class="row">
										<div class="col-xs-5">
											<div class="input-group" id="userdivinput">
						      					<input id="user" type="text" class="form-control userinput" name="user" placeholder="Username" required>
						      					<span id="userglyphcontainer" class="input-group-addon"><i id="userglyph" class="glyphicon glyphicon-user"></i></span> 
					      					</div>
					    				</div>

										<div class="col-xs-5">
											<div class="input-group" id="passdivinput">
												<input id="pass" type="password" class="form-control userinput" name="password" placeholder="Password" required>
												<span id="passglyphcontainer" class="input-group-addon"><i id="passglyph" class="glyphicon glyphicon-lock"></i></span>
											</div>
					    				</div>
				    				</div>

				    				

				    				<br>

				    				<!-- Forgot password -->
				    				<div class="row">
				    					<div class="col-xs-4 col-xs-offset-3">
				    						<a href="../forgotpass" id="fgt">Forgot password</a>
				    					</div>
				    				</div>

				    				<br>

				    				<?php
				    				if (isset($_POST['submit'])) {
										require "../connect.php";

										//grab the variables
										$user = trim(stripslashes(htmlspecialchars($_POST['user'])));
										$pass = sha1($_POST['password']).$salt; //with extra salt


										//attempt to grab the user
										$row = DB::queryFirstRow('select * from users where username = %s',$user);

										//echo $row["password"]."<br>";

										//echo $pass;

										//assume user is OK
										$error = 0;

										//check if a user exists
										if (!$row){
											//user name error
											$error = 1;
											//clobber
											session_destroy();
										} else {
											//live user - let's check passwords
											if($pass == $row['password']){
												//a user exists, make sure the website recognises them
		
												//find the user ID
												$userinfo = DB::query("SELECT * FROM users WHERE username = '".$user."'");

												$imglink = $userinfo[0]["profilepicture"];

												$_SESSION["user"] = $imglink;
												
												$firstname = $userinfo[0]["firstname"];

												/*//go back to the homepage
												echo '<script>
												        setTimeout(function(){window.location.href = "../homepage/?user='.$uid.'";},1000); 
												      </script>';*/

												echo '<div class="row"><div class="col-xs-6 col-xs-offset-2"><div class="alert alert-success">
													<strong>Success!</strong> Welcome back '.$firstname.'!
												</div></div></div>';

												// put this script on the page to send back to homepage
												echo '<script>
												        setTimeout(function(){window.location.href = "../homepage";},1000); 
												      </script>';

											} else {
												//second circle of hell
												$error = 1;
												//clobber
												session_destroy();

											}
										}
									}

				    				if ($error == 1 && !isset($_GET["r"])) {
				    				echo '<div class="row form-group">
				    					<div class="col-xs-6 col-xs-offset-2">
				    						<div class="alert alert-danger">
												<strong>Error!</strong> Username or Password is incorrect.
											</div>
				    					</div>
				    				</div>';
				    				}
				    				?>

				    				

				    				<!-- Login button-->
				    				<div class="row form-group">
				    					<div class="col-xs-2 col-xs-offset-4">
				    						<input type="submit" name="submit" value="Login" class="form-control input-lg">
				    					</div>
				    				</div>
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
};

/*
//check if user has pressed the login button
if (isset($_POST['submit'])) {
	require "../connect.php";

	//grab the variables
	$user = trim(stripslashes(htmlspecialchars($_POST['user'])));
	$pass = sha1($_POST['password']);

	//attempt to grab the user
	$row = DB::queryFirstRow('select * from users where username = %s',$user);

	//assume user is OK
	$GLOBALS["error"] = 0;

	//check if a user exists
	if (!$row){
		//user name error
		$GLOBALS["error"] = 1;
		//clobber
		session_destroy();

	} else {
		//live user - let's check passwords
		if($pass == $row['password']){
			//a user exists, make sure the website recognises them
			$_SESSION["user"] = $user;

			//go back to the homepage
			echo '<script>
			        setTimeout(function(){window.location.href = "../homepage";},1000); 
			      </script>';

		} else {
			//second circle of hell
			$GLOBALS["error"] = 1;
			//clobber
			session_destroy();

		}
	}
}*/


?>
</body>
</html>