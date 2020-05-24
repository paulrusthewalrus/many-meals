<?php
require '../connect.php';

//grab the order preference
$order = $_GET['order'];
$orderstring = '';

//what should it be ordered by
if ($order == 'recent') {
	$orderstring = 'ORDER BY creatingdate DESC';
	//get recipes from database from the user
	$recipeinfo = DB::query('SELECT * FROM recipes '.$orderstring);

} else if ($order == 'popular') {
	//$orderstring = "SELECT * FROM comments GROUP BY ref ORDER BY count(*) DESC";
	//"SELECT * FROM comments GROUP BY ref ORDER BY count(*) DESC"

	/*
	SELECT recipes.*, COUNT(comments.cid) AS comment_count
    FROM recipes LEFT JOIN comments
    ON recipes.rid = comments.ref
    GROUP BY recipes.rid
    ORDER BY comment_count DESC
	
	*/

	$recipeinfo = DB::query('SELECT recipes.*
						     FROM recipes LEFT JOIN comments
						     ON recipes.rid = comments.ref
						     GROUP BY recipes.rid
						     ORDER BY COUNT(comments.cid) DESC');
} else {
	$orderstring = 'ORDER BY rating DESC';
	//get recipes from database from the user
	$recipeinfo = DB::query('SELECT * FROM recipes '.$orderstring);
}

$length = count($recipeinfo);
//echo $length;

for ($i = 0; $i < $length; $i++) {
	//grab the recipe
	$recipe = $recipeinfo[$i];
	
	//echo $i;

	$name = $recipeinfo[$i]["name"];
	$description = $recipeinfo[$i]["description"];
	$duration = $recipeinfo[$i]["cookingduration"];
	$servings = $recipe["servings"];
	$image = $recipe["picture"];
	$difficulty = $recipe["difficulty"];
	$rating = $recipe['rating'];
	$author = $recipe["author"];
	$rid = $recipe["rid"];
	$url = '../recipeView/?recipe='.$rid;


	//echo $rid.'<br>';
	$tags = DB::query("SELECT * FROM tags WHERE rid = %s", $rid);
	$number = count($tags);

	$plate = '';
	foreach($tags as $tag) {
		$plate = $plate."<a href='#''>".$tag['tagname']."</a>, ";
	}    

	if ((($i+1) % $number+1 == 0) && $i !== $length) {
		echo '<div class="row">';
	} else {
				echo '<a href="'.$url.'"><div class="col-xs-4 recipe-profile">
						<div class="well well-sm" style="background-color: white">';
							echo "<center><h4>".$name."</h4></center>";
							echo "<center><img style='width:80%'; src='".$image."'/></center><br>";
							echo '<center>(<img src="../../resources/ratings/'.$rating.'star.png" style="width: 50%;">)</p></center>';
							echo "<p>$description</p>";
							echo "<p><strong>Tags: </strong>".$plate;
		echo '
						</div>
				</div>
			</a>';
			
	};
};

//getting stuff from the recipes found
/*$recipecount = 0;
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
};*/


?>