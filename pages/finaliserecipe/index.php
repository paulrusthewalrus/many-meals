<?php
//connect up with other pages
session_start();

//get that DB
require '../connect.php';

if (!isset($_SESSION['create_recipe_2'])) {
	//send away
	//header('Location: ../createrecipe');
}

//setup all the variables
$name = $_SESSION['recipename'];
$description = $_SESSION['recipedesc'];
$pic = $_SESSION['recipepic'];
$rating = $_SESSION['rating'];
$taglist = $_SESSION['tagnames'];
$steps = $_SESSION['steps'];
$ing_array = $_SESSION['ing_array'];
if (isset($_SESSION['equipment_array'])) {
	$equip_array = $_SESSION['equipment_array'];
}
$minutes = $_SESSION['minutes'];
$hours = $_SESSION['hours'];
$step_array = $_SESSION['step_array'];

$total_duration = $hours*60+$minutes;

//determine author
$userinfo = DB::query("SELECT * FROM users WHERE profilepicture = %s",$_SESSION['user']);
$fname = $userinfo[0]["firstname"];
$surname = $userinfo[0]["lastname"];
$author = $fname." ".$surname;
$authorRecipePage = "../myrecipes/?uid=".$userinfo[0]['uid'];

if (isset($_SESSION['user'])) {
	//find the current user
	$current_user_info = DB::query('SELECT * from users WHERE profilepicture = %s',$_SESSION['user']);
	$uid = strval($current_user_info[0]['uid']);
	$redirect_url = '../myrecipes/index.php?uid='.$uid;
};

