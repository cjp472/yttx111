<?php
	// This script accepts an ID and looks in the user's session for stored thumbnail data.
	// It then streams the data to the browser as an image
	
	// Work around the Flash Player Cookie Bug
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}
	include_once ("../../common.php");;

	$image_id = isset($_GET["id"]) ? $_GET["id"] : false;

	if ($image_id === false) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "No ID";
		exit(0);
	}

	// Use a output buffering to load the image into a variable
	$imagevariable = file_get_contents(RESOURCE_PATH.$image_id);

	header("Content-type: image/jpeg") ;
	header("Content-Length: ".strlen($imagevariable));
	echo $imagevariable;
	exit(0);
?>