<?php
	//connect up with other pages
	session_start();

	//setup the connection with the database
	require "../connect.php";

	//check if the user is logged in
	if (isset($_SESSION['user'])) {
		$loggedin = true;
	} else {
		$loggedin = false;
	}

	//grab all information from recipes
	$info = DB::query("SELECT * FROM recipes");
	//find the number of recipes
	$num = count($info);

	//echo 'Count in Array: '.$num.'<br>';
	//echo 'RID: '.$_GET['recipe'];


	//check if they were directed here
	if (isset($_GET["recipe"])) {
		//take them to that recipe
		$rid = $_GET["recipe"]; //minus num. of deleted recipes

		$url = "index.php";
		//$diff = $_GET['recipe'] - $num;
		//$recipeindex = $_GET['recipe'] - $diff;
	} else {
		//otherwise randomise it
		//$recipeindex = rand(0,$num-1);
		//header('Location: ../homepage');
		//change redirection url
		$url = "index.php";
	}

	//check if a comment took us here (reload the page)
	if (isset($_POST["submitBtn"]) || isset($_POST['deletecomment'])) {
		//$recipeindex = $_POST["rid"]-1;
		$rid = $_GET["recipe"];
		$string = $_POST["rid"];
		//echo $string;

		//refresh the page
		header("Refresh: 0; url='index.php?recipe=".$string."'");
	};

	//$recipeindex = rand(0,$num-1);
	//$recipeindex = 0; <-- GET variable of what RID the user wants to see. 

	//get all the information on that recipe
	//$recipe = $info[$recipeindex];

	$recipe = DB::query('SELECT * FROM recipes WHERE rid = %s',$rid)[0];

	//echo $recipeindex.'<br>';
	//echo $num;

	//get specific information about recipe
	$rid = $recipe["rid"];
	$name = $recipe["name"];
	$description = $recipe["description"];
	$duration = $recipe["cookingduration"];
	$servings = $recipe["servings"];
	$rating = $recipe["rating"];
	$image = $recipe["picture"];
	$difficulty = $recipe["difficulty"];

	//determine author
	$userinfo = DB::query("SELECT * FROM users WHERE uid = %s",$recipe["author"]);
	$fname = $userinfo[0]["firstname"];
	$surname = $userinfo[0]["lastname"];
	$author = $fname." ".$surname;
	$authorRecipePage = "../myrecipes/?uid=".$recipe["author"];

	//find recipe tags
	$taginfo = DB::query("SELECT * FROM tags WHERE rid = %s",$rid);
	$number = DB::count();
	$plate = '';         
	for ($i = 0; $i < $number; $i++) {
		if ($i == $number) {
			$plate = "<a href='#''>".$plate.$taginfo[$i]['tagname']."</a>";
		} else {
			$plate = $plate."<a href='#'>".$taginfo[$i]['tagname']."</a>".", ";
		}
	}

	//find recipe comments
	$commentinfo = DB::query("SELECT * FROM comments WHERE ref = %s",$rid);
	$numcomments = DB::count();

	if (isset($_SESSION['user'])) {
		//find the current user
		$current_user_info = DB::query('SELECT * from users WHERE profilepicture = %s',$_SESSION['user']);
		$uid = strval($current_user_info[0]['uid']);
		$redirect_url = '../myrecipes/index.php?uid='.$uid;
	}

	//check if the user has deleted a recipe
	if (isset($_POST['deleterecipe'])) {
		header('Location: ../myrecipes/index.php?uid='.$uid);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | <?php echo $name; ?></title>
	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="style_del_recipe.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script_delrecipe.js"></script>
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

		 					 /*<a href="../admin.php"><span><i class="fa fa-connectdevelop"></i></span>&nbspDeveloper Login</a>*/
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
			<div class="row">
				<!-- left side column (profile, ingredients, equipment) -->
				<div class="col-xs-4">
					<!-- Recipe profile -->
					<div class="col-xs-12 recipe-profile">
						<div class="well well-sm">
							<?php
							echo '<h3>'.$name.'</h3><hr>';

							//echo '<h3>'.$name.'</h3><hr>';
							//echo "<img style='width: 80%' src='data:image/png;base64,".base64_encode($image)."'/><br><br>";
							echo "<p><img style='width: 80%;' src='".$image."'><br><br>";
							echo '<p>'.$description.'</p>';
							echo '(<img src="../../resources/ratings/'.$rating.'star.png" style="width: 30%;">)</p>';
							echo '<p><strong>Cooking Duration: </strong>'.$duration.'min </p>';
							echo '<p><strong>Tags: </strong>'.$plate.'</p>';
							echo '<p><strong>Servings: </strong>'.$servings.'</p>';
							echo '<a href="'.$authorRecipePage.'"><p><strong>Author: </strong>'.$author.'</p></a>';

							?>
						</div>
					</div>

					<!-- Ingredients (HTML/CSS) -->
					<div class="col-xs-12">
						<div class="well well-lg" style="text-align: center">	
							<h4>Ingredients:</h4>
							<hr>
							<?php
							
								//info about ingredients
								$ingredientinfo = DB::query("SELECT * from ingredients where rid='".$rid."'");
								//find the number of ingredients
								$ingredientcount = DB::count();
								$ingcount = 0;

								echo '<div class="row" style="word-wrap: break-word;">';

								for ($i = 0; $i < $ingredientcount; $i++) {

									$ingname = $ingredientinfo[$i]["ingredientname"];

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
						$equipinfo = DB::query("SELECT * from equipment where rid='".$rid."'");
						//find the number of ingredients
						$equipcount = DB::count();
						$eqcount = 0;
						if ($equipcount > 0) {
						echo '<div class="col-xs-12">';
							echo '<div class="well well-lg" style="text-align: center">';
								echo'<h4>Equipment:</h4>';
								echo'<hr>';
									echo '<div class="row" style="word-wrap: break-word;">';

									for ($i = 0; $i < $equipcount; $i++) {

										$eqname = $equipinfo[$i]["equipmentname"];

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
					?>
	
					<!--<div class="col-xs-12">
						<div class="well well-lg" style="text-align: center">	
							<h4>Equipment:</h4>
							<hr>	
							<div class="row" style="word-wrap: break-word;">
								<div class="col-xs-6">
									<p class="rcitem">Spatula</p>
								</div>
								<div class="col-xs-6">
									<p class="rcitem">Frying Pan</p>
								</div>
							</div>

							<div class="row" style="word-wrap: break-word;">
								<div class="col-xs-6">
									<p class="rcitem">Fork</p>
								</div>
								<div class="col-xs-6">
									<p class="rcitem">Knife</p>
								</div>
							</div>
						</div>
					</div>-->
				</div>

				<!-- right side column (steps, comments) -->
				<div class="col-xs-8">
					<!-- Recipe Steps -->
					<div class="col-xs-20">
						<div class="well">
							<div class="row">
								<?php
									//check if the user owns this recipe
									$author_rid = $recipe['author'];
									if (isset($_SESSION['user'])) {
										if ($uid == $author_rid) {
											echo '<h3 style="text-align: center">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGet cooking:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href="#" id="recid'.$rid.'" class="delrecipebtn" onclick="delRecipe(this.id);">(<i class="delrecipebtn fa fa-times" style="font-size: 15px"></i>)</a></h3>';
											//echo '<h3 style="text-align: center">Get cooking now:</h3>';
										} else {
											echo '<h3 style="text-align: center">Get cooking now:</h3>';
										}
									} else {
										echo '<h3 style="text-align: center">Get cooking now:</h3>';
									}
								?>
								<hr>
								<!-- Steps -->
								<div class="col-xs-11">
								<?php
									//find all the steps and plate them up
									$stepinfo = DB::query("SELECT * FROM steps WHERE rid = %s ORDER BY stepnum",$recipe["rid"]);
									$stepcount = DB::count();

									
									//begin the step loop
									for ($i = 0; $i < $stepcount; $i++) {
										//print out the checkbox
										echo '<table><tr><td><input type="checkbox" onchange="update(this.id);"id="'.(($i+1)*10).'" class="check"></td>';

										//print out spacing
										echo '<td>&nbsp</td><td>&nbsp</td>';

										//print out the step
										echo '<td><span class="'.(($i+1)*10).'"><strong style="font-size: 18px;">'.($i+1).'.</strong> '.$stepinfo[$i]["stepdescription"].'</span></td></tr></table><br>';

										//if we're at the end, finish the table
										/*if ($i+1 == $stepcount) {
											echo '</table>';
										}*/
									}
								?>
								</div>

							</div>
						</div>
					</div>

					<!-- Recipe Comments --> 
					<div class="col-xs-13">
						<div class="row">
								<div class="col-xs-12">
									<div class="well">
										<!-- Comment Creator (ONLY FOR LOGGED IN USERS) -->
										<?php
										if ($loggedin) {

											//check what the title will be
											if ($numcomments > 0) {
												$string = "Comment and rate this recipe:";
											} else {
												$string = "Be the first to comment and rate:";
											}

											echo '<!-- make a comment -->
											<div class="row commentcreator">
												<div class="col-xs-8 col-xs-offset-4">
													<h4>'.$string.'</h4>
												</div>
												<br><br>
												<!-- fill information out -->
												<form method="post" action="index.php?recipe='.$rid.'" name="commentForm" id="commentForm">
													<div id="delcomment"></div>
													<input type="hidden" name="rid" value="'.$rid.'">
													<div class="well your-comment col-xs-10 col-xs-offset-1">
														<div class="row">	
															<!-- comment title -->
															<div class="col-xs-6">							
																<input type="text" name="title" class="form-control" placeholder="Comment Title" id="inputtitle" required>
															</div>

															<!-- Rating -->
															<div class="stars col-xs-offset-6">
																<div class="col-xs-2">							
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
																</div>	
															</div>	

														</div>
														<p></p>
														<!-- comment content -->
														<div class="row">	
															<div class="col-xs-12">							
																<input type="text" name="content" class="form-control" placeholder="Your comment..." id="inputcontent" required>
															</div>
														</div>
														<!-- Holds UID of person who"s being replied to-->
														<input type="hidden" name="replyingto" value="0" id="reply">
														<p></p>
														<!-- Submit button -->
														<div class="row">
															<div class="col-xs-4 col-xs-offset-4 submitrow">			
																<strong><input type="submit" name="submitBtn" class="new-comment form-control" value="Post comment" id="finalpost"></strong>
															</div>
														</div>
													</div>
													<!-- adjusted with javscript -->
													<input type="hidden" name="rating" value="0" id="rating">
													<!-- the current recipe id -->
													<input type="hidden" name="rid" value="'.$rid.'">
												</form>
											</div>';

											if ($numcomments > 0) {
												echo '<!-- divider -->
											<div class="row">
												<hr width="700px">
											</div>';
											}
											
										}
										?>

										<!-- The rest of the comments (ACCESSIBLE TO ALL USERS) -->
										<?php
										//check if a comment exists
										if ($numcomments > 0) {

										//style it all up
										echo '<div class="col-xs-13">								
											<h3 style="text-align: center">Comments:</h3><br>
											
												<!-- Comment section -->
												<div class="col-xs-14">';
													

													//post all the comments
													for ($i = 0; $i < $numcomments; $i++) {
														
														//get the profile picture
														$author = $commentinfo[$i]['author'];
														$authorinfo = DB::query("SELECT * FROM users WHERE uid = %s",$author);
														$finalpic = $authorinfo[0]["profilepicture"];
														$authorname = $authorinfo[0]["username"];
														//get the rating
														$cmtrating = $commentinfo[$i]['rating'];
														//title
														$cmttitle = $commentinfo[$i]['title'];
														//content
														$cmtcontent = $commentinfo[$i]['content'];
														//cmtid
														$cmtid = $commentinfo[$i]['cid'];


														echo '<div class="row">';
														echo '<div class="well comment col-xs-10 col-xs-offset-1" style="background-color: white">';
														echo '<div class="row">';

														//profile picture
														echo '<div class="col-xs-2">';
														printf('<img src="%s" class="col-xs-offset-2 commentprofpicture">',$finalpic);
														echo '<img src="../../resources/ratings/'.$cmtrating.'star.png" class="col-xs-offset-2 commentstar">';
														echo '</div>';

														if (isset($_SESSION['user'])) {
															//check if the user owns the comment
															$info = DB::query('SELECT * FROM users WHERE profilepicture = %s',$_SESSION['user']);
															$current = $info[0]['uid'];

															//check if the user is an admin
															$editlevel = $info[0]['editlevel'];
														} else {
															$current = 'undefined';
															$editlevel = 0;
														}
														//allow the user to delete their comment
														if ($author == $current || $editlevel == '2') {
															echo '<div class="col-xs-7">';
															echo '<b class="ctitle">'.$cmttitle.'</b>&nbsp&nbsp&nbsp(<a href="#" id="cmtid'.$cmtid.'" onclick="delComment(this.id);"><i class="fa fa-times" style="font-size: 15px"></i></a>)</center>';
														} else {
															echo '<div class="col-xs-7">';
															echo '<b class="ctitle">'.$cmttitle.'</b></center>';
														}


														echo '<p class="ccontent">'.$cmtcontent.'</p>';
														echo '<a href="../myrecipes/index.php?uid='.$author.'" class="authorclick"><i>- '.$authorname.'</i></a>';
														echo '</div>';

														//reply button
														if (isset($_SESSION['user'])) {
														echo '<div class="col-xs-3">
																	<br>
																	<center>
																		<a onclick="replyClick(this.id);" class="quotebtn col-xs-offset-1" id="'.$cmtid.'"><i class="fa fa-quote-left""></i>
																		</a>

																		
																	</center>
																</div>';
														}

														echo '</div>';
														echo '</div>';
														echo '</div>'; //finishing a single comment

														//FIND OUT THE REPLY INFORMATION

														$replyinfo = DB::query('SELECT * FROM replies WHERE cid = %s ORDER BY postdate ASC',$cmtid);
														$numreplies = count($replyinfo);

														if ($numreplies > 0) {
															for ($k = 0; $k < $numreplies; $k++) {
																//grab specific author information
																$replycontent = $replyinfo[$k]['content'];
																$replyauthor = $replyinfo[$k]['uid'];

																$replyauthorinfo = DB::query('SELECT * FROM users WHERE uid = %s',$replyauthor);
																$replyusername = $replyauthorinfo[0]['username'];
																$replypicture = $replyauthorinfo[0]['profilepicture'];

																//plate it all up
																echo '<div class="row">
																	<div class="col-xs-2 col-xs-offset-1"></div>
																	<!-- reply area -->
																	<div class="col-xs-8 well reply">
																		<!-- Profile Picture inside comment -->
																			<div class="col-xs-2">
																				<br>';
																		
																printf('<center><img src="'.$replypicture.'" class="col-xs-offset-2 replyprofpicture"></center>');
																		
																echo '</div>

																		<!-- Comment content/title -->
																		<div class="col-xs-10">
																			<p class="ccontent">'.$replycontent.'</p>

																			<a href="../myrecipes/index.php?uid='.$replyauthor.'" class="authorclick"><i>-'.$replyusername.'</i></a>
																		</div>

																		</div>
																		</div>';
															}

														}	
													}

													?>
													<!-- example comment 2 ->
													<div class="row">
														<div class="well comment col-xs-10 col-xs-offset-1" style="background-color: white">
															<div class="row">
																<!- Profile Picture inside comment ->
																<div class="col-xs-2">
																	<br>
																	<?php
																	//printf('<img src="../../resources/book.png" class="col-xs-offset-2 commentprofpicture">');
																	?>

																	<img src="../../resources/ratings/3star.png" width="65px" class="col-xs-offset-2 commentstar">
																</div>

																<!- Comment content/title ->
																<div class="col-xs-7">
																	<b class="ctitle">Title of Comment</b>
																	<p class="ccontent">This is the total area of the comment content area. This is the total area of the comment content area. This is the total area of the comment content area. This is the total area of the comment content area. This is the total area of the comment content area.</p>
																</div>

																<!- Reply button ->
																<div class="col-xs-3">
																	<br>
																	<center><a onclick="replyClick(this.id);" class="quotebtn col-xs-offset-1" id="1"><i class="fa fa-quote-left"></i></a>
																	<br><br><br>
																	<i class="fa fa-times" style="font-size: 25px"></i></center>
																</div>

																	
															</div>							
														</div>
													</div>-->

													<!-- Example Reply 1->
													<div class="row">
														<div class="col-xs-2 col-xs-offset-1"></div>
														<!- reply area ->
														<div class="col-xs-8 well reply">
															<!- Profile Picture inside comment ->
																<div class="col-xs-2">
																	<br>
																	<?php
																	//printf('<center><img src="../../resources/book.png" class="col-xs-offset-2 replyprofpicture"></center>');
																	?>
																</div>

																<!- Comment content/title ->
																<div class="col-xs-10">
																	<p class="ccontent">This is the total area of the comment content area. This is the total area of the comment content area. This is the total area of the comment content area. This is the total area of the comment content area. This is the total area of the comment content area.</p>

																	<a href="../myrecipes/index.php?uid=4" class="authorclick"><i>-xfilez</i></a>
																</div>
														</div>		
													</div>-->

													<?php

												echo '</div>';
												

												/*<span>
													<?php	
													<div class="row">
														<!-- Left Column -->
														<div class="col-xs-6">
															<div class="col-xs-12">
																<div class="well comment">
																	<div class="row">
																		<!-- Profile Picture inside comment -->
																		<div class="col-xs-3">

																			/*<?php
																			printf('<img src="%s" width="50px">',$_SESSION['user'][3]);
																			?>

																			<br><br>
																			<a onclick="replyClick(this.id);" class="quotebtn col-xs-offset-2" id="1"><i class="fa fa-quote-left"></i></a>
																		</div>

																		<!-- Comment content/title -->
																		<div class="col-xs-9">
																			<b class="ctitle">Title of Comment</b>
																			<p class="ccontent">This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here.</p>
																		</div>
																	</div>	
																	<hr>
																	<b style="font-size: 16px" class="col-xs-offset-5">Replies:</b>
																	<br><br>
																	<div class="row">
																		<!-- Profile Picture inside comment -->
																		<div class="col-xs-3">

																			/*<?php
																			printf('<img src="%s" width="50px">',$_SESSION['user'][3]);
																			?>

																			<br><br>
																			<a onclick="replyClick(this.id);" class="quotebtn col-xs-offset-2" id="1"><i class="fa fa-quote-left"></i></a>
																		</div>

																		<!-- Comment content/title -->
																		<div class="col-xs-9">
																			<p class="ccontent">This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here.</p>
																		</div>
																	</div>						
																</div>
															</div>

															<div class="col-xs-10">
																<div class="well comment">
																	<div class="row">
																		<!-- Profile Picture inside comment -->
																		<div class="col-xs-3">
																		/*
																			<?php
																			printf('<img src="%s" width="50px">',$_SESSION['user'][3]);
																			?>

																			<br><br>
																			<a onclick="replyClick(this.id);" class="quotebtn col-xs-offset-2" id="1"><i class="fa fa-quote-left"></i></a>
																		</div>

																		<!-- Comment content/title -->
																		<div class="col-xs-9">
																			<p class="ccontent">This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here.</p>
																		</div>
																	</div>								
																</div>
															</div>*/
															




															 /* <div class="well comment">
																<div class="row">
																	<!-- Profile Picture inside comment -->
																	<div class="col-xs-3">

																		<?php
																		printf('<img src="%s" width="50px">',$_SESSION['user'][3]);
																		?>

																		<br><br>
																		<a onclick="replyClick(this.id);" class="quotebtn col-xs-offset-2" id="1"><i class="fa fa-quote-left"></i></a>
																	</div>

																	<!-- Comment content/title -->
																	<div class="col-xs-9">
																		<b class="ctitle">Title of Comment</b>
																		<p class="ccontent">This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here. This is the total area of the comment content area. Anything can be here.</p>
																	</div>
																</div>								
															</div>
														</div>
												</span>	*/
										echo '</div>';	
										}		?>
									</div>
								</div>			
						</div>
					</div>
				</div>
			</div>
		</div>
			
		<!-- content ^^^ going past this line means doing stuff without a background -->
	</div>	

<?php
//check if the user has clicked the sign out button
if (isset($_GET['signout'])) {
	//destroy any session of the user
	session_destroy();
}

//check if the user wants to delete a recipe
if (isset($_POST['deleterecipe'])) {
	//grab the id of the comment to be deleted
	$rid_delete = $_POST['deleterecipe'];

	//create a connection
	//$conn = new mysqli('localhost','root','','manymeals');

	/*//delete all comments
	$recipe_comments = DB::query('SELECT * FROM comments WHERE ref = %s',$rid_delete);
	$num_comments = count($recipe_comments);
	for ($i = 0; $i < $num_comments; $i++) {
		//grab the comment id
		$cid_delete = $recipe_comments[$i]['cid'];
		//delete this comment id from the database 
		DB::delete('comments','cid=%s',$cid_delete);
	}*/

	//delete all equipment
	/*$recipe_equip = DB::query('SELECT * FROM equipment WHERE rid = %s',$rid_delete);
	$num_equip = count($recipe_equip);
	for ($i = 0; $i < $num_equip; $i++) {
		//grab the comment id
		$equip_delete = $recipe_equip[$i]['equipmentname'];
		$equip_rid = $recipe_equip[$i]['rid'];
		//delete this comment id from the database 
		//DB::delete('equipment','rid=%s and equipmentname=%s',$equip_rid,$equip_delete);
		$qry = 'DELETE FROM equipment WHERE rid = '.$rid_delete.' AND equipmentname = '.$equip_delete;
		if ($conn->query($qry) === TRUE) {
		    //echo "Record deleted successfully";
		} else {
		    //echo "Error deleting record: " . $conn->error;
		}
	}*/

	//delete all ingredients
	/*$ingredients = DB::query('SELECT * FROM ingredients WHERE rid = %s',$rid_delete);
	$num = count($ingredients);
	for ($i = 0; $i < $num; $i++) {
		//grab the comment id
		$rid_delete = $ingredients[$i]['rid'];
		$ing_delete = $ingredients[$i]['ingredientname'];
		//delete this comment id from the database 
		//DB::delete('ingredients','rid=%s and ingredientname=%s',$rid_delete,$ing_delete);
		$qry = 'DELETE FROM ingredients WHERE rid = '.$rid_delete.' AND ingredientname = '.$ing_delete;
		if ($conn->query($qry) === TRUE) {
		    //echo "Record deleted successfully";
		} else {
		    //echo "Error deleting record: " . $conn->error;
		}
	}*/

	/*//delete all steps
	$steps = DB::query('SELECT * FROM steps WHERE rid = %s',$rid_delete);
	$num = count($steps);
	for ($i = 0; $i < $num; $i++) {
		//grab the comment id
		$rid_delete = $steps[$i]['rid'];
		$stepnum = $steps[$i]['stepnum'];
		//delete this comment id from the database 
		//DB::delete('steps','rid=%s and stepnum=%s',$rid_delete,$stepnum);
		$qry = 'DELETE FROM steps WHERE rid = '.$rid_delete;
		if ($conn->query($qry) === TRUE) {
		    //echo "Record deleted successfully";
		} else {
		    //echo "Error deleting record: " . $conn->error;
		}
	}*/

	DB::delete('equipment','rid=%s',$rid_delete);
	DB::delete('ingredients','rid=%s',$rid_delete);
	DB::delete('steps','rid=%s',$rid_delete);
	DB::delete('tags','rid=%s',$rid_delete);
	DB::delete('comments','ref=%s',$rid_delete);

	//delete all tags
	/*$ingredients = DB::query('SELECT * FROM tags WHERE rid = %s',$rid_delete);
	$num = count($ingredients);
	for ($i = 0; $i < $num; $i++) {
		//grab the comment id
		$rid_delete = $ingredients[$i]['rid'];
		$tagname = $ingredients[$i]['tagname'];
		//delete this comment id from the database 
		DB::delete('tags','rid=%s',$rid_delete);
		//$qry = 'DELETE FROM tags WHERE rid = '.$rid_delete.' AND tagname = '.$tagname;
		/*if ($conn->query($qry) === TRUE) {
		    //echo "Record deleted successfully";
		} else {
		    echo "Error deleting record: " . $conn->error;
		}
	}*/

	
	//delete all replies
	$replies = DB::query('SELECT * from replies WHERE cid in (SELECT cid FROM comments WHERE ref=%s)',$rid_delete);
	$num = count($replies);
	for ($i = 0; $i < $num; $i++) {
		//grab the reply id
		$reply_delete = $replies[$i]['pid'];
		//delete this comment id from the database 
		DB::delete('replies','pid=%',$reply_delete);
	}

	//delete this recipe id from the database 
	DB::delete('recipes','rid=%s',$rid_delete);

	//$conn->close();
}

//check if the user wants to delete a comment
if (isset($_POST['deletecomment'])) {
	//grab the id of the comment to be deleted
	$cid_delete = $_POST['deletecomment'];
	//delete this comment id from the database 
	DB::delete('comments','cid=%s',$cid_delete);

	//update rating
	$ratinginfo = DB::query('SELECT rating FROM comments WHERE ref = %s',$rid);
	//find the number of ratings
	$numratings = count($ratinginfo);
	//add up all the ratings
	$sumtotal = 0;
	for ($i = 0; $i < $numratings; $i++) {
		$sumtotal = $sumtotal + $ratinginfo[$i]['rating'];
	}
	//determine an average rating
	$avg = $sumtotal/$numratings;

	if ($numratings == 0) {
		$avg = 0;
	}
	
	//update the rating for the recipe
	DB::update("recipes",array(
		"rating" => $avg
	), 'rid=%s',$rid);

	//delete any replies under the comment
	$replyinfo = DB::query('SELECT * FROM replies WHERE cid = %s',$cid_delete);
	$replies = count($replyinfo);
	if ($replies > 0) {
		for ($i = 0; $i < $replies; $i++) {
			//grab the id and delete
			$pid = $replyinfo[$i]['pid'];
			DB::delete('replies','pid=%s',$pid);
		}
	}
};

//the user has posted a comment
if (isset($_POST['submitBtn']) && isset($_POST['content'])) {
	
	//find if replying or commenting (assume replying)
	$replyingto = trim(stripslashes(htmlspecialchars($_POST['replyingto'])));

	//grab the comment content
	$content = trim(stripslashes(htmlspecialchars($_POST['content'])));

	//find the user's uid
	$userinfo = DB::query("SELECT * FROM users WHERE profilepicture = '".$_SESSION["user"]."'");
	$uid = $userinfo[0]["uid"];

	//user is commenting, so allowed to grab these variables
	if ($replyingto == 0){

		//find how many comments user has and increase it by 1 for insertion
		/*$commentnum = $userinfo[0]["comments"];
		$newcomment = $commentnum+1;*/

		//grab the variables and clean them up
		$title = trim(stripslashes(htmlspecialchars($_POST['title'])));
		$rating = trim(stripslashes(htmlspecialchars($_POST['rating'])));

		//check if a rating exists
		if ($rating !== "0") {

			$rid = $_POST['rid'];

			//insert the comment into the database
			DB::insert("comments",array(
				"title" => $title,
				"content" => $content,
				"rating" => $rating,
				"picture" => "none",
				"postdate" => new DateTime("now"),
				"ref" => $rid,
				"author" => $uid,
			));

			//grab number of comments
			$commentinfo = DB::query("SELECT * FROM comments WHERE author = '".$uid."'");
			$comments = count($commentinfo);

			//update the number of comments in the database
			DB::update("users",array(
				"comments" => $comments
			), "uid=%s", $uid);

			//update the recipe rating, firstly grab all the ratings for the recipe
			$ratinginfo = DB::query('SELECT rating FROM comments WHERE ref = %s',$rid);
			//find the number of ratings
			$numratings = count($ratinginfo);
			//add up all the ratings
			$sumtotal = 0;
			for ($i = 0; $i < $numratings; $i++) {
				$sumtotal = $sumtotal + $ratinginfo[$i]['rating'];
			}
			//determine an average rating
			$avg = $sumtotal/$numratings;

			if ($numratings == 0) {
				$avg = 0;
			}

			//update the rating for the recipe
			DB::update("recipes",array(
				"rating" => $avg
			), 'rid=%s',$rid);

		}
	} else {
	
		//insert the reply into the database WIP
		DB::insert("replies",array(
			"content" => $content,
			"cid" => $replyingto,
			"replyref" => 1,
			"postdate" => new DateTime("now"),
			"uid" => $uid
		));
	}
}






?>
</body>
</html>