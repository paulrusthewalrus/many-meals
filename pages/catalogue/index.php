<?php
//connect up with other pages
session_start();

//get that database
require '../connect.php';

if (isset($_SESSION['user'])) {
	//find the current user
	$current_user_info = DB::query('SELECT * from users WHERE profilepicture = %s',$_SESSION['user']);
	$uid = strval($current_user_info[0]['uid']);
	$redirect_url = '../myrecipes/index.php?uid='.$uid;
}

//delete everything in temp
$files = glob('../../photo_storage/temp_recipes/'); // get all file names
foreach($files as $file){ // iterate files
	if(is_file($file)) {
    	unlink($file); // delete file
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Categories</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_new2.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script_mon.js"></script>
</head>
<body onload="init();">
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
		 				  	<a href="'.$redirect_url.'"><span><i class="fa fa-book"></i></span>&nbspMy Recipes</a>
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
			
			<div class="row">
				<div class="col-xs-8 col-xs-offset-2">
					<div class="well">
						<center><h3>Recipe Catalogue</h3></center>
						<!--<hr>-->
					</div>
				</div>
			</div>

			<!-- All the category content-->
			<div class="row">
				<!-- Well containing all the tags-->
				<div class="col-xs-8 col-xs-offset-2">
					<div class="well">
						<div class="row">
							<div class="col-xs-7 col-xs-offset-1 cat2">
								<center><h4>Discover your new favourite recipe!</h4></center>
							</div>
							<div class="col-xs-4 form-group">
								<p></p>
								<table>
									<tr>
										<td><h5>Order by:&nbsp</h5></td>
										<td>
										<select id="selectionOrder" class="form-control" onchange="order(this.value);">
											<option id="recent" value="recent">Most Recent</option>
											<option id="popular" value="popular">Most Popular</option>
											<option id="rating" value="rating">Rating</option>
										</select>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<!--<div class="row">
							<hr width="600px">
						</div>-->
						<br>
						<!-- All the tags -->
						<div class="row" id="output">
							<?php
							
							/*//grab all the tagname info about tags
							$taginfo = DB::query('SELECT tagname FROM tags');
							//find how many tags there
							$tags = count($taginfo);*/

							/*
							//get recipes from database from the user
							$recipeinfo = DB::query('SELECT * FROM recipes ORDER BY creatingdate DESC');
							$length = count($recipeinfo);
							//echo $length;

							//getting stuff from the recipes found
							$recipecount = 0;
							foreach ($recipeinfo as $recipe) {
								$recipecount += 1;
								$name = $recipe["name"];
								$description = $recipe["description"];
								$duration = $recipe["cookingduration"];
								$servings = $recipe["servings"];
								$image = $recipe["picture"];
								$difficulty = $recipe["difficulty"];
								$rating = $recipe['rating'];
								$author = $recipe["author"];
								$rid = $recipe["rid"];
								$url = '../recipeView/?recipe='.$rid;
								$tags = DB::query("SELECT * FROM tags WHERE rid = %s", $rid);
								$number = DB::count();
								$plate = '';         
								for ($i = 0; $i < $number; $i++) {
									if ($i+1 == $number) {
										$plate = "<a href='#''>".$plate.$tags[$i]['tagname']."</a>";
									} else {
										$plate = $plate."<a href='#'>".$tags[$i]['tagname']."</a>".", ";
									}
								}
								
								//PLATE UP RECIPE
								echo '<a href="'.$url.'">';

								if ((($recipecount+1) % 4 == 0) && $recipecount !== $length) {
									echo '<div class="row">';
								} else {
											echo '<div class="col-xs-4 recipe-profile">
													<div class="well well-sm" style="background-color: white">';
														echo "<center><h4>$name</h4></center>";
														echo "<center><img style='width:80%'; src='".$image."'/></center><br>";
														echo '<center>(<img src="../../resources/ratings/'.$rating.'star.png" style="width: 50%;">)</p></center>';
														echo "<p>$description</p>";
														echo "<p><strong>Tags: </strong>$plate";
									echo '
													</div>
											</div>
										</a>';
										
									}
								}*/
							
							/*
							//start plating the tags up
							for ($i = 0; $i < $tags; $i++) {
								//information about the current tag
								$tagname = $taginfo[$i]['tagname'];
								//check if it's the first in the row
								if (($i+1)%3 == 1) {
									printf('<a href="#"><div class="col-xs-2 col-xs-offset-2 cat">
										<h4>%s</h4></div></a>',$tagname);
								} else {
									printf('<a href="#"><div class="col-xs-2 col-xs-offset-1 cat">
										<h4>%s</h4></div></a>',$tagname);
								}

								//check if we're at the end of the row
								if ((($i+1) % 3 == 0) && $i !== $tags-1) {
									echo '</div><br><div class="row">';
								};
							};*/

						?>
						<!--
						<div class="row">
							<div class="col-xs-2 col-xs-offset-2 cat">
								<h4>Tomatoes</h4>
							</div>
							<div class="col-xs-2 col-xs-offset-1 cat">
								<h4>Pumpkin</h4>
							</div>
							<div class="col-xs-2 col-xs-offset-1 cat">
								<h4>Nut-free</h4>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-xs-2 col-xs-offset-2 cat">
								<h4>Tomatoes</h4>
							</div>
							<div class="col-xs-2 col-xs-offset-1 cat">
								<h4>Pumpkin</h4>
							</div>
							<div class="col-xs-2 col-xs-offset-1 cat">
								<h4>Nut-free</h4>
							</div>
						</div>-->
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

//insert your code below...






?>
</body>
</html>