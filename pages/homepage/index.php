<?php
//connect up with other pages
session_start();

//database
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
	<title>Many Meals | Homepage</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_sat.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<!--Styling-->
<style> 
	
	/*Font colour*/
	.ff {
		color: #000000;
		border-color: white;
		text-shadow: none;
	}

	/*Custom well container design for title*/
	.textboxstyle1{
		width: 100%;
		border-radius: 10px;
		background-color: #f2f2f2;
		padding: 1%;
	}

	/*Custom well container design for content*/
	.textboxstyle2{
		width: 100%;
		border-radius: 10px;
		background-color: #f2f2f2;
		padding: 2%;
	}

	/*Custom space design*/
	.space {
		padding: 10px;
	}

	/*Repositioning indicators*/
	.carousel-indicators {
		left: 530px;
	}

	/*Repositioning content container for text info*/
	.reposition {
		left: 567px;
		top: 0;
		width: 37%;
		height: 360px;
		border-radius: 10px;
		background-color: #e6e6e6;
	}

</style>

<body>
	<!-- Every piece of content is stored ON the background image-->
	<div class="view first_back " id="div-back" style="background-image: url('../../resources/background_updated.png'); background-repeat: repeat-y; background-size: cover; background-position: center center;">
		 <!-- navigation bar (if you need to mess with the nav talk to me first) -->
		<nav class="navbar navbar-default">
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
		 		<h3 class="ff" id="title"><a id="homepagetitle" href="../homepage">Many Meals</a></h3>
		 	</div>
		 	<!--The main links to go to-->
		 	<ul class="nav navbar-nav">
		    	<li><a id="search" class="active" href="../search">Search</a></li>
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
		<div class="container">
			<!-- insert html content below this line -->
			<!-- background repeat doesn't work so try to keep your content to the page you see -->
			<div class="row">
				<!-- range/size of custom well -->
				<div class="col-xs-10 col-xs-offset-1">
					<!-- center title and content wells -->
					<div class="text-center">
						<!-- title well -->
						<div class="textboxstyle1">
							<!-- title context -->
							<h3 class="ff">Welcome to Many Meals! Your #1 Recipe and Cooking Hub</h3>
						</div>
						<div class="space"></div>

						<!-- Content well containing carousel -->
						<div id="myCarousel" class="carousel slide textboxstyle2" data-ride="carousel" data-interval="13000">

							<!-- Indicators -->
						    <ol class="carousel-indicators">
						    	<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
								<li data-target="#myCarousel" data-slide-to="1"></li>
								<li data-target="#myCarousel" data-slide-to="2"></li>
							</ol>

							<!-- Wrapper for slides -->
							<div class="carousel-inner">



								<?php

								//setup the connection with the database
								require"../connect.php";

								//grab all information from recipes
								$info = DB::query("SELECT * FROM recipes");
								//find the number of recipes
								$num = count($info);
								    	
								//locate file location			
								$dir = "../../photo_storage/recipes/";
								//get an array of all file names
								$files = array_diff(scandir($dir,1),array("..","."));
								//find length of array
								$num2 = count($files);
								//current array of picked recipes
								$picked = array();
								$counter = 1;

								//pick 3 recipes
								while ($counter < 4) {
								   	//failesafe
								   	$failsafe = 0;

								   	//pick a random recipe
								   	if (isset($_GET["recipe"])) {
										//take them to that recipe
										$recipeindex = $_GET["recipe"]-1;
									} else {
										//otherwise randomise it
										$recipeindex = rand(0,$num-1);
												
									}	

									//get all the information on that recipe
									$recipe = $info[$recipeindex];						

									//get specific information about recipe
									$rid = $recipe["rid"];
									$name = $recipe["name"];
									$description = $recipe["description"];	
									$image = $recipe["picture"];	
									$rating = $recipe['rating'];
									$duration = $recipe['cookingduration'];
									$servings = $recipe['servings'];

									//determine author
									$userinfo = DB::query("SELECT * FROM users WHERE uid = %s",$recipe["author"]);
									$fname = $userinfo[0]["firstname"];
									$surname = $userinfo[0]["lastname"];
									$author = '<a href="../myrecipes/index.php">'.$fname.' '.$surname.'</a>';

									//find recipe tags
									$taginfo = DB::query("SELECT * FROM tags WHERE rid = %s",$rid);
									$number = DB::count();
									
									$plate = '';         
									for ($j = 0; $j < $number; $j++) {
										if ($j+1 == $number) {
											$plate = $plate."<a class='specialtag' href='#'>".$taginfo[$j]['tagname']."</a>";
										} else {
											$plate = $plate."<a class='specialtag' href='#'>".$taginfo[$j]['tagname']."</a>".", ";
											//echo $j;
										}
									}		

									//check if it's already in the picked array
									for ($i = 0; $i < count($picked); $i++) {
									  	if ($files[$recipeindex] == $picked[$i]) {
											$failsafe = 1;
									    			
										}
									}

									//if not picked, add it
									if ($failsafe == 0) {
										array_push($picked,$files[$recipeindex]);
										$counter++;

									//create directory for specific image
									$img = $dir.$files[$recipeindex];

									//get information about the directory
									$infos = pathinfo($img);
									//find the extension name (i.e. the RID)
									$ridnum = $infos["filename"];	

									//plate up the html
									if ($counter == 2) {
										echo "<div class='item active'>";
									} else {
										echo "<div class='item'>";
									}
										echo "<a href='../recipeView/?recipe=".$rid."'><img src='".$image."' class='imgshowcase'></a>";

										echo "<div class='carousel-caption reposition ff'>
												<div class='row'>
													
													<div class='col-xs-10 col-xs-offset-1'>
													<h3>".$name."</h3>
													<hr width='250px'>
										    		<p>".$description."</p>
										    		<p>(<img src='../../resources/ratings/".$rating."star.png' style='width: 30%;'>)</p>
										    		<p><strong>Tags: </strong>".$plate."</p>
										    		<p><strong>Author: </strong>".$author."</p>
										    		
										    		<!-- Left and right controls -->
										    		&nbsp&nbsp
												  <a class='left carousel-control' href='#myCarousel' role='button' data-slide='prev'>
												    <span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
												    <span class='sr-only'>Previous</span>
												  </a>
												  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
												  <a class='right carousel-control' href='#myCarousel' role='button' data-slide='next'>
												    <span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
												    <span class='sr-only'>Next</span>
												  </a>


										    		</div>
									    		</div>
									    	</div>";

									    echo "</div>";
									    		
									}
								}

								?>	
						 	</div>
						</div>		
					</div>
				</div>
			</div>
		</div>

		<!-- Going past this line risks messing up some CSS styling-->
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