<?php
$url = parse_url($_SERVER['HTTP_HOST']);

$f = $_GET['f'];

$filename = explode('/',$f);

$filename = array_pop($filename);

echo $filename;
//header("content-type: image/png");
?>
