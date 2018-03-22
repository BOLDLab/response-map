<?php
	// Rename this file to config.php and enter in your MySQL database credentials

	// MySQL database credentials
		$host = 'bold.newcastle.edu.au';
	$database_name = 'edx-heroku-hello-map';
	$database_user = 'edx-heroku';
	$database_pass = 'AXvsy5E7TcMy11XR';
	$adminpassword = 'ctlBOLD#nhi';

	// Establish a connection to the database
	$conn = mysqli_connect($host, $database_user, $database_pass, $database_name);

	// Google Maps API key
	$google_key = 'AIzaSyCyyijfRSuYHQDxtZREVk20YImRrwwu_Is';

	$allowed_origins = ["https://edx.org", "https://studio.edx.org"];
?>
