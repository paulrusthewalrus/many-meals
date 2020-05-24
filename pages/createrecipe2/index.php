<?php
	//connect up with other pages
	session_start();

	//database
	require '../connect.php';

	unset($_SESSION['create_recipe_2']);

	function resetFunc() {
		unset($_SESSION['step_array']);
	};


	$error = 0;

	if (!isset($_SESSION['createrecipe_submit'])) {
		header('Location: ../createrecipe');
	};

	if (isset($_SESSION['user'])) {
			//find the current user
			$current_user_info = DB::query('SELECT * from users WHERE profilepicture = %s',$_SESSION['user']);
			$uid = strval($current_user_info[0]['uid']);
			$redirect_url = '../myrecipes/index.php?uid='.$uid;
		}

	if (isset($_POST['addEquipment'])) {
		//grab the variable to add
		$item_name = trim(stripslashes(htmlspecialchars($_POST['equipmentname'])));

		$check = 0;

		if (isset($_SESSION['equipment_array'])) {
			for ($i = 0; $i < count($_SESSION['equipment_array']); $i++) {
				if ($item_name == $_SESSION['equipment_array'][$i]) {
					$check = 1;
				}
			}
		}

		if ($check == 0) {
			//check if the equipment session has already been set
			if (isset($_SESSION['equipment_array'])) {
				array_push($_SESSION['equipment_array'],$item_name);
			} else {
				$_SESSION['equipment_array'] = [];
				array_push($_SESSION['equipment_array'],$item_name);
			}
		}
	}

	if (isset($_POST['addIngredient'])) {
		//grab the variable to add
		$item_name = trim(stripslashes(htmlspecialchars($_POST['ingredientname'])));

		$check = 0;

		if (isset($_SESSION['ing_array'])) {
			for ($i = 0; $i < count($_SESSION['ing_array']); $i++) {
				if ($item_name == $_SESSION['ing_array'][$i]) {
					$check = 1;
				}
			}
		}

		if ($check == 0) {
			//check if the equipment session has already been set
			if (isset($_SESSION['ing_array'])) {
				array_push($_SESSION['ing_array'],$item_name);
			} else {
				$_SESSION['ing_array'] = [];
				array_push($_SESSION['ing_array'],$item_name);
			}
		}
	}

	if (isset($_POST['submit_step'])) {
		$_SESSION['steps'] = $_POST['newstep'];
		header("Refresh:0");
	}

	//insert your code below...
	if (isset($_POST['submit'])) {
		//check if all fields have been filled out
		$test = 1;

		if (isset($_SESSION['ing_array'])) {
			if (count($_SESSION['ing_array']) == 0) {
				$test = 0;
				header("Refresh:0; url=index.php?error=1");
				$error = 1;
				resetFunc();
			}
		} else {
			$test = 0;
			header("Refresh:0; url=index.php?error=1");
			$error = 1;
			resetFunc();
		}

		$steps = $_SESSION['steps'];
		for ($i = 0; $i < $steps; $i++) {
			//find the value
			if (!isset($_POST['stepcontent'.strval($i+1)])) {
				$test = 0;
				header("Refresh:0; url=index.php?error=2");
				$error = 2;
				resetFunc();
			}

			$value = $_POST['stepcontent'.strval($i+1)];

			if ($value == '') {
				$test = 0;
				header("Refresh:0; url=index.php?error=2");
				$error = 2;
				resetFunc();
			}

			//find the value
			$value = trim(stripslashes(htmlspecialchars($_POST['stepcontent'.strval($i+1)])));

			//check if a step array exists
			if (isset($_SESSION['step_array'])) {
				array_push($_SESSION['step_array'],$value);
			} else {
				//create the array
				$_SESSION['step_array'] = [];
				array_push($_SESSION['step_array'],$value);
			}
		}

		if ($error == 0) {
			$_SESSION['create_recipe_2'] = 1;

			header('Location: ../finaliserecipe');
		}

	}	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Create recipe part 2</title>

	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_new2.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script.js"></script>
