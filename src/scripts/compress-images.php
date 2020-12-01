<?php

/*
 * Script to sync down all the images, compress them, and re-upload
 */


require_once(__DIR__ . '/../bootstrap.php');


function main()
{
    $dest =  __DIR__ . '/product-images';

    $requiredEnvironmentVariables = array(
        'S3_IMAGE_BUCKET_KEY',
        'S3_IMAGE_BUCKET_SECRET',
        'S3_IMAGE_BUCKET_NAME',
        'S3_IMAGE_BUCKET_PATH',
        'COMPRESSED_IMAGE_QUALITY',
    );

    foreach ($requiredEnvironmentVariables as $requiredVariable)
    {
        if (getenv($requiredVariable) === false)
        {
            throw new ExceptionMissingRequiredEnvironmentVariable($requiredVariable);
        }
    }

    $awsKey = getenv("S3_IMAGE_BUCKET_KEY");
    $awsSecret = getenv("S3_IMAGE_BUCKET_SECRET");
    $bucketName = getenv("S3_IMAGE_BUCKET_NAME");
    $bucketFolder = getenv("S3_IMAGE_BUCKET_PATH");

    if ($bucketFolder !== "" && \Programster\CoreLibs\StringLib::startsWith($bucketFolder, "/") === false)
    {
        $bucketFolder = "/{$bucketFolder}";
    }
    elseif ($bucketFolder === "/")
    {
        $bucketFolder = "";
    }

    $localFolder = "/root/product-images";
    Programster\CoreLibs\Filesystem::mkdir($localFolder);
    $fullBucketPath = "s3://{$bucketName}{$bucketFolder}";
    shell_exec("aws configure set aws_access_key_id {$awsKey}");
    shell_exec("aws configure set aws_secret_access_key {$awsSecret}");
    shell_exec("aws s3 sync {$fullBucketPath} {$localFolder} --delete");
    $productImageQualityLevel = getenv("COMPRESSED_IMAGE_QUALITY");

    // compress
    $files = Programster\CoreLibs\Filesystem::getDirContents($localFolder, true, false);

    foreach ($files as $filename)
    {
        $filenameParts = pathinfo($filename);
        $extension = strtolower($filenameParts['extension']);

        if
        (
               in_array($extension, ["jpg", "jpeg"])
            && \Programster\CoreLibs\StringLib::endsWith($filename, "-compressed.jpg") === false
        )
        {
            $filenameWithoutExtension = $filenameParts['filename'];
            $outputFilename = "{$filenameWithoutExtension}-compressed.jpg";
            $sourceImage = imagecreatefromjpeg("{$localFolder}/{$filename}");
            $compressedImage = imagejpeg($sourceImage,"{$localFolder}/{$outputFilename}", $productImageQualityLevel);
        }
    }

    // sync up
    shell_exec("aws s3 sync {$localFolder} {$fullBucketPath}");
    shell_exec("aws configure set aws_access_key_id xxx");
    shell_exec("aws configure set aws_secret_access_key xxx");
}


main();