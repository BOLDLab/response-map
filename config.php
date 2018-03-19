<?php
	// Rename this file to config.php and enter in your MySQL database credentials

	// MySQL database credentials
	$host = 'bfjrxdpxrza9qllq.cbetxkdyhwsb.us-east-1.rds.amazonaws.com';
	$database_name = 'e3qkwax2j2r8u5zc';
	$database_user = 'g7dk6fe86toueyoq';
	$database_pass = 'lfusckc1l521pmff';
	$adminpassword = 'ctlBOLD#nhi';

	// Establish a connection to the database
	$conn = mysqli_connect($host, $database_user, $database_pass, $database_name);

	// Google Maps API key
	$google_key = ' AIzaSyAjXp5e_Anijx03JpoxUji3mT0lqbl69nU';
?>
