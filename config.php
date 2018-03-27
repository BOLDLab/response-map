<?php
	$host = getenv('MYSQL_DB_HOST');
	$database_name = getenv('MYSQL_DB_NAME');
	$database_user = getenv('MYSQL_DB_USER');
	$database_pass = getenv('MYSQL_DB_PASSWORD');
	$adminpassword = getenv('DB_ADMIN_PASSWORD');

	// Establish a connection to the database
	$conn = mysqli_connect($host, $database_user, $database_pass, $database_name);

	// Google Maps API key
	$google_key = getenv('GOOGLE_API_KEY');

	$aws_access_key = getenv('AWS_ACCESS_KEY');
	$aws_secret_key = getenv('AWS_SECRET_KEY');
	$aws_s3_bucket = getenv('S3_BUCKET');

	$allowed_origins = ["https://edx.org", "https://studio.edx.org"];
?>
