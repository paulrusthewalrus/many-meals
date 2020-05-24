<?php
	//connect up with other pages
	session_start();

	//get that DB
	require '../connect.php';

	unset($_SESSION['equipment_array']);
	unset($_SESSION['ing_array']);
	unset($_SESSION['step_array']);
	unset($_SESSION['create_recipe_2']);

	//check to see if the user is NOT logged in
	if (!isset($_SESSION['user'])) {
		//a user is NOT logged in, redirect them away from this page
		header('Location: ../homepage');
	};

	//delete everything in temp
	$files = glob('../../photo_storage/temp_recipes/'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file)) {
	    	unlink($file); // delete file
		}
	}

	$error_notfilled = 0;

	//insert your code below...
	if (isset($_POST['submit'])) {
		//print_r($_POST);


		if (isset($_POST['recipename']) && isset($_POST['recipedesc']) && $_POST['taglist']) {
			$tag_list = json_decode($_POST['taglist'], true);
			if (count($tag_list) > 0 && intval($_POST['hours'])+intval($_POST['minutes']) > 0 && intval($_POST['steps'])>0) {
					//grab the variables
					$recipename = stripslashes(trim(htmlspecialchars($_POST['recipename'])));
					$recipedesc = stripslashes(trim(htmlspecialchars($_POST['recipedesc'])));

					$pic = $_FILES['fileToUpload'];

					//these variables are set via JS. 
					$rating = $_POST['rating'];
					$tag_list = json_decode($_POST['taglist'], true);
					$tag_names = json_decode($_POST['tagnames'],true);

					//upload them into the session (FOR OTHER PAGE ACCESS)
					$_SESSION["recipename"] = $recipename;
					$_SESSION["recipedesc"] = $recipedesc;
					//$_SESSION["recipepic"] = $pic;
					$_SESSION["rating"] = $rating;
					$_SESSION["taglist"] = $tag_names;
					$_SESSION['tagnames'] = $tag_names;
					$_SESSION['steps'] = intval($_POST['steps']);
					$_SESSION['minutes'] = intval($_POST['minutes']);
					$_SESSION['hours'] = intval($_POST['hours']);
					$_SESSION['createrecipe_submit'] = 1;

					$pic = $_FILES['fileToUpload'];

					//grab the next possible recipe ID
					$recipesinfo = DB::query("SELECT * FROM recipes");
					$rid  = count($recipesinfo)+1;

					//setup
					$target_dir = '../../photo_storage/temp_recipes/'.$rid;
					$uploadOk = 1;  //assume we good to go (collect evidence to contrary)


					//go get filename and file extension
					$target_file = $target_dir . basename($pic['name']);  //understood
					
				    //grabs the extension
				    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

				    //echo $target_file;

					// Check if image file is a actual image or fake image
				    $check = getimagesize($pic['tmp_name']);

				    if($check !== false) {
				        //echo 'File is an image - ' . $check['mime'] . '.';
				        $uploadOk = 1;
				    } else {
				        echo 'File is not an image.';
				        $uploadOk = 0;
				    }
				    

					// Check if file already exists
					if (file_exists($target_file)) {
				    	echo 'Sorry, file already exists.';
				    	$uploadOk = 0;
					}

					// Check file size
					if ($pic['size'] > 1000000) {
				    	echo '<p style="background-color: white; font-size: 20px">Sorry, your file is too large.</p>';
				    	$uploadOk = 0;
					}

					// Allow certain file formats (UNDERSTOOD)
					if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif' ) {
				    	echo 'Sorry, only JPG, JPEG, PNG, GIF files are allowed.';
				    	$uploadOk = 0;
					}

					// Check if $uploadOk is set to 0 by an error
					if ($uploadOk == 0) {
				    	//echo '<p>Your file was not uploaded.</p>';

					// if everything is ok, try to upload file
					} else {
						//setup new string variable
						$new_target = '';
						$numbers = array('1','2','3','4','5','6','7','8','9','0');
						$found = true;
						
						//create the new target 
						while ($i < strlen($target_file) && $found == true) {
							//check if the letter is NOT a number
							if (!in_array($target_file[0],$numbers)) {
								//add to the array
								$new_target = $new_target.$target_file[$i];
								//increment looping variable
								$i++;
							} else {
								//add this one to the array, but cancel the loop
								$new_target = $new_target.$target_file[$i];
								$found == false;
							}
						};

						echo $new_target;
						
						$new_target = $target_dir.'.'.$imageFileType;

				        //move the file from it's temporary location to the NEW location
				    	if (move_uploaded_file($pic['tmp_name'], $new_target)) { //understood
				        	echo 'The file '. $profpic['name']. ' has been uploaded.';

				        	//all good, send it off
				        	$_SESSION['recipepic'] = $new_target;

							header('Location: ../createrecipe2');

				            //delete any remnant of the uploaded photo
				            unset($pic);
				    	} else {
				        	echo 'Sorry, there was an error uploading your profile picture.';
				    
				    	}
					};

					
			} else {

				$error_notfilled = 1;
				
			}
		} else {
			$error_notfilled = 1;
		}
	}

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
	<title>Many Meals | Create a recipe</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script_mon.js"></script>
  	<style type="text/css">
  		/* Original star images */
		.stars img {
		  width: 50px;
		}

		/* When the stars are hovered over */
		.stars img:hover {
		  width: 53px;
		  transition: 0.2s;
		}
  	</style>
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
			
			<!-- Title for the create recipe page -->
			<div class="row">
				<div class="col-xs-8 col-xs-offset-2">
					<div class="col-xs-20">
						<div class="well">
							<div class="row">
								<h3 style="text-align: center">What's your recipe about?</h3>
								<!--<hr>-->
							</div>
						</div>
					</div>
				</div>
			</div>


			<!-- All the content -->
			<div class="row">
				<!-- Content column -->
				<div class="col-xs-10 col-xs-offset-1">
					<!-- Start a well to contain all the content -->
					<div class="well">
						<div class="row">
							<div class="col-xs-10 col-xs-offset-2">
								<!-- Recipe name -->
								<div class="row">
									<div class="col-xs-10 col-xs-offset-2">
										<table class="inputtables">
											<tr>
												<td><center><h4>Recipe Name:</h4></center></td>
												<td>
													<div class="input-group">
											      		<input id="recipename" type="text" class="form-control userinput" value="" name="recipename" placeholder="Eggs on toast..." required value="" onchange="btnCheck();">
											      	</div>
										      	</td>
									      	</tr>
								      	</table>
							     	</div>
						      	</div>   

						      	<br>

						      	<!-- Recipe Right Column / Description -->
						      	<div class="row">
						      		<!-- Recipe Description -->
									<div class="col-xs-10 col-xs-offset-1">
										<table class="inputtables">
											<tr>
												<td><h4>Recipe Description:</h4></td>
												<td>
													<div class="input-group">
											      		<input id="recipedesc" size="30px" type="textarea" class="form-control userinput" value="" name="recipedesc" placeholder="A very tasty meal..." required value="" onchange="btnCheck();">
											      	</div>
										      	</td>
									      	</tr>
								      	</table>
							     	</div>
						      	</div>
							     	
							    <br>

						      	<!-- Difficulty and Steps -->
						      	<div class="row">
						      		<div class="col-xs-6">
						      			<table class="inputtables" style="padding-bottom: 10px">
						      				<tr>
						      					<td style="width: 127px"><h4><center>Recipe Difficulty:</center></h4></td>
						      					<td>
						      						<div class="stars">
														<!--<div class="col-xs-2">							
															<img src="../../resources/gray.png" width="50px" id="1" onmouseenter="starEnter(this.id);" onmouseleave="starLeave(this.id);" onclick="starClick(this.id);">
														</div>

														<div class="col-xs-2">							
															<img src="../../resources/gray.png" width="50px" id="2" onmouseenter="starEnter(this.id);" onmouseleave="starLeave(this.id);" onclick="starClick(this.id);">
														</div>

														<div class="col-xs-2">							
															<img src="../../resources/gray.png" width="50px" id="3" onmouseenter="starEnter(this.id);" onmouseleave="starLeave(this.id);" onclick="starClick(this.id);">
														</div>

														<div class="col-xs-2">							
															<img src="../../resources/gray.png" width="50px" id="4" onmouseenter="starEnter(this.id);" onmouseleave="starLeave(this.id);" onclick="starClick(this.id);">
														</div>

														<div class="col-xs-2">							
															<img src="../../resources/gray.png" width="50px" id="5" onmouseenter="starEnter(this.id);" onmouseleave="starLeave(this.id);" onclick="starClick(this.id);">
														</div>-->
														<h5 style="color: green">Feature for version 2</h5>
													</div>
												</td>
											</tr>
										</table>
						      		</div>
						      		<div class="col-xs-5">
						      			<table class="inputtables">
											<tr>
												<td><h4><center>Steps involved:</center></h4></td>
												<td>
													<div class="input-group">
														<input type="number" name="steps" value="0" id="stepinput" min="0" style="width: 48px;" onchange="checkNum(this.id);">
											      	</div>
										      	</td>
									      	</tr>
								      	</table>
						      		</div>
						      	</div>

						      	<!-- Tag and scrollbar -->
								<div class="row">
									<div class="col-xs-8 col-xs-offset-1"> 
										<div class="row">
											<div class="col-xs-12">
								      			<h3 id="instruction"><center>Choose from a selection of tags: &nbsp<a href="../tagcreate/index.php?recipecreation=1" id="addtag"><span class="glyphicon glyphicon-plus-sign addtag"></span></a></center></h3>
								      		</div>
								      	</div>

										<div class="card example-1 scrollbar-ripe-malinka">
								      		<div class="card-body">
								      			<table class="table-bordered table tags" cellspacing="10">
								      				<tr>
									      			<?php
									      			require "../connect.php";

									      			$temparray = array();

													$tags = DB::query("SELECT * FROM tags");
													$length = count($tags);

													for ($i = 0; $i < $length; $i++) {
														//information about the current tag
														$tid = $tags[$i]['rid'];
														$tagname = $tags[$i]['tagname'];
														//add it to an array
														$temp = array_push($temparray,$tagname);
													};

													//check for a possible temp tag from the SESSION
													if (isset($_SESSION['tagarray'])) {
														$tagarray = $_SESSION['tagarray'];
														//merge the two arrays
														$temparray = array_merge($temparray,$tagarray);
														//unset it for the next round
														unset($_SESSION['tagarray']);
													}

													for ($i = 0; $i < count($temparray); $i++) {
														//STAR AND TID ID ARE THE SAME CHANGE THEM
														//update length
														$length = count($temparray);

														printf('<td onclick="update(this.id)" id="t%s" class="bck"><div 								class="col-xs-12"><h5>%s</h5></div></td>',$i,$temparray[$i]);
														
														//check if we're at the end of the row
														if ((($i+1) % 4 == 0) && $i !== $length-1) {
															echo '</tr><tr>';
														};
													};

									      			?>
									      		</table>
								      		</div>
								    	</div>
								    	
								    	<br><br>

								
			    					</div>						
									</div>
								</div>

								<br><br>

								<!--  Recipe Duration -->
								<div class="row">
									<div class="col-xs-4 col-xs-offset-1 inputtables">
										<!-- title-->
										<div class="row">
											<div class="col-xs-13">

												<center><h4>Duration of Recipe: &nbsp&nbsp&nbsp</h4><span>0min</span></center>
												<hr>
								      		</div>
								      	</div>
								      	<div class="row">
								      		<div class="col-xs-6 col-xs-offset-1">
								      			<div class="input-group">
								      				<input type="range" class="rangeinput userinput" name="duration" value="0" min="0">
								      			</div>
								      		</div>
								      	</div>
							     	</div>
							     <!--<table class="inputtables">
											<tr>
												<td><span>Recipe Duration:</span></td>
												<td>
													<div class="input-group">
														<span>Hours: </span>
											      		<input type="number" id="hrsinput" value="0" onchange="checkNum(this.id);" name="hours" min="0" style="width: 35px;">
											      		<span>&nbsp&nbspand Minutes: </span>
											      		<input type="number" id="mininput" value="0" onchange="checkNum(this.id);" name="minutes" min="0" style="width: 35px;">
											      	</div>
										      	</td>
									      	</tr>
								      	</table>-->
							     	
							     </div>

							      <br>

							      <?php

							      if ($error_notfilled == 1) {
							      	echo '<center><div class="row"><div class="col-xs-3 col-xs-offset-4"><div class="alert alert-warning">
									  <strong>Warning!</strong> One or more fields were not filled out.
									</div></div></div></center>';
							      }

							      ?>

							      <br>
					
							    <!-- Recipe Picture -->
								<div class="col-xs-5" id="picturearea">
									<form method="post" action="index.php" enctype="multipart/form-data">

									<input type="hidden" name="rating" value="0" id="rating">
									<input type="hidden" name="taglist" value="" id="taglist">	
									<input type="hidden" name="tagnames" value="" id="tagnames">

									<!-- The recipe picture area -->
									<table style="background-color: white;" id="profpictable">
										<tr>
											<td style="text-align: center">
												<img src="../../resources/anon_food.png" width="180px" height="180px" id="recipepic">

				    							<p id="recipetask"><i>Upload recipe image</i></p>
				    							<hr>
				    							<div class="form-group col-xs-10 col-xs-offset-1">

		    									<input type="file" class="form-control" name="fileToUpload" id="uploader" onchange="picture = 1; uploaded(this);">
											</td>
										</tr>
									</table>
								</div>

								<!-- Submit button -->
								<div class="row">
									<div class="col-xs-3 col-xs-offset-4">
				    					<input type="submit" name="submit" value="Continue creation" class="form-control input-lg" id="submit" disabled>
				    				</div>
			    				</div>
		    				</div>

		    				</form>
		    			</div>
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


if (isset($_POST['submit'])) {

};






?>
</body>
</html>