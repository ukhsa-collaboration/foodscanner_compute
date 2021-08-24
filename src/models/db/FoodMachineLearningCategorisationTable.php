<?php

/*
 * An object to represent the food_consolidated table in the ETL database.
 */

declare(strict_types = 1);


class FoodMachineLearningCategorisationTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getSwapsCacheDb();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array(
            'phe_ml_cat_1',
            'phe_ml_cat_2',
        );
    }


    public function getFieldsThatHaveDefaults()
    {
        return array(
            'phe_ml_cat_1',
            'phe_ml_cat_2',
        );
    }


    public function getObjectClassName()
    {
        return FoodMachineLearningCategorisationRow::class;
    }


    public function getTableName() { return 'food_ml_categorisations'; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetch a single product by its barcode
     * @param string $barcode
     * @return \FoodMachineLearningCategorisationRow
     * @throws ExceptionProductNotFound - if the product with the provided barcode could not be found.
     */
    public function findByBarcode(string $barcode) : FoodMachineLearningCategorisationRow
    {
        $products = $this->loadWhereAnd(['barcode' => $barcode]);

        if (count($products) !== 1)
        {
            throw new ExceptionProductNotFound();
        }

        return $products[0];
    }
}

