<?php

/*
 * An object to represent the food_consolidated table in the ETL database.
 */

class FoodConsolidatedTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getEtlDb();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array(
            'index',
            'product_name',
            'product_name_clean',
            'manufacturer',
            'manufacturer_clean',
            'sugar_100',
            'fat_100',
            'salt_100',
            'fibre_100',
            'saturates_100',
            'cals_100',
            'sugar_serving',
            'fat_serving',
            'salt_serving',
            'fibre_serving',
            'saturates_serving',
            'cals_serving',
            'sugar_level',
            'salt_level',
            'saturates_level',
            'fat_level',
            'packcount',
            'packsize',
            'packsize_extract',
            'category',
            'main_category',
            'sub_category_1',
            'sub_category_2',
            'main_and_sub1',
            'main_category_name',
            'sub_category_name',
            'product_category_name',
            'PHE_cat',
            'badge_new',
            'high_five_man',
            'pgc_badge',
            'retailer_extract',
            'ingredients',
            'ingredients_clean',
        );
    }


    public function getFieldsThatHaveDefaults()
    {
        return array(
            'index',
            'sugar_100',
            'fat_100',
            'salt_100',
            'fibre_100',
            'saturates_100',
            'cals_100',
            'sugar_serving',
            'fat_serving',
            'salt_serving',
            'fibre_serving',
            'saturates_serving',
            'cals_serving',
            'packcount',
            'packsize',
            'packsize_extract',
            'category',
            'main_category',
            'sub_category_1',
            'sub_category_2',
            'main_and_sub1',
            'badge_new',
            'high_five_man',
            'pgc_badge',
        );
    }


    public function getObjectClassName()
    {
        return FoodConsolidatedItem::class;
    }


    public function getTableName() { return getenv('ETL_TABLE_NAME'); }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetches the food_consolidted items that correspond to the provided swaps
     * @param Swap $swaps
     * @return array - map of food barcodes to the FoodConsolidated objects.
     */
    public function fetchForSwaps(Swap ...$swaps) : array
    {
        foreach ($swaps as $swap)
        {
            $barcodes[] = $this->getDb()->escape_string($swap->getSwapBarcode());
        }

        $foodConsolidatedItems = $this->loadWhereAnd(array('barcode' => $barcodes));
        $result = array();

        foreach ($foodConsolidatedItems as $foodConsolidatedItem)
        {
            /* @var $foodConsolidatedItem FoodConsolidatedItem */
            $result[$foodConsolidatedItem->getBarcode()] = $foodConsolidatedItem;
        }

        return $result;
    }


    /**
     * Fetch a single product by its barcode
     * @param string $barcode
     * @return \FoodConsolidatedItem
     * @throws ExceptionProductNotFound - if the product with the provided barcode could not be found.
     */
    public function findByBarcode(string $barcode) : FoodConsolidatedItem
    {
        $products = $this->loadWhereAnd(['barcode' => $barcode]);

        if (count($products) !== 1)
        {
            throw new ExceptionProductNotFound();
        }

        return $products[0];
    }
}

