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

        foreach ($swaps as $swap)
        {
            $swapBarcode = $swap['barcode'];
            $newRank = $swap['Rank'];

            $objects[] = array(
                'barcode' => $productBarcode,
                'swap_barcode' => $swapBarcode,
                'rank' => $newRank,
            );
        }
    }

    if (count($objects) > 0)
    {
        // sort the objects by "rank" (which is actually a similarity rating, not a ranking of the first 30).
        $sorter = function($a, $b) {
            $result = $a['barcode'] <=> $b['barcode'];

            if ($result === 0)
            {
                $result = $a['rank'] <=> $b['rank'];
            }

            return $result;
        };

        usort($objects, $sorter);

        // now re-rank so we get numbers 1-30
        $lastBarcode = "";
        $newRank = 1;

        foreach ($objects as $index => $object)
        {
            if ($object['barcode'] !== $lastBarcode)
            {
                $newRank = 1;
            }

            $originalRank = $objects[$index]['rank'];
            //$objects[$index]['original_rank'] = $originalRank;
            $objects[$index]['rank'] = $newRank;

            $newRank++;
            $lastBarcode = $object['barcode'];
        }

        // batch insert the data.
        $db = SiteSpecific::getSwapsCacheDb();
        $query = \Programster\MysqliLib\MysqliLib::generateBatchInsertQuery($objects, SWAPS_CACHE_BUFFER_TABLE_NAME, $db);
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
