<?php

/*
 * Handle the output of the machine learning algorithms, and import the result into the database.
 */


require_once(__DIR__ . '/../bootstrap.php');


class Categorization
{
    public function __construct(array $arrayForm)
    {
        if (!isset($arrayForm['pvid']))
        {
            throw new Exception("Missing expected pvid");
        }

        if (!isset($arrayForm['category']))
        {
            throw new Exception("Missing expected category");
        }

        $this->m_barcode = $arrayForm['pvid'];
        $this->m_category = $arrayForm['category'];
    }


    # Accessors
    public function getBarcode() : string { return $this->m_barcode; }
    public function getCategory() : string { return $this->m_category; }
}


function main()
{
    try
    {
        SiteSpecific::getLogger()->info("Swaps compute engine - Starting importation of machine-learning categorization output.");
        $spacyFiles = Programster\CoreLibs\Filesystem::getDirContents(SPACY_OUTPUT_FOLDER);
        $sklearnFiles = Programster\CoreLibs\Filesystem::getDirContents(SKLEARN_OUTPUT_FOLDER);
        $spacyCategorizations = getCategorizations($spacyFiles);
        $sklearnCategorizations = getCategorizations($sklearnFiles);
        $barcodes = array(...array_keys($spacyCategorizations), ...array_keys($sklearnCategorizations));
        $uniqueBarcodes = array_keys(array_flip($barcodes));
        $inputData = array();

        foreach ($uniqueBarcodes as $barcode)
        {
            $arrayForm = ['barcode' => $barcode];

            if (array_key_exists($barcode, $spacyCategorizations))
            {
                $arrayForm['phe_ml_cat_1'] = $spacyCategorizations[$barcode];
            }
            else
            {
                $arrayForm['phe_ml_cat_1'] = null;
            }

            if (array_key_exists($barcode, $sklearnCategorizations))
            {
                $arrayForm['phe_ml_cat_2'] = $sklearnCategorizations[$barcode];
            }
            else
            {
                $arrayForm['phe_ml_cat_2'] = null;
            }

            $inputData[] = $arrayForm;
        }

        if (count($inputData) > 0)
        {
            $db = FoodMachineLearningCategorisationTable::getInstance()->getDb();

            # delete all existing records just before import to do a complete replacement.
            $categorizationsTable = FoodMachineLearningCategorisationTable::getInstance();
            SiteSpecific::getLogger()->info("Swaps compute engine - deleting everything from machine learning categorization table.");
            $categorizationsTable->deleteAll();

            SiteSpecific::getLogger()->info("Swaps compute engine - inserting " . count($inputData) . " rows into machine learning categorization table.");

            $insertQuery = Programster\MysqliLib\MysqliLib::generateBatchInsertQuery(
                $inputData,
                FoodMachineLearningCategorisationTable::getInstance()->getTableName(),
                $db
            );

            $insertResult = $db->query($insertQuery);

            if ($insertResult === false)
            {
                $context = ['query' => $insertQuery, 'error' => $db->error];
                SiteSpecific::getLogger()->error("Failed to insert machine learning categorizations", $context);
                throw new ExceptionQueryFailed($insertQuery, $db->error);
            }
            else
            {
                SiteSpecific::getLogger()->debug("Swaps compute engine - insertion of machine learning data succeeded");
            }
        }
        else
        {
            $msg = "Swaps compute engine - There is no machine learning categorization data to insert.";

            $context = array(
                'num_spacy_files' => count($spacyFiles),
                'num_sklearn_files' => count($sklearnFiles),
                'num_unique_barcodes' => count($uniqueBarcodes),
            );

            SiteSpecific::getLogger()->error($msg, $context);
        }

        SiteSpecific::getLogger()->info("Swaps compute engine - Finished importation of machine-learning categorization output.");
    }
    catch (Exception $ex)
    {
        $context = array(
            'exception_message' => $ex->getMessage(),
            'exception_line' => $ex->getLine(),
            'exception_file' => $ex->getFile(),
        );

        SiteSpecific::getLogger()->error("Swaps compute engine - Unexpected Exception in import-ml-output script", $context);
    }
}


function getCategorizations(array $filepaths)
{
    $categorizations = array();

    foreach ($filepaths as $outputFilepath)
    {
        try
        {
            $dataArray = Safe\json_decode(file_get_contents($outputFilepath), true);

            foreach ($dataArray as $row)
            {
                $categorization = new Categorization($row);
                $categorizations[$categorization->getBarcode()] = $categorization->getCategory();
            }
        }
        catch (Safe\Exceptions\JsonException $ex)
        {
            $context = array(
                'output_filepath' => $outputFilepath,
                'output_contents' => file_get_contents($outputFilepath),
            );

            $outputFilename = basename($outputFilepath);
            $inputFilepath = sys_get_temp_dir() . "/{$outputFilename}";

            if (Programster\CoreLibs\StringLib::contains($outputFilepath, "sklearn"))
            {
                $errorOutputFilepath = SKLEARN_ERROR_OUTPUT_FOLDER . "/{$outputFilename}";
            }
            else
            {
                $errorOutputFilepath = SPACY_ERROR_OUTPUT_FOLDER . "/{$outputFilename}";
            }


            if (file_exists($inputFilepath))
            {
                $context['input_config'] = file_get_contents($inputFilepath);
            }
            else
            {
                $context['input_config_error'] = 'Could not find the input config file.';
            }

            if (file_exists($errorOutputFilepath))
            {
                $context['output_error_file_contents'] = file_get_contents($errorOutputFilepath);
            }
            else
            {
                $context['output_error_file_contents'] = 'Error file does not exist.';
            }

            SiteSpecific::getLogger()->error("There was an error decoding a machine-learning output JSON file.", $context);

            // carry on with the loop as if nothing happened, might as well get the rest of the data.
        }
    }

    return $categorizations;
}

main();
