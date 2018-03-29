<?php
require('../vendor/autoload.php');
//use Aws\S3\S3Client;
use Aws\Resource\Aws;

$config = [
    'region' => getenv('AWS_REGION'),
    'version' => '2006-03-01',
    'credentials' => [
      'key' => getenv('AWS_ACCESS_KEY'),
      'secret' => getenv('AWS_SECRET_KEY')
    ]
];
$file_path = "files/testme2.png";
// P SIJPKES - added copy file to S3 bucket, allows Heroku to provision files dir from S3
$arr = explode('/', $file_path);
$name = end($arr);
$l = count($arr);
$p = array_slice($arr, 0, $l-1);

$thumb_path = implode('/', $p);
$thumb_path .= "/thumbnails/$name";
$aws = new Aws($config);
$s3 = $aws->s3;
$bucket = $aws->s3->bucket(getenv('S3_BUCKET')) ? : die('No "S3_BUCKET" config var in found in env!');
$object = $bucket->putObject([
  'Key' => "images/$name",
  'Body' => fopen($file_path, 'rb')
]);
$object_thumb = $bucket->putObject([
  'Key' => "images/thumbnails/$name",
  'Body' => fopen($thumb_path, 'rb')
]);

$meta = $object->getData()['@metadata']['effectiveUri'];
$meta2 = $object_thumb->getData()['@metadata']['effectiveUri'];

echo $meta;
echo $meta2;

//var_dump($meta);
/*$s3Client = new Aws\S3\S3Client($sharedConfig);

$cmd = $s3Client->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key'    => $keyname
]);*/
