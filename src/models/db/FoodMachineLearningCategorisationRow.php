<?php

/*
 * A class to represent a row in the food_consolidated_extended table.
 */

class FoodMachineLearningCategorisationRow extends Programster\MysqlObjects\AbstractTableRowObject implements JsonSerializable
{
    private string $m_barcode;
    private ?string $m_pheCategory1 = null;
    private ?string $m_pheCategory2 = null;


    public function __construct(array $data, $fieldTypes = null)
    {
        $this->initializeFromArray($data, $fieldTypes);
    }

    
    protected function getAccessorFunctions(): array
    {
        return array(
            'barcode' => function() { return $this->m_barcode; },
            'phe_category_1' => function() { return $this->m_pheCategory1; },
            'phe_category_2' => function() { return $this->m_pheCategory2; },
        );
    }


    protected function getSetFunctions(): array
    {
        return array(
            'barcode' => function($x) { $this->m_barcode = $x; },
            'phe_category_1' => function($x) { $this->m_pheCategory1 = $x; },
            'phe_category_2' => function($x) { $this->m_pheCategory2 = $x; },
        );
    }


    public function toArray()
    {
        return array(
            'barcode' => $this->getBarcode(),
            'phe_category_1' => $this->getPheCategory1(),
            'phe_category_2' => $this->getPheCategory2(),
        );
    }



    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return FoodMachineLearningCategorisationTable::getInstance();
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getBarcode() : string { return $this->m_barcode; }
    public function getPheCategory1() : ?string { return $this->m_pheCategory1; }
    public function getPheCategory2() : ?string { return $this->m_pheCategory2; }
}