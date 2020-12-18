<?php

/*
 * Generates the commands to run the machine-learning "sklearn" routine for calculating a phe_category for the food items.
 */

require_once(__DIR__ . '/../bootstrap.php');


function main()
{
    SiteSpecific::getLogger()->info("Swaps compute engine - Starting generate-ml-commands");
    /* @var $extendedTable BrandbankFeedTable */
    $extendedTable = BrandbankFeedTable::getInstance();

    /* @var $foodConsolidatedTable FoodConsolidatedTable */
    $foodConsolidatedTable = FoodConsolidatedTable::getInstance();

    $feedItems = $extendedTable->fetchLatestBarcodeData();
    $consolidatedItems = $foodConsolidatedTable->loadAll();
    $productConfigs = array();
    $counter = 0;
    $filenames = array();

    foreach ($consolidatedItems as $consolidatedItem)
    {
        /* @var $consolidatedItem FoodConsolidatedItem */
        $barcode = $consolidatedItem->getBarcode();

        if (isset($feedItems[$barcode]))
        {
            $extendedItem = $feedItems[$barcode];

            $productConfigs[] = MachineLearningInputConfigProduct::createFromDatabaseObjects(
                $consolidatedItem,
                $extendedItem
            );

            $counter++;

            if (count($productConfigs) >= ML_NUM_PRODUCTS_PER_JSON_INPUT_CONFIG)
            {
                $newFilename = \Safe\tempnam(sys_get_temp_dir(), "ml-config-");
                \Safe\file_put_contents($newFilename, Safe\json_encode($productConfigs));
                $productConfigs = [];
                $filenames[] = $newFilename;
            }
        }
    }

    if (count($productConfigs) > 0)
    {
        $newFilename = \Safe\tempnam(sys_get_temp_dir(), "ml-config-");
        \Safe\file_put_contents($newFilename, Safe\json_encode($productConfigs));
        $productConfigs = [];
        $filenames[] = $newFilename;
    }

    if (getenv('ML_ALGORITHM') === false)
    {
        throw new ExceptionMissingRequiredEnvironmentVariable('ML_ALGORITHM');
    }

    $algorithm = getenv('ML_ALGORITHM');
    $spacyOutputFolder = SPACY_OUTPUT_FOLDER;
    $sklearnOutputFolder = SKLEARN_OUTPUT_FOLDER;

    // refresh the output folders in case there is remaining data from a previous run.
    Programster\CoreLibs\Filesystem::deleteDir($spacyOutputFolder);
    Programster\CoreLibs\Filesystem::deleteDir($sklearnOutputFolder);
    Programster\CoreLibs\Filesystem::mkdir($spacyOutputFolder);
    Programster\CoreLibs\Filesystem::mkdir($sklearnOutputFolder);


    foreach ($filenames as $filepath)
    {
        switch ($algorithm)
        {
            case 'spacy':
            {
                $touchCommand = 'touch ' . PROGRESS_FOLDER . '/' . \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS);
                $pythonCommand = "python3 /root/categorizer-module/spacy-getPredictions.py -p {$filepath}";
                $outputFilename = \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS) . ".json";
                $commands[] = "{$touchCommand} && {$pythonCommand} > {$spacyOutputFolder}/{$outputFilename} || true" . PHP_EOL;
            }
            break;

            case 'sklearn':
            {
                $touchCommand = 'touch ' . PROGRESS_FOLDER . '/' . \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS);
                $pythonCommand = "python3 /root/categorizer-module/sklearn-getPredictions.py -p {$filepath}";
                $outputFilename = \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS) . ".json";
                $commands[] = "{$touchCommand} && {$pythonCommand} > {$sklearnOutputFolder}/{$outputFilename} || true" . PHP_EOL;
            }
            break;

            case 'both':
            {
                $touchCommand = 'touch ' . PROGRESS_FOLDER . '/' . \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS);
                $pythonCommand = "python3 /root/categorizer-module/sklearn-getPredictions.py -p {$filepath}";
                $outputFilename = \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS) . ".json";
                $commands[] = "{$touchCommand} && {$pythonCommand} > {$sklearnOutputFolder}/{$outputFilename} || true" . PHP_EOL;

                $touchCommand = 'touch ' . PROGRESS_FOLDER . '/' . \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS);
                $outputFilename = \Programster\CoreLibs\StringLib::generateRandomString(36, \Programster\CoreLibs\StringLib::PASSWORD_DISABLE_SPECIAL_CHARS) . ".json";
                $pythonCommand = "python3 /root/categorizer-module/spacy-getPredictions.py -p {$filepath}";
                $commands[] = "{$touchCommand} && {$pythonCommand} > {$spacyOutputFolder}/{$outputFilename} || true" . PHP_EOL;
            }
            break;

            default:
            {
                throw new Exception("Unrecognized value for ML_ALGORITHM: '{$algorithm}'");
            }
        }
    }

    Programster\CoreLibs\Filesystem::deleteDir(PROGRESS_FOLDER);
    Programster\CoreLibs\Filesystem::mkdir(PROGRESS_FOLDER);
    file_put_contents(__DIR__ . '/commands.sh', $commands);
    SiteSpecific::getLogger()->info("Swaps compute engine - Finished generating machine-learning commands file");
}

main();


