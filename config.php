<?php
	// Rename this file to config.php and enter in your MySQL database credentials

	// MySQL database credentials
		$host = 'zwgaqwfn759tj79r.chr7pe7iynqr.eu-west-1.rds.amazonaws.com';
	$database_name = 'muvuw2rqcpm532tg';
	$database_user = 'lx0agcf2cs4eejzf';
	$database_pass = 'i89j1lrs12i76b87';
	$adminpassword = 'ctlBOLD#nhi';

	// Establish a connection to the database
	$conn = mysqli_connect($host, $database_user, $database_pass, $database_name);

	// Google Maps API key
	$google_key = 'AIzaSyCyyijfRSuYHQDxtZREVk20YImRrwwu_Is';

	$allowed_origins = ["https://edx.org", "https://studio.edx.org"];
?>
