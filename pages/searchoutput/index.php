<?php
//connect up with other pages
session_start();

require '../connect.php';

if (isset($_SESSION['user'])) {
	//find the current user
	$current_user_info = DB::query('SELECT * from users WHERE profilepicture = %s',$_SESSION['user']);
	$uid = strval($current_user_info[0]['uid']);
	$redirect_url = '../myrecipes/index.php?uid='.$uid;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Search Results</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script.js"></script>
</head>
<body>
	<?php
	require('../connect.php');

	//find out what the search is
	//it can be ingredients, recipe names, tags
	

	if (isset($_GET['searchi'])) {
		//search is an ingredient
		$iname = $_GET['searchi'];
		$header = $iname;
		//get stuff from database
		//$recipes = DB::query("SELECT rid FROM ingredients WHERE ingredientname=%s", $iname);
		$table = 'ingredients';
		$column = 'ingredientname';
		$value = $iname;

	}elseif (isset($_GET['searchr'])) {
		//it's a recipe name 
		$rname = $_GET['searchr'];
		$header = $rname;
		//$recipes = DB::query("SELECT rid FROM recipes WHERE recipename=%s", $rname);
		$table = 'recipes';
		$column = 'name';
		$value = $rname;

	}elseif (isset($_GET['searcht'])){
		//it's a tag
		$tname = $_GET['searcht'];
		$header = $tname;
		//$recipes = DB::query("SELECT rid FROM recipies WHERE recipename=%s", $tname);
		$table = 'tags';
		$column = 'tagname';
		$value = $tname;

	};

	?>
	<!-- Every piece of content is stored ON the background image-->
	<div class="view first_back" style="background-image: url('../../resources/background_updated.png'); background-repeat: repeat-y;">
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
			<!--banner for the page-->
			<div class="well">
				<h3 style="text-align: center">
					<?php 
						if (isset($header)){
							echo($header);
						}else{
							echo('Whoops');
						}
						;
					?> Recipes</h3>
			</div>
				<!-- THIS IS WHERE THE RECIPIES ARE DISPLAYED -->
				<!-- get profile of recipe and display it -->
				<?php
				//get recipes from database from the user
                $myquery = "SELECT * FROM ". $table ." WHERE ".$column." = '".$value."'"; 
				$recipeinfo = DB::query($myquery);
				$length = count($recipeinfo);
				//check if there are any recipes
				if (!$recipeinfo){
					//no recipes found
					echo "<center><h4>No recipes found</h4></center>";
				}else{
					//getting stuff from the recipes found
					$recipecount = 0;
					foreach ($recipeinfo as $recipe) {
						$recipecount+=1;
						$name = $recipe["name"];
						$description = $recipe["description"];
						$duration = $recipe["cookingduration"];
						$servings = $recipe["servings"];
						$image = $recipe["picture"];
						$difficulty = $recipe["difficulty"];
						$author = $recipe["author"];
						$rid = $recipe["rid"];
						$url = '../recipeView/?recipe='.$rid;
						$tags = DB::query("SELECT * FROM tags WHERE rid = %s", $rid);
						$number = DB::count();
						$plate = '';         
						for ($i = 0; $i < $number; $i++) {
							if ($i+1 == $number) {
								$plate = "<a href='#'>".$plate.$tags[$i]['tagname']."</a>";
							} else {
								$plate = $plate."<a href='#'>".$tags[$i]['tagname']."</a>".", ";
							}
						}
						
						//PLATE UP RECIPE
						echo '<a href="'.$url.'">';
						if ((($recipecount+1) % 4 == 0) && $recipecount !== $length-1) {
							echo '<div class="row">';
						}else{
									echo '<div class="col-xs-3 recipe-profile">
											<div class="well well-sm" style="background-color: white">';
												echo "<center><h3>$name</h3></center>";
												echo "<center><img style='width:80%'; src='".$image."'/></center><br>";
												echo "<p>$description</p>";
												echo "<p><strong>Tags: </strong>$plate";
							echo '
											</div>
									</div>
								</a>';
								
							}
						}
					}
					?>			


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