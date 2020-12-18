<?php


class FoodConsolidatedItem extends Programster\MysqlObjects\AbstractTableRowObject implements JsonSerializable
{
    protected $m_index;
    protected $m_barcode;
    protected $m_product_name;
    protected $m_product_name_clean;
    protected $m_manufacturer;
    protected $m_manufacturer_clean;
    protected $m_sugar_100;
    protected $m_fat_100;
    protected $m_salt_100;
    protected $m_fibre_100;
    protected $m_saturates_100;
    protected $m_cals_100;
    protected $m_sugar_serving;
    protected $m_fat_serving;
    protected $m_salt_serving;
    protected $m_fibre_serving;
    protected $m_saturates_serving;
    protected $m_cals_serving;
    protected $m_sugar_level;
    protected $m_salt_level;
    protected $m_saturates_level;
    protected $m_fat_level;
    protected $m_packcount;
    protected $m_packsize;
    protected $m_packsize_extract;
    protected $m_category;
    protected $m_main_category;
    protected $m_sub_category_1;
    protected $m_sub_category_2;
    protected $m_main_and_sub1;
    protected $m_main_category_name;
    protected $m_sub_category_name;
    protected $m_product_category_name;
    protected $m_PHE_cat;
    protected $m_badge_new;
    protected $m_high_five_man;
    protected $m_pgc_badge;
    protected $m_retailer_extract;
    protected $m_ingredients;
    protected $m_ingredients_clean;


    public function __construct(array $data, $fieldTypes = null)
    {
        $this->initializeFromArray($data, $fieldTypes);
    }


    protected function getAccessorFunctions(): array
    {
        return array(
            "index" => function() { return $this->m_index; },
            "barcode" => function() { return $this->m_barcode; },
            "product_name" => function() { return $this->m_product_name; },
            "product_name_clean" => function() { return $this->m_product_name_clean; },
            "manufacturer" => function() { return $this->m_manufacturer; },
            "manufacturer_clean" => function() { return $this->m_manufacturer_clean; },
            "sugar_100" => function() { return $this->m_sugar_100; },
            "fat_100" => function() { return $this->m_fat_100; },
            "salt_100" => function() { return $this->m_salt_100; },
            "fibre_100" => function() { return $this->m_fibre_100; },
            "saturates_100" => function() { return $this->m_saturates_100; },
            "cals_100" => function() { return $this->m_cals_100; },
            "sugar_serving" => function() { return $this->m_sugar_serving; },
            "fat_serving" => function() { return $this->m_fat_serving; },
            "salt_serving" => function() { return $this->m_salt_serving; },
            "fibre_serving" => function() { return $this->m_fibre_serving; },
            "saturates_serving" => function() { return $this->m_saturates_serving; },
            "cals_serving" => function() { return $this->m_cals_serving; },
            "sugar_level" => function() { return $this->m_sugar_level; },
            "salt_level" => function() { return $this->m_salt_level; },
            "saturates_level" => function() { return $this->m_saturates_level; },
            "fat_level" => function() { return $this->m_fat_level; },
            "packcount" => function() { return $this->m_packcount; },
            "packsize" => function() { return $this->m_packsize; },
            "packsize_extract" => function() { return $this->m_packsize_extract; },
            "category" => function() { return $this->m_category; },
            "main_category" => function() { return $this->m_main_category; },
            "sub_category_1" => function() { return $this->m_sub_category_1; },
            "sub_category_2" => function() { return $this->m_sub_category_2; },
            "main_and_sub1" => function() { return $this->m_main_and_sub1; },
            "main_category_name" => function() { return $this->m_main_category_name; },
            "sub_category_name" => function() { return $this->m_sub_category_name; },
            "product_category_name" => function() { return $this->m_product_category_name; },
            "PHE_cat" => function() { return $this->m_PHE_cat; },
            "badge_new" => function() { return $this->m_badge_new; },
            "high_five_man" => function() { return $this->m_high_five_man; },
            "pgc_badge" => function() { return $this->m_pgc_badge; },
            "retailer_extract" => function() { return $this->m_retailer_extract; },
            "ingredients" => function() { return $this->m_ingredients; },
            "ingredients_clean" => function() { return $this->m_ingredients_clean; },
        );
    }


