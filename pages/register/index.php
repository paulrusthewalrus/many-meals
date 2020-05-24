<?php
//connect up with other pages
session_start();

//check if a user is logged in
if (isset($_SESSION['user']) && !isset($_GET['signout'])) {
	//a user is already logged in, redirect them away from this apge
	header('Location: ../homepage');
};

//check if the user has clicked the sign out button
if (isset($_GET['signout'])) {
	//destroy any session of the user
	session_destroy();
}

//check if the user has clicked the register button
if (isset($_POST['submit'])) {
	//setup link
	require "../connect.php";

	$salt = "SpaghettiOnRiceNotVeryNice"; 

	//grab the variables from the inputs
	$un = stripslashes(trim(htmlspecialchars($_POST['user'])));
	$email = stripslashes(trim(htmlspecialchars($_POST['email'])));
	$fname = stripslashes(trim(htmlspecialchars($_POST['fname'])));
	$surname = stripslashes(trim(htmlspecialchars($_POST['surname'])));
	$pw =  sha1($_POST['password']).$salt;
	$pwconfirm = sha1($_POST['confirm']).$salt;
	$profpic = $_FILES['fileToUpload'];

	echo '<h3>'.$profpic.'</h3>';

	//check variable
	$setup = 1;

	//username and email check
	$userinfo = DB::query("SELECT * FROM users");
	$numusers = count($userinfo);
	for ($i = 0; $i < $numusers; $i++) {
		if (($un == $userinfo[$i]["username"]) or (($email == $userinfo[$i]["email"]))) {
			$setup = 0;
		}
	}

	if ($pw === $pwconfirm && $setup == 1) {
		if (isset($_POST['fileToUpload'])) {
			//setup
			$target_dir = '../../photo_storage/profile/'.$un;
			$uploadOk = 1;  //assume we good to go (collect evidence to contrary)


			//go get filename and file extension
			$target_file = $target_dir . basename($profpic['name']);  //understood
			
		    //grabs the extension
		    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		    //echo $target_file;

			// Check if image file is a actual image or fake image
		    $check = getimagesize($profpic['tmp_name']);

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
			if ($profpic['size'] > 1000000) {
		    	echo 'Sorry, your file is too large.';
		    	$uploadOk = 0;
			}

			// Allow certain file formats (UNDERSTOOD)
			if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif' ) {
		    	echo 'Sorry, only JPG, JPEG, PNG, GIF files are allowed.';
		    	$uploadOk = 0;
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
		    	echo 'Sorry, your file was not uploaded.';

			// if everything is ok, try to upload file
			} else {
		        //move the file from it's temporary location to the NEW location
		    	if (move_uploaded_file($profpic['tmp_name'], $target_file)) { //understood
		        	//echo 'The file '. $profpic['name']. ' has been uploaded.';

		            //delete any remnant of the uploaded photo
		            unset($profpic);
		    	} else {
		        	echo 'Sorry, there was an error uploading your profile picture.';
		    
		    	}
			}
		} else {
			$target_file = '../../photo_storage/profile/'.$un.'.png';
			copy('../../resources/book.png',$target_file);
		}

		//echo $target_file;

		DB::insert('users',array(
			"firstname" => $fname,
			"lastname" => $surname,
			"username" => $un,
			"password" => $pw,
			"editlevel" => 1, //automatically all registered users are guests (NOT ADMINS)
			"email" => $email,
			"profilepicture" => $target_file,
			"Joindate" => new DateTime("now")
		));

		//redirect them away
		header('Location: ../login/index.php?r=1');
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Many Meals | Register</title>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" href="../../resources/cappuccino.png" type="image/x-icon">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  	<link rel="stylesheet" type="text/css" href="stylesheet_new.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  	<script type="text/javascript" src="script.js"></script>
</head>
<body>
	<!-- Every piece of content is stored ON the background image-->
	<div class="view first_back" style="background-image: url('../../resources/background_updated.png'); background-repeat: no-repeat; background-size: cover; background-position: center center;">
		 <!-- navigation bar (if you need to mess with the nav talk to me first) -->
		<nav class="navbar navbar">
		 	<!--image w/ dropdown-->
		 	<div class="dropdown">
		 		<?php
		 		//check whether or not the user is logged in
		 		if (isset($_SESSION['user']) && !isset($_GET['signout'])) {
		 			//a user exists
		 			printf('<table><tr><td><img class="navbar-brand profile dropbtn" src="%s"></td></tr></table>',$_SESSION['user'][3]);
		 			//using get to determine what the users' choice is
		 			echo '<div class="dropdown-content">
		 				  	<a href="../myrecipes">My Recipes</a>
		 				  	<a href="../homepage/index.php?signout=1"><span class="fa fa-sign-out"></span>Signout</a>
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
			<!-- insert html content below this line -->

			<div class="well well-sm">
				<h2 class="title">Welcome to Many Meals! Your #1 Recipe Sharing Platform</h2>
			</div>

			<!-- Using bootstrap rows and columns in order to organise the information
				inside the wells. Wells use lg, info uses xs, to centre we use xs-offset -->

			<div class="row content">
				<div class="col-lg-8 col-lg-offset-2">
					<div class="well well-sm login-stuff">  <!-- action: "../login/index.php?r=1" -->
						<form method="post" action="index.php" name="register" id="form" enctype="multipart/form-data">
							<h4>Join our foodie community today! Start here!</h4>
							<hr>
							<!-- Where all the information gathering begins-->
							<div class="row">
								<!-- Text questions -->
								<div class="textq col-xs-5 col-xs-offset-1">
									<div class="row">
										<div class="col-xs-12">
											<div class="input-group" id="userdivinput">
						      					<input id="user" onkeyup="testInfo();" type="text" class="form-control userinput" name="user" placeholder="Username" required>
						      					<span id="userglyphcontainer" class="input-group-addon"><i id="userglyph" class="glyphicon glyphicon-user"></i></span>
					      					</div>
					    				</div>
					    			</div>

					    			<p></p>

					    			<div class="row">
										<div class="col-xs-12">
											<div class="input-group" id="emaildivinput">
												<input id="email" onkeyup="testInfo();" type="email" class="form-control userinput" name="email" placeholder="Email" required>
												<span id="emailglyphcontainer" class="input-group-addon"><i id="emailglyph" class="glyphicon glyphicon-envelope"></i></span>
											</div>
					    				</div>			
				    				</div>

				    				<br>

				    				<div class="row">
										<div class="col-xs-12">
											<div class="input-group">
						      					<input id="fname" type="text" class="form-control userinput" onkeyup="testInfo();" name="fname" placeholder="First name" required>

						      					<span class="input-group-addon"><i class="fa fa-user-o" style="font-size: 18px"></i></span> 
					      					</div>
					    				</div>
					    			</div>

					    			<p></p>

					    			<div class="row">
										<div class="col-xs-12">
											<div class="input-group">
												<input id="surname" type="text" class="form-control userinput" onkeyup="testInfo();" name="surname" placeholder="Last name" required>

												<span style="font-size: 18px" class="input-group-addon"><i class="fa fa-user-o"></i></span>
											</div>
					    				</div>
				    				</div>

				    				<br>

				    				<div class="row">
										<div class="col-xs-12">
											<div class="input-group">
												<input id="password" type="password" class="form-control userinput" onkeyup="testInfo();" name="password" placeholder="Password" required>

												<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
											</div>
					    				</div>
					    			</div>

					    			<p></p>

					    			<div class="row">
										<div class="col-xs-12">
											<div class="input-group" id="confirmdivinput">
												<input id="confirm" type="password" class="form-control userinput" onkeyup="testInfo();" name="confirm" placeholder="Confirm Password" required>

												<span class="input-group-addon"><i class="glyphicon glyphicon-lock" id="confirmglyph"></i></span>
											</div>
					    				</div>
				    				</div>
			    				</div>

			    				<!-- Profile pic -->
			    				<div class="profilepicq col-xs-13">
			    					<div class="row">
			    						<div class="col-xs-6">
			    							<img src="../../resources/guest.png" width="180px" height="180px" id="prof">
			 
			    							<p><i>Choose a Profile Picture</i></p>
			    							<p><i><b>Warning:</b> This Cannot Be Changed Later</i></p>

			    							<div class="form-group col-xs-8 col-xs-offset-2">
			    								<!--<input type="file" class="form-control" name="fileToUpload" id="uploader" onchange="uploaded(this);">-->
			    								<input type="file" class="form-control" name="fileToUpload" id="uploader" onchange="uploaded(this);">
			    							</div>

			    						</div>
			    					</div>
			    				</div>

		    				</div>   <!-- row finish -->

		    				<div class="warning-message">
		    					<strong>
		    						<p id="warning-text">This email is already in use</p>
		    					</strong>
		    				</div>	
		    				
		    				<br>

		    				<div class="row">
		    					<div class="col-xs-2 col-xs-offset-5">
		    						<input type="submit" name="submit" value="Register" class="form-control input-lg" id="submit" disabled>
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
/*
//check if the user has clicked the sign out button
if (isset($_GET['signout'])) {
	//destroy any session of the user
	session_destroy();
}

//check if the user has clicked the register button
if (isset($_POST['submit'])) {
	//setup link
	require "../connect.php";

	//grab the variables from the inputs
	$un = stripslashes(trim(htmlspecialchars($_POST['user'])));
	$email = stripslashes(trim(htmlspecialchars($_POST['email'])));
	$fname = stripslashes(trim(htmlspecialchars($_POST['fname'])));
	$surname = stripslashes(trim(htmlspecialchars($_POST['surname'])));
	$pw =  sha1($_POST['password']);
	$pwconfirm = sha1($_POST['confirm']);
	$profpic = $_FILES['fileToUpload'];

	//check variable
	$setup = 1;

	//username and email check
	$userinfo = DB::query("SELECT * FROM users");
	$numusers = count($userinfo);
	for ($i = 0; $i < $numusers; $i++) {
		if (($un == $userinfo[$i]["username"]) or (($email == $userinfo[$i]["email"]))) {
			$setup = 0;
		}
	}

	if ($pw === $pwconfirm && $setup == 1) {
		//setup
		$target_dir = '../../photo_storage/profile/'.$un;
		$uploadOk = 1;  //assume we good to go (collect evidence to contrary)


		//go get filename and file extension
		$target_file = $target_dir . basename($profpic['name']);  //understood
		
	    //grabs the extension
	    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

	    //echo $target_file;

		// Check if image file is a actual image or fake image
	    $check = getimagesize($profpic['tmp_name']);

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
		if ($profpic['size'] > 1000000) {
	    	echo 'Sorry, your file is too large.';
	    	$uploadOk = 0;
		}

		// Allow certain file formats (UNDERSTOOD)
		if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif' ) {
	    	echo 'Sorry, only JPG, JPEG, PNG, GIF files are allowed.';
	    	$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
	    	echo 'Sorry, your file was not uploaded.';

		// if everything is ok, try to upload file
		} else {
	        //move the file from it's temporary location to the NEW location
	    	if (move_uploaded_file($profpic['tmp_name'], $target_file)) { //understood
	        	//echo 'The file '. $profpic['name']. ' has been uploaded.';

	            //delete any remnant of the uploaded photo
	            unset($profpic);
	    	} else {
	        	echo 'Sorry, there was an error uploading your profile picture.';
	    
	    	}
		}
		
		DB::insert('users',array(
			"firstname" => $fname,
			"lastname" => $surname,
			"username" => $un,
			"password" => $pw,
			"editlevel" => 1, //automatically all registered users are guests (NOT ADMINS)
			"email" => $email,
			"profilepicture" => $target_dir,
			"Joindate" => new DateTime("now")
		));

		//redirect them away
		header('Location: ../login/index.php?r=1');

	}
}*/


?>
</body>
</html>