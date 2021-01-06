<?php

/*
 * This script simply creates a JSON file of the data the python-swaps-module uses in order to calculate swaps.
 */

require_once(__DIR__ . '/../bootstrap.php');


function main(string $filepath)
{
    SiteSpecific::getLogger()->info("Swaps compute engine - Generating food consolidated JSON file for python swaps module.");
    $foodConsolidatedDb = SiteSpecific::getEtlDb();
    $foodConsolidatedTableResult = $foodConsolidatedDb->query("SELECT * FROM `food_consolidated`");
    #Programster\MysqliLib\MysqliLib::convertResultToJsonFile($result, __DIR__ . '/food_consolidated.json');

    $swapsDb = SiteSpecific::getSwapsCacheDb();
    $result2 = $swapsDb->query("SELECT * FROM `food_ml_categorisations`");

    while (($row = $result2->fetch_assoc()) !== null)
    {
        $map[$row['barcode']] = $row;
    }

    $fileHandle = fopen($filepath, 'w');
    $firstRow = true;
    fwrite($fileHandle, "[");


    if (getenv('ML_ALGORITHM') === false)
    {
        throw new ExceptionMissingRequiredEnvironmentVariable('ML_ALGORITHM');
    }

    $algorithm = getenv('ML_ALGORITHM');

    switch ($algorithm)
    {
        case 'both':
        case 'spacy':
        {
            $mlColumn = 'phe_ml_cat_1';
        }
        break;

        case 'sklearn':
        {
            $mlColumn = 'phe_ml_cat_2';
        }
        break;

        default:
        {
            throw new Exception("Unrecognized value for ML_ALGORITHM: '{$algorithm}'");
        }
    }

    while (($foodConsolidatedRow = $foodConsolidatedTableResult->fetch_assoc()) !== null)
    {
        $barcode = $foodConsolidatedRow['barcode'];

        if (OVERRIDE_PHE_CAT_COLUMN)
        {
            if (isset($map[$barcode]))
            {
                $foodConsolidatedRow['PHE_cat'] = $map[$barcode][$mlColumn];
            }
            else
            {
                if (ONLY_USE_ML_CAT_IF_OVERRIDING_PHE_CAT)
                {
                    $foodConsolidatedRow['PHE_cat'] = null;
                }
                else
                {
                    // don't do anything, we don't have a value because product is not in brandbank data, but
                    // we may have categorization data from elsewhere so leave alone.
                }
            }
        }
        else
        {
            if (isset($map[$barcode]))
            {
                $foodConsolidatedRow['phe_ml_cat'] = $map[$barcode][$mlColumn];
            }
            else
            {
                $foodConsolidatedRow['phe_ml_cat'] = null;
            }
        }


        $jsonForm = json_encode($foodConsolidatedRow, JSON_PRESERVE_ZERO_FRACTION); // using JSON_NUMERIC_CHECK will also try and change the barcode.

        if ($jsonForm === FALSE)
        {
            $msg = "Failed convert row to json. " .
                   "Perhaps you need to set the MySQL connection charset to UTF8?";
            throw new \Exception($msg);
        }

        if ($firstRow)
        {
            $firstRow = false;
            fwrite($fileHandle, PHP_EOL);
        }
        else
        {
            fwrite($fileHandle, "," . PHP_EOL);
        }

        fwrite($fileHandle, $jsonForm);
    }

    fwrite($fileHandle, PHP_EOL . "]"); // end the JSON array list.
    fclose($fileHandle);

    SiteSpecific::getLogger()->info("Swaps compute engine - Finished generating food consolidated JSON file for python swaps module.");
}


if (!isset($argv[1]))
{
    SiteSpecific::getLogger()->error("Swaps compute engine - generate-food-consolidated-json-file.php requires you to pass the filepath.");
    die("generate-food-consolidated-json-file.php requires you to pass the filepath.");
}

main($argv[1]);