</head>

<style>
	.button {
		width: 80%;
		padding: 10px;
	}
</style>

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
		    	<li>
		    		<a id="search" href="../search">Search</a>
		    	</li>
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
			<div class="row">
				<div class="well col-xs-8 col-xs-offset-2">
					<center><h3 style="text-align: center">Step us through your recipe, how do we get started?</h3></center>
				</div>
			</div>

			<div class="row">
				<?php
				if (isset($_GET['error'])) {
					if ($_GET['error'] == '1') {
						printf('<center>
								<div class="col-xs-4 col-xs-offset-4">
								<div class="alert alert-warning">
									<strong>Warning!</strong> Not enough ingredients were added!
								</div>
								</div>
								</center>');
					} else {
						printf('<center>
								<div class="col-xs-4 col-xs-offset-4">
								<div class="alert alert-warning">
									<strong>Warning!</strong> Not all information was filled out!
								</div>
								</div>
								</center>');
					}
				}
				
				?>
			</div>

			<!-- Form for all the input boxes -->
			<!-- ALL BOXES IN HERE -->
			<form name="main" method="post" action="./">

				<!-- Ingredients and Equipment -->
				<div class="row">
					<div class="col-xs-10">
					<div class="row">
						<div class="col-xs-4 col-xs-offset-3 well outlinebox">
								<h3 style="text-align: center">Ingredients</h3>
								<hr width="250px">
								<div class="form-group">
									<center>
		  							<input type="text" autofocus style="width: 51%;"name="ingredientname" class="form-control" placeholder="Name of Ingredient">

		  							<div class="row">
		  								<div class="col-xs-3 col-xs-offset-3">
		  									<h5>Quantity:</h5>
		  									<input type="number" class="form-control" min="0" name="ingredientamount" value="1" required>
		  								</div>
		  								<div class="col-xs-3">
		  									<h5>Units:</h5>
		  									<input type="text" class="form-control" name="measuremethod" value="Kg" required>
		  								</div>
		  							</div>

		  							<br>
		  							<input type="submit" class="form-control " style="width: 30%;" name="addIngredient" value="Add">
		  							<br>
		  							<h5>Current Ingredient List: <?php if (isset($_SESSION['ing_array'])) { echo count($_SESSION['ing_array']);} else {echo '0';}?></h5>
		  									<p>
		  										<?php
		  										//check if some equipment exists
		  										if (isset($_SESSION['ing_array'])) {
		  											$num_equipment = count($_SESSION['ing_array']);
		  											if ($num_equipment > 0) {
		  												//loop through it all
		  												for ($i = 0; $i < $num_equipment; $i++) {
		  													if ($i+1 != $num_equipment) {
		  														echo $_SESSION['ing_array'][$i].', ';
		  													} else {
		  														echo $_SESSION['ing_array'][$i];
		  													}
		  												}
		  											}
		  										}
		  										?>
		  									</p>
		  						</center>
	  							</div>
						</div>
					
						<div class="col-xs-4 col-xs-offset-1 well outlinebox">
								<h3 style="text-align: center">Equipment</h3>
								<hr width="250px">
								<div class="form-group">
									<center>
		  							<input type="text" style="width: 75%;" name="equipmentname" class="form-control" placeholder="Name of Equipment Item">

		  							<br>
		  							<input type="submit" class="form-control" style="width: 30%;" name="addEquipment" value="Add">

		  							<br>
		  							<div class="row">
		  								<div class="col-xs-12">
		  									<h5>Current Equipment List: <?php if (isset($_SESSION['equipment_array'])) { echo count($_SESSION['equipment_array']);} else {echo '0';}?></h5>
		  									<p>
		  										<?php
		  										//check if some equipment exists
		  										if (isset($_SESSION['equipment_array'])) {
		  											$num_equipment = count($_SESSION['equipment_array']);
		  											if ($num_equipment > 0) {
		  												//loop through it all
		  												for ($i = 0; $i < $num_equipment; $i++) {
		  													if ($i+1 != $num_equipment) {
		  														echo $_SESSION['equipment_array'][$i].', ';
		  													} else {
		  														echo $_SESSION['equipment_array'][$i];
		  													}
		  												}
		  											}
		  										}
		  										?>
		  									</p>
		  								</div>
		  							</div>		
		  						</center>
	  							</div>
						</div>
					</div>
					</div>

				<div class="row">
					<div class="col-xs-6 col-xs-offset-3 well">
						<center>
							<h5>Make sure to fill out the ingredients and equipment before you start steps</h5>
						</center>
					</div>
				</div>

				<?php
					$steps = $_SESSION['steps'];

					if (!isset($_GET['error'])) {
						for ($i = 0; $i < $steps; $i++) {
							printf('<div class="row">
									<div class="col-xs-10 well col-xs-offset-1" style="border: 0.5px solid gray">
										<div class="row">
											<div class="well col-xs-2 col-xs-offset-1 outlinebox">
													<h4 style="text-align: center">Step %s</h4>
											</div>
											<div class="col-xs-7 well form-group col-xs-offset-1 outlinebox">
						  							<input type="text" name="stepcontent%s" value="" autofocus class="form-control">
											</div>
										</div>
									</div>
								</div>',$i+1,$i+1);
						}
					} else {
						for ($i = 0; $i < $steps; $i++) {
							printf('<div class="row">
									<div class="col-xs-10 well col-xs-offset-1" style="border: 0.5px solid gray">
										<div class="row">
											<div class="well col-xs-2 col-xs-offset-1 outlinebox">
													<h4 style="text-align: center">Step %s</h4>
											</div>
											<div class="col-xs-7 well form-group col-xs-offset-1 outlinebox">
						  							<input type="text" name="stepcontent%s" value="%s" autofocus class="form-control">
											</div>
										</div>
									</div>
								</div>',$i+1,$i+1,$_SESSION['step_array'][$i]);
						}
					}
				?>
				<!--<div class="row">
					<div class="col-xs-10 well col-xs-offset-1" style="border: 0.5px solid gray">
						<div class="row">
							<div class="well col-xs-2 col-xs-offset-1 outlinebox">
									<h4 style="text-align: center">Step 5</h4>
							</div>
							<div class="col-xs-7 well form-group col-xs-offset-1 outlinebox">
		  							<input type="text" name="step1content" value="" autofocus class="form-control">
							</div>
						</div>
					</div>
				</div>-->

				<!-- Submit and Steps -->
				<div class="row">
					<div class="col-xs-2 well form-group outlinebox col-xs-offset-4">
						<center>
							<h5>Change the number of steps:</h5>
							<input type="number" name="newstep" style="width: 34%;" value="<?php echo $steps; ?>" class="form-control">
							&nbsp
							<input type="submit" name="submit_step" value="Adjust Steps" class="form-control">
						</center>
					</div>
					<div class="col-xs-2 well form-group outlinebox col-xs-offset-1">
						<br><input type="submit" name="submit" style="height: 80px; background-color: silver; color: black; border: 1px solid black"value="Finalise Creation" class="form-control"><br><br>
						
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

	/*//is data is posted
	$step1 = $_POST['step1content'];
	$step2 = $_POST['step2content'];
	$step3 = $_POST['step3content'];
	$step4 = $_POST['step4content'];
	$step5 = $_POST['step5content'];

	//put data into session variables for finalisation
	$_SESSION["step1"] = $step1;
	$_SESSION["step2"] = $step2;
	$_SESSION["step3"] = $step3;
	$_SESSION["step4"] = $step4;
	$_SESSION["step5"] = $step5;*/

?>
</body>
</html>