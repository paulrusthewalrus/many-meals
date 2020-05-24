<?php
//connect up with other pages
session_start();

if (isset($_GET['recipecreation'])) {
	$before = '../createrecipe';
} else if (isset($_GET['searchtag'])) {
	$before = '../search';
} else {
	$before = '../createrecipe';
}

//check if a tag was just created
if (isset($_POST["tagcreate"])) {
	header("Location: ".$before);  //../createrecipe    OR   ../search
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Tag Creation</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_new.css">
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
		 					 	<a href="../admin.php"><span><i class="fa fa-connectdevelop"></i></span>&nbspDeveloper Login</a>
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
				<div class="col-xs-8 col-xs-offset-2">
					<div class="well">
						<center><h3>Create a Tag</h3></center>
						<hr>
					</div>
				</div>
			</div>

			<form name="main" method="post" action="index.php">
				<div class="row">
					<div class="col-xs-8 col-xs-offset-2">
						<div class="well">
							<div class="row">
								<!--<div class="well" style="background-color: white">-->
								<div class="col-xs-2 col-xs-offset-2">
									<h4>Tag Name:</h4>
								</div>

								<div class="col-xs-4">
									<div class="form-group">
										<input class="form-control" type="text" name="tag" required>
									</div>
								<!--</div>-->
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-xs-4 col-xs-offset-4">						
									<div class="form-group">
										<?php
										if (!isset($_POST['again'])) {
											echo '<input type="submit" name="tagcreate" value="Submit Tag" class="form-control">';
										} else {
											echo '<input type="submit" name="tagcreate" value="Finish Creating Tags" class="form-control">';
										}
										?>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-xs-6 col-xs-offset-3">
									<div class="form-group">
										<input type="submit" name="again" value="Submit Tag and Create Another" class="form-control">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
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

require "../connect.php";

//insert your code below...
if (isset($_POST["tagcreate"])) {

	//grab the tag
	$tagname = stripslashes(trim(htmlspecialchars($_POST["tag"])));
	//check if a tag with that already exists in the database
	$info = DB::query("SELECT * FROM tags WHERE tagname = %s",$tagname);
	
	if (count($info) == 0 && count($_SESSION['tagarray']) == 0) {
		//add it to the session for a record
		$_SESSION['tagarray'] = array($tagname);

	} else if (count($info) == 0 && count($_SESSION['tagarray']) > 0) {
		//push it
		array_push($_SESSION['tagarray'],$tagname);
	}
}

if (isset($_POST["again"])) {
	//grab the tag
	$tagname = stripslashes(trim(htmlspecialchars($_POST["tag"])));
	$info = DB::query("SELECT * FROM tags WHERE tagname = %s",$tagname);

	
	if (count($info) == 0) {
		if (!isset($_SESSION["tagarray"])) {
			//create an array
			$_SESSION['tagarray'] = array($tagname);
		} else {
			//add it to the session for a record
			array_push($_SESSION['tagarray'],$tagname);
			//print_r($_SESSION['tagarray']);
		}
	}
}





?>
</body>
</html>