<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

if ($argc < 5) {
    echo "Usage: AWS_ACCESS_KEY_ID=x AWS_SECRET_ACCESS_KEY=x {$argv[0]} <region> <bucket> <key> <SaveAs>\n";
    exit(1);
}

$s3Client = new S3Client([
    'region' => $argv[1],
    'version' => '2006-03-01'
]);

/** @see https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#getobject */
$result = $s3Client->getObject([
    'Bucket' => $argv[2],
    'Key' => $argv[3],
    'SaveAs' => $argv[4],
]);

print($result);
