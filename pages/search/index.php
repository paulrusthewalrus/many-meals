<?php
//connect up with other pages
session_start();

//setup the connection with the database
require"../connect.php";

//delete everything in temp
$files = glob('../../photo_storage/temp_recipes/'); // get all file names
foreach($files as $file){ // iterate files
	if(is_file($file)) {
    	unlink($file); // delete file
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
	<title>Many Meals | Search</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script.js"></script>
</head>

<style>
	input[type=text], select{
		width: 100%;
		padding: 10px;
		margin: 3px 0;
		display: inline-block;
		border: 1px solid #ccc;
		border-radius: 5px;
		box-sizing: border-box;
		background-color: white;
		background-image: url('../../resources/searchicon.png');
		background-size: 20px;
		background-position: 10px 10px;
		background-repeat: no-repeat;
		padding-left: 40px; 
	}

	button[type=submit] {
	  	width: 100%;
	  	background-color: #4CAF50;
	  	color: white;
	  	padding: 10px;
	  	margin: 3px 0;
	  	border: none;
	  	border-radius: 5px;
	  	cursor: pointer;
	  	-webkit-transition-duration: 0.4s;
		transition-duration: 0.4s;
	}

	button[type=button] {
	  	width: 100%;
	  	color: white;
	  	padding: 10px;
	  	margin: 0px 0;
	  	border: none;
	  	border-radius: 5px;
	  	cursor: pointer;
	  	-webkit-transition-duration: 0.4s;
		transition-duration: 0.4s;
	}

	.textboxstyle1{
		width: 100%;
		border-radius: 10px;
		background-color: #f2f2f2;
		padding: 1%;
	}

	.textboxstyle2{
		width: 70%; 
		border-radius: 10px;
		background-color: #f2f2f2;
		padding: 1%;
	}

	.textboxstyle3{
		width: 70%;
		float: left;
		border-radius: 10px;
		background-color: #f2f2f2;
		padding: 1%;
	}

	.col-2 {
		width: 67%;
		margin-top: 6px;
	}

	.space {
		padding: 10px;
	}

	.right {
		margin-top: 0.4%;
		position: absolute;
		right: 1.5%;
		width: 465px;
		background-color: #f2f2f2;
		border-radius: 10px;
		padding: 2%;
	}

	.reposition {
		top: -10px;
	}

	.imgadjust {
		border-radius: 10px;
	}

	.carousel-indicators {
		width: 70px;
		left: 308px;
		bottom: -5px;

	}

	.button1 span{
		cursor: pointer;
		display: inline-block;
	  	position: relative;
	  	transition: 0.5s;
	}

	.button1 span:after {
		content: '\00bb';
		position: absolute;
		opacity: 0;
		top: 0;
		right: -20px;
		transition: 0.5s;
	}

	.button1:hover span{
		padding-right: 25px;
	}

	.button1:hover span:after{
		opacity: 1;
		right: 0;
	}

	.button2 span{
		cursor: pointer;
		display: inline-block;
	  	position: relative;
	  	transition: 0.5s;
		
	}

	.button2 span:after {
		content: '\00bb';
		position: absolute;
		opacity: 0;
		top: 0;
		right: -20px;
		transition: 0.5s;
	}

	.button2:hover{
		background-color: #228b22;
	}

	.button2:hover span{
		padding-right: 25px;
	}

	.button2:hover span:after{
		opacity: 1;
		right: 0;
	}

	/*FOR THE TAG */
	.scrollbar-deep-purple::-webkit-scrollbar-track {
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: #F5F5F5;
	border-radius: 10px; }

	.scrollbar-deep-purple::-webkit-scrollbar {
	width: 12px;
	background-color: #F5F5F5; }

	.scrollbar-deep-purple::-webkit-scrollbar-thumb {
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: #512da8; }

	.scrollbar-cyan::-webkit-scrollbar-track {
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: #F5F5F5;
	border-radius: 10px; }

	.scrollbar-cyan::-webkit-scrollbar {
	width: 12px;
	background-color: #F5F5F5; }

	.scrollbar-cyan::-webkit-scrollbar-thumb {
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: #00bcd4; }

	.scrollbar-dusty-grass::-webkit-scrollbar-track {
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: #F5F5F5;
	border-radius: 10px; }

	.scrollbar-dusty-grass::-webkit-scrollbar {
	width: 12px;
	background-color: #F5F5F5; }

	.scrollbar-dusty-grass::-webkit-scrollbar-thumb {
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-image: -webkit-linear-gradient(330deg, #d4fc79 0%, #96e6a1 100%);
	background-image: linear-gradient(120deg, #d4fc79 0%, #96e6a1 100%); }

	.scrollbar-ripe-malinka::-webkit-scrollbar-track {
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: #F5F5F5;
	border-radius: 10px; }

	.scrollbar-ripe-malinka::-webkit-scrollbar {
	width: 12px;
	background-color: #F5F5F5; }

	.scrollbar-ripe-malinka::-webkit-scrollbar-thumb {
	border-radius: 10px;
	-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-image: -webkit-linear-gradient(330deg, #f093fb 0%, #f5576c 100%);
	background-image: linear-gradient(120deg, #f093fb 0%, #f5576c 100%); }

	.bordered-deep-purple::-webkit-scrollbar-track {
	-webkit-box-shadow: none;
	border: 1px solid #512da8; }

	.bordered-deep-purple::-webkit-scrollbar-thumb {
	-webkit-box-shadow: none; }

	.bordered-cyan::-webkit-scrollbar-track {
	-webkit-box-shadow: none;
	border: 1px solid #00bcd4; }

	.bordered-cyan::-webkit-scrollbar-thumb {
	-webkit-box-shadow: none; }

	.square::-webkit-scrollbar-track {
	border-radius: 0 !important; }

	.square::-webkit-scrollbar-thumb {
	border-radius: 0 !important; }

	.thin::-webkit-scrollbar {
	width: 6px; }

	.example-1 {
	position: relative;
	overflow-y: scroll;
	height: 140px; }

	.bck {
	  text-align: center;
	  background-color: white;
	  font-size: 14px;
	  transition: 0.2s;
	  text-decoration: none;
	}

	.bck:hover {
	  background-color: #ddd;
	  font-size: 15px;
	  transition: 0.2s;
	}

	.perm {
	  background-color: #dfa;
	}

	#addtag {
	  color: black;
	}

	#addtag:hover {
	  color: #3288bf;
	  width: 15px;
	  transition: 0.2s;
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
			<!-- insert html content below this line -->
			<!-- background repeat doesn't work so try to keep your content to the page you see -->
		<div class="container">
			<div class="row">
				<div class="col-xs-10 col-xs-offset-1">
					<div class="text-center">
						<div class="textboxstyle1">
							<h3 class="ff">Start your search now!</h3>
						</div>
					</div>
					<div class="space"></div>
					<div class="right">
						<!-- Content well containing carousel -->
						<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">

							<!-- Indicators -->
						    <ol class="carousel-indicators">
						    	<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
								<li data-target="#myCarousel" data-slide-to="1"></li>
								<li data-target="#myCarousel" data-slide-to="2"></li>
							</ol>

							<!-- Wrapper for slides -->
							<div class="carousel-inner">

								<?php
								
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
									$image = $recipe["picture"];								

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
										echo "<img class='imgadjust' src='".$image."'>";

										/*echo "<div class='carousel-caption reposition'>
												<h3>".$name."</h3>
									    	</div>";*/

									    echo "</div>";
									    		
									}
								}

								?>	

						 	</div>
						</div>
					</div>

							
					<div class="col-2">
						<div class="textboxstyle2">
							<input type="text" id="inputrnames" name="recipename" placeholder="Search for recipe names">
						</div>
					</div> 
							
					<div class="space"></div>
								
					<div class="col-2">
						<div class="textboxstyle2">
							<input type="text" id="inputingredience" name="ingredient" placeholder="Search for ingredient">
						</div>
					</div>
								
					<div class="space"></div>
								
					<div class="col-2">
						<div class="textboxstyle2">

							<!-- Trigger the modal with a button -->
							<button type="button" class="button1 btn btn-info btn-lg" data-toggle="modal" data-target="#myModal"><span>Select Tags </span></button>
						</div>

						<!-- Modal -->
						<div id="myModal" class="modal fade" role="dialog">
							<div class="modal-dialog modal-lg">

								<!-- Modal content-->
							    <div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h3 class="modal-title text-center">Tag Selection</h3>
									</div>
								
									<div class="modal-body">
									    <h4>Choose a selection of tag/s to search:</h4>
									  
										<div class="card example-1 scrollbar-ripe-malinka">
								      		<div class="card-body">
								      			<table class="table-bordered table tags" cellspacing="10">
								      				<tr>
									      			<?php
									      			
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

														printf('<td onclick="update(this.id)" id="t%s" class="bck"><div class="col-xs-12"><h5>%s</h5></div></td>',$i,$temparray[$i]);
														
														//check if we're at the end of the row
														if ((($i+1) % 4 == 0) && $i !== $length-1) {
															echo '</tr><tr>';
														};
													};

									      			?>
									      		</table>
								      		</div>
								    	</div>

								    	<br>

										<input type="hidden" name="taglist" value="" id="taglist">
									</div>
									
									<div class="modal-footer">
									    <button type="button" class="btn btn-default" data-dismiss="modal" id="searchconfirm">Close</button>
									</div>
								</div>

							</div>
						</div>
																			
					</div>
								
					<div class="space"></div>
								
					<div class="col-2">
						<div class="container textboxstyle3">
							<!---<input type="submit" class="btn-lg" value="Submit">-->
							<button type="submit"class="button2 btn btn-info btn-lg" onclick="alert('Coming in Version 2!')"><span>Search </span></button>
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
}

//insert your code below...

if (isset($_GET['searchconfirm'])) {
	
	$_GET['inputingredience'] = $_GET['searchi'];

	$_GET['recipename'] = $_GET['searchr'];

	$_GET['taglist'] = $_GET['seacht'];

}


?>

</body>
</html>