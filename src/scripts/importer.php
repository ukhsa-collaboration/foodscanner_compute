<?php

require_once(__DIR__ . '/../bootstrap.php');



function main(array $data)
{
    $insertData = array();
    $objects = array();

    foreach ($data as $index => $row)
    {
        $productBarcode = $row['barcode'];
        $swaps = $row['swaps'];
        $rank = 0;

        // swaps are already in order according to all the top 3 and long_list swaps etc
        // so dont sort by "Rank", "cos_toal", or any other field.
        foreach ($swaps as $index => $swap)
        {
            $rank++;
            $swapBarcode = $swap['barcode'];

            $insertData[] = array(
                'barcode' => $productBarcode,
                'swap_barcode' => $swapBarcode,
                'rank' => $rank,
            );
        }
    }

    if (count($insertData) > 0)
    {
        // batch insert the data.
        $db = SiteSpecific::getSwapsCacheDb();

        $query = \Programster\MysqliLib\MysqliLib::generateBatchInsertQuery(
            $insertData,
            SWAPS_CACHE_BUFFER_TABLE_NAME,
            $db
        );

        $result = $db->query($query);

        if ($result === false)
        {
            print "Failed to insert swaps into the database";
            print $db->error . PHP_EOL;
            PRINT $query . PHP_EOL;
            die();
        }
    }
}

try
{
    $content = file_get_contents("php://stdin");
    $data = Safe\json_decode($content, true);
    main($data);
}
catch (JsonException | \Safe\Exceptions\JsonException $jsonException)
{
    $context = array(
        'content_to_decode' => $content,
        'message' => $jsonException->getMessage(),
        'line' => $jsonException->getLine(),
        'file' => $jsonException->getFile(),
        'trace' => $jsonException->getTraceAsString(),
    );

    SiteSpecific::getLogger()->error("There was an exception performing json_decode", $context);
}
catch (Exception $ex)
{
    $context = array(
        'message' => $ex->getMessage(),
        'line' => $ex->getLine(),
        'file' => $ex->getFile(),
        'trace' => $ex->getTraceAsString(),
    );

    SiteSpecific::getLogger()->error("Unexpected exception in importation script", $context);
}
