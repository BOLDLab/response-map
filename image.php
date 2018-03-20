<?php
$url = parse_url($_SERVER['HTTP_HOST']);

$f = $_GET['f'];

$path = explode('/',$f);

$fn = array_pop($path);

$f = (count($path) === 0) ? $fn : $f;

header('Access-Control-Allow-Origin: *');
//header("Content-Type: image/png");

//echo file_get_contents('files/'.$f);

$image = imagecreatefrompng($file);

header('Content-Type: ' . image_type_to_mime_type(IMAGETYPE_WBMP));
image2wbmp($image); // output the stream directly
imagedestroy($image);
?>
