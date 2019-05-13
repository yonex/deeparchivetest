<?php
require 'vendor/autoload.php';

use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;

if ($argc < 4) {
    echo "Usage: AWS_ACCESS_KEY_ID=x AWS_SECRET_ACCESS_KEY=x {$argv[0]} <region> <bucket> <file>\n";
    exit(1);
}

$s3Client = new S3Client([
    'region' => $argv[1],
    'version' => '2006-03-01'
]);

$bucket = $argv[2];
$filename = $argv[3];

$source = fopen($filename, 'rb');

$uploader = new MultipartUploader(
    $s3Client,
    $source,
    [
        'before_initiate' => function (\Aws\Command $command) {
            // $command is a CreateMultipartUpload operation
            $command['StorageClass'] = 'DEEP_ARCHIVE';
        },
        'before_upload' => function (\Aws\Command $command) {
            gc_collect_cycles();
        },
        'bucket' => $bucket,
        'key' => $filename,
    ]
);

do {
    try {
        $result = $uploader->upload();
        if ($result["@metadata"]["statusCode"] == '200') {
            echo 'File successfully uploaded to ' . $result["ObjectURL"] . PHP_EOL;
        }
        print($result);
    } catch (MultipartUploadException $e) {
        echo 'E: ' . $e->getMessage() . PHP_EOL;
        rewind($source);
        $uploader = new MultipartUploader($s3Client, $source, [
            'state' => $e->getState(),
        ]);
    }
} while (!isset($result));