//if the user has finally submitted their recipe for creation
if (isset($_POST['submit'])) {

	$con = mysqli_connect("localhost","root","","manymeals");

	//grab the next possible recipe ID
	$recipesinfo = DB::query("SELECT * FROM recipes");
	//$rid  = count($recipesinfo)+200; //UGLY FIX BUT IT'S THE ONLY WAY. Handles up to 200 deleted recipes.
	$ridinfo = DB::query('SELECT max(`auto_increment`)
					  FROM INFORMATION_SCHEMA.TABLES
					  WHERE table_name = "recipes"');

	//print_r($rid);
	$rid = ($ridinfo[0]['max(`auto_increment`)']);

	
	$ext = substr($pic,strlen($pic)-4,4);

	//url to move to
	$new_url = '../../photo_storage/recipes/'.$rid.$ext;

	//move the recipe image
	copy($pic,$new_url);

	//delete everything in temp
	$files = glob('../../photo_storage/temp_recipes/'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file)) {
	    	unlink($file); // delete file
		}
	}

	//insert information into the database
	DB::insert('recipes',array(
		'rid' => $rid,
		'name' => $name,
		'description' => $description,
		'servings' => 2,
		'rating' => 0,
		'difficulty' => 3,
		'cookingduration' => $total_duration,
		'creatingdate' => new DateTime('now'),
		'author' => intval($uid),
		'picture' => $new_url
	));

	//insert all the steps
	for ($i = 0; $i < count($step_array); $i++) {
		DB::insert('steps',array(
			'rid' => $rid,
			'stepnum' => $i+1,
			'stepdescription' => $step_array[$i]
		));
	}

	//insert all the ingredients
	for ($i = 0; $i < count($ing_array); $i++) {
		DB::insert('ingredients',array(
			'rid' => $rid,
			'ingredientname' => $ing_array[$i],
			'units' => 'none',
			'quantity' => 0.00
		));
	}

	//insert any and all equipment
	if (isset($_SESSION['equipment_array'])) {
		if (count($equip_array) > 0) {
			for ($i = 0; $i < count($equip_array); $i++) {
				DB::insert('equipment',array(
					'rid' => $rid,
					'equipmentname' => $equip_array[$i],
				));
			}
		}
	}

	//insert any and all tags
	if (count($taglist) > 0) {
		for ($i = 0; $i < count($taglist); $i++) {
			DB::insert('tags',array(
				'rid' => $rid,
				'tagname' => $taglist[$i],
			));
		}
	}

	mysqli_close($con);

	header('Location: ../myrecipes/index.php?uid='.$uid.'');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Finalisation</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_new.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script_new.js"></script>
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
		    	<li><a id="categories" href="../catalogue">Catalogue</a></li>
		 	</ul>
		</nav>
		
		<!-- Content div required to fix some CSS styling issues with links -->
		<div class="content container">
			<!-- insert html content below this line -->
			<!-- background repeat doesn't work so try to keep your content to the page you see -->
			<div class="row">
				<!-- left side column (profile, ingredients, equipment) -->
				<div class="col-xs-4">
					<!-- Recipe profile -->
					<div class="col-xs-12 recipe-profile">
						<div class="well well-sm">
							<center>
							<?php
							if (isset($_SESSION['taglist'])) {

								$number = count($taglist);

								$plate = '';         
								for ($i = 0; $i < $number; $i++) {
									//find the name of the tag
									//$taginfo = DB::query('SELECT * FROM tags WHERE ')

									if ($i+1 == $number) {
										$plate = "<span>".$plate.$taglist[$i]."</span>";
									} else {
										$plate = $plate."<span>".$taglist[$i]."</span>".", ";
									}
								}
							}

							//print_r($pic);
							echo '<h3>'.$name.'</h3><hr>';
							//echo "<img style='width: 80%' src='data:image/png;base64,".base64_encode($image)."'/><br><br>";
							echo "<p><img style='width: 80%;' src='".$pic."'><br><br>";
							echo '<p>'.$description.'</p>';
							echo '(<img src="../../resources/ratings/0star.png" style="width: 30%;">)</p>';
							echo '<p><strong>Cooking Duration: </strong>'.$total_duration.'min </p>';
							echo '<p><strong>Tags: </strong>'.$plate.'</p>';
							echo '<p><strong>Author: </strong>'.$author.'</p>';

							?>
							</center>
						</div>
					</div>

					<!-- Ingredients (HTML/CSS) -->
					<div class="col-xs-12">
						<div class="well well-lg" style="text-align: center">	
							<h4>Ingredients:</h4>
							<hr>
							<?php
							
								//info about ingredients
								//$ingredientinfo = DB::query("SELECT * from ingredients where rid='".$rid."'");
								//find the number of ingredients
								//$ingredientcount = DB::count();
								//$ingcount = 0;

								//print_r($ing_array);

								$ingredientcount = count($ing_array);

								echo '<div class="row" style="word-wrap: break-word;">';

								for ($i = 0; $i < $ingredientcount; $i++) {

									$ingname = $ing_array[$i];

									echo '<div class="col-xs-6">';
									echo '<p class="rcitem">'.$ingname.'</p>';
									echo '</div>';

									if ($i==1) {
										echo '</div>';
										if ($i != $ingredientcount-1) {
											echo '<div class="row" style="word-wrap: break-word;">';
										}
									}

								}
								echo '</div>';
							?>
						</div>
					</div>

					<!-- Equipment v2 (HTML/CSS) -->
					<?php
						//info about ingredients
						//$equipinfo = DB::query("SELECT * from equipment where rid='".$rid."'");
						
						if (isset($_SESSION['equipment_array'])) {
							//$equip_array

							//find the number of equipment
							$equipcount = DB::count();
							$eqcount = 0;
							if ($equipcount > 0) {
							echo '<div class="col-xs-12">';
								echo '<div class="well well-lg" style="text-align: center">';
									echo'<h4>Equipment:</h4>';
									echo'<hr>';
										echo '<div class="row" style="word-wrap: break-word;">';

										for ($i = 0; $i < $equipcount; $i++) {

											$eqname = $equip_array[$i];

											echo '<div class="col-xs-6">';
											echo '<p class="rcitem">'.$eqname.'</p>';
											echo '</div>';

											if ($i==1) {
												echo '</div>';
												if ($i != $eqcount-1) {
													echo '<div class="row" style="word-wrap: break-word;">';
												}
											}

										}
										echo '</div>';
									
								echo '</div>';
							echo '</div>';
							}
						}
					?>
				</div>

				<form method="post" action="index.php" name="main">
					<!-- right side column (steps, comments) -->
					<div class="col-xs-8">
						<!-- Recipe Steps -->
						<div class="col-xs-20">
								<div class="well row">
									<h3 style="text-align: center">Get cooking now:</h3>
									<hr>
									<!-- Steps -->
									<div class="col-xs-11">
									<?php
										//find all the steps and plate them up
										//$stepinfo = DB::query("SELECT * FROM steps WHERE rid = %s ORDER BY stepnum",$recipe["rid"]);
										//$stepcount = DB::count();


										$stepcount = $steps;

										//begin the step loop
										for ($i = 0; $i < $stepcount; $i++) {
											//print out the checkbox
											echo '<table><tr><td><input type="checkbox" onchange="update(this.id);"id="'.(($i+1)*10).'" class="check"></td>';

											//print out spacing
											echo '<td>&nbsp</td><td>&nbsp</td>';

											//print out the step
											echo '<td><span class="'.(($i+1)*10).'"><strong style="font-size: 18px;">'.($i+1).'.</strong> '.$step_array[$i].'</span></td></tr></table><br>';

											//if we're at the end, finish the table
											/*if ($i+1 == $stepcount) {
												echo '</table>';
											}*/
										}
									?>
									</div>
								</div>
							<div class="row">
								<br>
								<div class="col-xs-4 col-xs-offset-4 well form-group">
									<center>
										<input type="submit" name="reset" value="Go Back" class="form-control" style="width: 60%;"><br>
										<input type="submit" name="submit" value="Finish Creation" style="width: 60%"class="form-control">
									</center>
								</div>
							</div>
						</div>
					</div>
				</form>
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