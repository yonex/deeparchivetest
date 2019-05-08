<?php
require 'vendor/autoload.php';

use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;

if ($argc < 2) {
    echo "Usage: AWS_ACCESS_KEY_ID=x AWS_SECRET_ACCESS_KEY=x {$argv[0]} filename\n";
    exit(1);
}

$s3Client = new S3Client([
    'region' => 'ap-northeast-1',
    'version' => '2006-03-01'
]);

$vault = 'yonexvault';
$filename = $argv[1];

$source = fopen($filename, 'rb');

$uploader = new MultipartUploader(
    $s3Client,
    [
        'before_initiate' => function (\Aws\Command $command) {
            // $command is a CreateMultipartUpload operation
            $command['StorageClass'] = 'DEEP_ARCHIVE';
        },
        'before_upload' => function (\Aws\Command $command) {
            gc_collect_cycles();
        },
        'bucket' => $vault,
        'key' => $filename,
    ]
);

do {
    try {
        $result = $uploader->upload();
        if ($result["@metadata"]["statusCode"] == '200') {
            print('<p>File successfully uploaded to ' . $result["ObjectURL"] . '.</p>');
        }
        print($result);
    } catch (MultipartUploadException $e) {
        rewind($source);
        $uploader = new MultipartUploader($s3Client, $source, [
            'state' => $e->getState(),
        ]);
    }
} while (!isset($result));

var_dump($result);
