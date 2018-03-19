<?php
	// Rename this file to config.php and enter in your MySQL database credentials

	// MySQL database credentials
	$host = 'iwqrvsv8e5fz4uni.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
	$database_name = 'ee4r54etwrtw7ilw';
	$database_user = 'dllphy9r18z7gdgl';
	$database_pass = 'qf2r9wfczyu2ezwy';
	$adminpassword = 'ctlBOLD#nhi';

	// Establish a connection to the database
	$conn = mysqli_connect($host, $database_user, $database_pass, $database_name);

	// Google Maps API key
	$google_key = 'AIzaSyCyyijfRSuYHQDxtZREVk20YImRrwwu_Is';
?>