    protected function getSetFunctions(): array
    {
        return array(
            "index" => function($x) { $this->m_index = $x; },
            "barcode" => function($x) { $this->m_barcode = $x; },
            "product_name" => function($x) { $this->m_product_name = $x; },
            "product_name_clean" => function($x) { $this->m_product_name_clean = $x; },
            "manufacturer" => function($x) { $this->m_manufacturer = $x; },
            "manufacturer_clean" => function($x) { $this->m_manufacturer_clean = $x; },
            "sugar_100" => function($x) { $this->m_sugar_100 = $x; },
            "fat_100" => function($x) { $this->m_fat_100 = $x; },
            "salt_100" => function($x) { $this->m_salt_100 = $x; },
            "fibre_100" => function($x) { $this->m_fibre_100 = $x; },
            "saturates_100" => function($x) { $this->m_saturates_100 = $x; },
            "cals_100" => function($x) { $this->m_cals_100 = $x; },
            "sugar_serving" => function($x) { $this->m_sugar_serving = $x; },
            "fat_serving" => function($x) { $this->m_fat_serving = $x; },
            "salt_serving" => function($x) { $this->m_salt_serving = $x; },
            "fibre_serving" => function($x) { $this->m_fibre_serving = $x; },
            "saturates_serving" => function($x) { $this->m_saturates_serving = $x; },
            "cals_serving" => function($x) { $this->m_cals_serving = $x; },
            "sugar_level" => function($x) { $this->m_sugar_level = $x; },
            "salt_level" => function($x) { $this->m_salt_level = $x; },
            "saturates_level" => function($x) { $this->m_saturates_level = $x; },
            "fat_level" => function($x) { $this->m_fat_level = $x; },
            "packcount" => function($x) { $this->m_packcount = $x; },
            "packsize" => function($x) { $this->m_packsize = $x; },
            "packsize_extract" => function($x) { $this->m_packsize_extract = $x; },
            "category" => function($x) { $this->m_category = $x; },
            "main_category" => function($x) { $this->m_main_category = $x; },
            "sub_category_1" => function($x) { $this->m_sub_category_1 = $x; },
            "sub_category_2" => function($x) { $this->m_sub_category_2 = $x; },
            "main_and_sub1" => function($x) { $this->m_main_and_sub1 = $x; },
            "main_category_name" => function($x) { $this->m_main_category_name = $x; },
            "sub_category_name" => function($x) { $this->m_sub_category_name = $x; },
            "product_category_name" => function($x) { $this->m_product_category_name = $x; },
            "PHE_cat" => function($x) { $this->m_PHE_cat = $x; },
            "badge_new" => function($x) { $this->m_badge_new = $x; },
            "high_five_man" => function($x) { $this->m_high_five_man = $x; },
            "pgc_badge" => function($x) { $this->m_pgc_badge = $x; },
            "retailer_extract" => function($x) { $this->m_retailer_extract = $x; },
            "ingredients" => function($x) { $this->m_ingredients = $x; },
            "ingredients_clean" => function($x) { $this->m_ingredients_clean = $x; },
        );
    }


    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return FoodConsolidatedTable::getInstance();
    }


    public function toArray()
    {
        return array(
            "barcode" => $this->m_barcode,
            "manufacturer" => $this->m_manufacturer,
            "name" => $this->m_name,
            "timestamp" => $this->m_timestamp,
            "category" => $this->m_category,
            "badge" => $this->m_badge,
            "packsize" => $this->m_packsize,
            "packunits" => $this->m_packunits,
            "100units" => $this->m_100units,
            "packcount" => $this->m_packcount,
            "lightsdesc" => $this->m_lightsdesc,
            "fat_100" => $this->m_fat_100,
            "fat_pack" => $this->m_fat_pack,
            "fat_serving" => $this->m_fat_serving,
            "fat_level" => $this->m_fat_level,
            "saturates_100" => $this->m_saturates_100,
            "saturates_pack" => $this->m_saturates_pack,
            "saturates_serving" => $this->m_saturates_serving,
            "saturates_level" => $this->m_saturates_level,
            "sugar_100" => $this->m_sugar_100,
            "sugar_pack" => $this->m_sugar_pack,
            "sugar_serving" => $this->m_sugar_serving,
            "sugar_level" => $this->m_sugar_level,
            "salt_100" => $this->m_salt_100,
            "salt_pack" => $this->m_salt_pack,
            "salt_serving" => $this->m_salt_serving,
            "salt_level" => $this->m_salt_level,
            "cals_100" => $this->m_cals_100,
            "cals_pack" => $this->m_cals_pack,
            "cals_serving" => $this->m_cals_serving,
            "kj_100" => $this->m_kj_100,
            "kj_pack" => $this->m_kj_pack,
            "kj_serving" => $this->m_kj_serving,
            "fibre_100" => $this->m_fibre_100,
            "fibre_pack" => $this->m_fibre_pack,
            "fibre_serving" => $this->m_fibre_serving,
            "ingredients" => $this->m_ingredients,
            "source" => $this->m_source,
        );
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getIndex() { return $this->m_index; }
    public function getBarcode() { return $this->m_barcode; }
    public function getProductName() { return $this->m_product_name; }
    public function getProductNameClean() { return $this->m_product_name_clean; }
    public function getManufacturer() { return $this->m_manufacturer; }
    public function getManufacturerClean() { return $this->m_manufacturer_clean; }
    public function getSugar100() { return $this->m_sugar_100; }
    public function getFat100() { return $this->m_fat_100; }
    public function getSalt100() { return $this->m_salt_100; }
    public function getFibre100() { return $this->m_fibre_100; }
    public function getSaturates100() { return $this->m_saturates_100; }
    public function getCals100() { return $this->m_cals_100; }
    public function getSugarServing() { return $this->m_sugar_serving; }
    public function getFatServing() { return $this->m_fat_serving; }
    public function getSaltServing() { return $this->m_salt_serving; }
    public function getFibreServing() { return $this->m_fibre_serving; }
    public function getSaturatesServing() { return $this->m_saturates_serving; }
    public function getCalsServing() { return $this->m_cals_serving; }
    public function getSugarLevel() { return $this->m_sugar_level; }
    public function getSaltLevel() { return $this->m_salt_level; }
    public function getSaturatesLevel() { return $this->m_saturates_level; }
    public function getFatLevel() { return $this->m_fat_level; }
    public function getPackcount() { return $this->m_packcount; }
    public function getPacksize() { return $this->m_packsize; }
    public function getPacksizeExtract() { return $this->m_packsize_extract; }
    public function getCategory() { return $this->m_category; }
    public function getMainCategory() { return $this->m_main_category; }
    public function getSubCategory_1() { return $this->m_sub_category_1; }
    public function getSubCategory_2() { return $this->m_sub_category_2; }
    public function getMainAndSub1() { return $this->m_main_and_sub1; }
    public function getMainCategoryName() { return $this->m_main_category_name; }
    public function getSubCategoryName() { return $this->m_sub_category_name; }
    public function getProductCategoryName() { return $this->m_product_category_name; }
    public function getPHECat() { return $this->m_PHE_cat; }
    public function getBadgeNew() { return $this->m_badge_new; }
    public function getHighFiveMan() { return $this->m_high_five_man; }
    public function getPgcBadge() { return $this->m_pgc_badge; }
    public function getRetailerExtract() { return $this->m_retailer_extract; }
    public function getIngredients() { return $this->m_ingredients; }
    public function getIngredients_clean() { return $this->m_ingredients_clean; }
}