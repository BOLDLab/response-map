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
// P SIJPKES - added copy file to S3 bucket, allows Heroku to provision files dir from S3

$aws = new Aws($config);
$s3 = $aws->s3;
$bucket = $aws->s3->bucket(getenv('S3_BUCKET')) ? : die('No "S3_BUCKET" config var in found in env!');
$object = $bucket->putObject([
  'Key' => "images/testme.png",
  'Body' => fopen('testme.png', 'rb')
]);

/*$s3Client = new Aws\S3\S3Client($sharedConfig);

$cmd = $s3Client->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key'    => $keyname
]);*/
