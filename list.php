<?php
require 'vendor/autoload.php';

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

if ($argc < 4) {
    echo "Usage: AWS_ACCESS_KEY_ID=x AWS_SECRET_ACCESS_KEY=x {$argv[0]} <region> <bucket> <prefix>\n";
    exit(1);
}

$s3Client = new S3Client([
    'region' => $argv[1],
    'version' => '2006-03-01'
]);

$bucket = $argv[2];
$prefix = $argv[3];

// Use the high-level iterators (returns ALL of your objects).
try {
    $results = $s3Client->getPaginator('ListObjects', [
        'Bucket' => $bucket,
        'Prefix' => $prefix,
    ]);

    foreach ($results as $result) {
        foreach ($result['Contents'] as $object) {
            echo $object['Key'] . PHP_EOL;
        }
    }
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

// Use the plain API (returns ONLY up to 1000 of your objects).
//try {
//    $objects = $s3Client->listObjects([
//        'Bucket' => $bucket,
//        'Prefix' => $prefix,
//    ]);
//    foreach ($objects['Contents'] as $object) {
//        echo $object['Key'] . PHP_EOL;
//    }
//} catch (S3Exception $e) {
//    echo $e->getMessage() . PHP_EOL;
//}
