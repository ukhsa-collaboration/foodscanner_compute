<?php

/*
 * This simple script reads the food table to find the barcodes of all the products there are, and generates
 * a commands file full of commands to run to calculate the swaps for all those products.
 */

require_once(__DIR__ . '/../bootstrap.php');





function main()
{
    SiteSpecific::getLogger()->info("Swaps compute engine - generating commands for calculating swaps.");
    $tableName = getenv('ETL_TABLE_NAME');

    $swapsDb = SiteSpecific::getSwapsCacheDb();
    $result = $swapsDb->query("TRUNCATE `" . SWAPS_CACHE_BUFFER_TABLE_NAME . "`");

    if ($result === false)
    {
        die("Failed to truncate the swaps buffer table");
    }

    $etlDb = SiteSpecific::getEtlDb();

    # Have to use distinct, because for some reason sometimes the same barcode is in twice.
    $result = $etlDb->query("SELECT DISTINCT(`barcode`) FROM `{$tableName}`");

    if ($result === false)
    {
        throw new Exception("Failed to select barcodes from {$tableName} table.");
    }

    /* @var $result mysqli_result */
    $barcodes = array();

    while (($row = $result->fetch_assoc()) !== null)
    {
        $barcodes[] = $row['barcode'];
    }

    $commands = [];
    $sets = array_chunk($barcodes, NUM_BARCODES_PER_PYTHON_PROCESS);

    foreach ($sets as $set)
    {
        $wrappedSet = Programster\CoreLibs\ArrayLib::wrapElements($set, '"');
        $touchCommand = 'touch ' . PROGRESS_FOLDER . '/' . \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS);
        $pythonCommand = 'python3 script.py ' . implode(" ", $wrappedSet);
        $commands[] = "{$touchCommand} && {$pythonCommand} | php importer.php || true" . PHP_EOL;
    }

    Programster\CoreLibs\Filesystem::deleteDir(PROGRESS_FOLDER);
    Programster\CoreLibs\Filesystem::mkdir(PROGRESS_FOLDER);
    file_put_contents(__DIR__ . '/commands.sh', $commands);
    SiteSpecific::getLogger()->info("Swaps compute engine - finished generating commands for calculating swaps.");
}

try
{
    main();
}
catch (Exception $ex)
{
    $context = array(
        'message' => $ex->getMessage(),
        'line' => $ex->getLine(),
        'file' => $ex->getFile(),
        'trace' => $ex->getTraceAsString(),
    );

    SiteSpecific::getLogger()->error("Unexpected exception in generate-commands script", $context);
}

