<?php
$url = parse_url($_SERVER['HTTP_HOST']);

$f = $_GET['f'];

$path = explode('/',$f);

$fn = array_pop($path);
echo count($path).'<br>';

$f = (count($path === 0)) ? $fn : $f;

echo $f;
return;
header("content-type: image/png");

echo file_get_contents('files/'.$f);

?>
