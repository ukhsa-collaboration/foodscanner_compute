<?php

/*
 * Generates the commands to run the machine-learning "spacey" routine for calculating a phe_category for the food items.
 */

require_once(__DIR__ . '/../bootstrap.php');



class MachineLearningInputConfigProduct implements JsonSerializable
{
    private string $m_pvid;
    private string $m_regulatedProductName;
    private string $m_mainCategoryName;
    private string $m_subCategoryName;
    private string $m_storageTypeString;
    private string $m_packTypeString;
    private array $m_ingredients;
    private string $m_cookingGuidelines;


    /**
     *
     * @param string $pvid - the pvid of the item - this comes out again, so would be good if we could "convert" to barcode.
     * @param string $regulatedProductName
     * @param string $mainCategoryName - the etl.main_category_name
     * @param string $subCategoryName - the etl.sub_category_name
     * @param string $storageTypeString - e.g. "Chilled"
     * @param string $packTypeString - e.g. "Tub"
     * @param array $ingredients - array of strings of ingredients.
     * @return array
     */
    public function __construct(
        string $pvid,
        string $regulatedProductName,
        string $mainCategoryName,
        string $subCategoryName,
        ?string $storageTypeString,
        ?string $packTypeString,
        ?string $cookingGuidelines,
        array $ingredients
    )
    {
        $this->m_pvid = $pvid;
        $this->m_regulatedProductName = $regulatedProductName;
        $this->m_mainCategoryName = $mainCategoryName;
        $this->m_subCategoryName = $subCategoryName;
        $this->m_storageTypeString = $storageTypeString ?? ""; // if null, the input json needs to be empty string
        $this->m_packTypeString = $packTypeString ?? ""; // if null, the input json needs to be empty string
        $this->m_cookingGuidelines = $cookingGuidelines ?? "";
        $this->m_ingredients = $ingredients;
    }


    public function jsonSerialize()
    {
        $categories = array(
            array('description' => $this->m_mainCategoryName),
            array('description' => $this->m_subCategoryName),
        );

        $ingredients = array(
            "Lemon Juice",
            "Grilled Red Peppers (5%)"
        );

        $attributes = array(
            'storageType' => array(array('lookupValue' => $this->m_storageTypeString)),
            'packType'    => array(array('lookupValue' => $this->m_packTypeString)),
            'ingredients' => $this->m_ingredients,
            'regulatedProductName' => $this->m_regulatedProductName,
            'cookingGuidelines' => $this->m_cookingGuidelines,
        );

        $languages = array(
            array(
                'groupingSets' => array(
                    array('attributes' => $attributes)
                )
            )
        );

        $arrayForm = array(
            'pvid' => $this->m_pvid,
            'categories' => $categories,
            'languages' => $languages
        );

        return $arrayForm;
    }


    /**
     * Creates a MachineLearningInputConfigProduct from the relevant rows in the food consolidated tables.
     * @param FoodConsolidatedItem $foodItem - a row from the food_consolidated table
     * @param BrandbankFeedItem $extendedItem - the corresponding (same barcode) row from the food_consolidated_extended table.
     * @return \MachineLearningInputConfigProduct
     * @throws Exception
     */
    public static function createFromDatabaseObjects(
        FoodConsolidatedItem $foodItem,
        BrandbankFeedItem $extendedItem
    ) : MachineLearningInputConfigProduct
    {
        if ($foodItem->getBarcode() !== $extendedItem->getBarcode())
        {
            throw new Exception(__CLASS__ . " createFromDatabaseObjects() expects food items to have matching barcodes.");
        }

        if (empty($foodItem->getIngredients()))
        {
            $ingredientsArray = [];
        }
        else
        {
            $ingredientsArray = explode("|", $foodItem->getIngredients());
        }

        $cookingGuidelinesJson = $extendedItem->getPreparationInstructions();

        if (!empty($cookingGuidelinesJson))
        {
            $cookingGuidelinesArray = json_decode($cookingGuidelinesJson, true);
            $cookingMethods = [];

            foreach ($cookingGuidelinesArray as $cookingInstruction)
            {
                $method = $cookingInstruction['method'];
                $cookingMethods[$method] = 1;
            }

            $cookingGuidelinesString = implode(" | ", array_keys($cookingMethods));
        }
        else
        {
            $cookingGuidelinesString = null;
        }

        return new MachineLearningInputConfigProduct(
            $foodItem->getBarcode(),
            $foodItem->getProductName(),
            $foodItem->getMainCategoryName(),
            $foodItem->getSubCategoryName(),
            $extendedItem->getStorageTypeString(),
            $extendedItem->getPackTypeString(),
            $cookingGuidelinesString,
            $ingredientsArray
        );
    }
}

