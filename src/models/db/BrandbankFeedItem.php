<?php

/*
 * A class to represent a row in the food_consolidated_extended table.
 */

class BrandbankFeedItem extends Programster\MysqlObjects\AbstractTableRowObject implements JsonSerializable
{
    private string $m_barcode;
    private int $m_pvid;
    private ?int $m_storageTypeId = null;
    private ?string $m_storageTypeString = null;
    private ?int $m_packTypeId = null;
    private ?string $m_packTypeString = null;
    private ?string $m_preparationInstructions = null;
    private int $m_updatedFromBbAt;


    public function __construct(array $data, $fieldTypes = null)
    {
        $this->initializeFromArray($data, $fieldTypes);
    }


    /**
     *
     * @param string $barcode
     * @param int $pvid
     * @param Type|null $storageType
     * @param Type|null $packType
     * @param string|null $preparationInstructions
     * @param int $updatedFromBbAt
     * @return \BrandbankFeedItem
     */
    public static function createNew(
        string $barcode,
        int $pvid,
        ?Type $storageType,
        ?Type $packType,
        ?string $preparationInstructions,
        int $updatedFromBbAt
    ) : BrandbankFeedItem
    {
        $arrayForm = array(
            'barcode' => $barcode,
            'pvid' => $pvid,
            'updated_from_bb_at' => $updatedFromBbAt,
        );


        if ($preparationInstructions !== null)
        {
            $arrayForm['preparation_instructions'] = $preparationInstructions;
        }

        if ($storageType !== null)
        {
            $arrayForm['storage_type_id'] = $storageType->getLookupId();
            $arrayForm['storage_type_string'] = $storageType->getLookupValue();
        }

        if ($packType !== null)
        {
            $arrayForm['pack_type_id'] = $packType->getLookupId();
            $arrayForm['pack_type_string'] = $packType->getLookupValue();
        }

        return new BrandbankFeedItem($arrayForm);
    }



    /**
     *
     * @param BrandbankProduct $product
     * @return \BrandbankFeedItem
     */
    public static function createFromBrandbankProduct(BrandbankProduct $product)
    {
        $arrayForm = array(
            'barcode' => $product->getgtin(),
            'pvid' => $product->getPvid(),
            'updated_from_bb_at' => time(),
        );

        if ($product->getPreparationAndUsage() !== null)
        {
            $arrayForm['preparation_instructions'] = $product->getPreparationAndUsage();
        }

        if ($product->getStorageType() !== null)
        {
            $arrayForm['storage_type_id'] = $product->getStorageType()->getNameId();
            $arrayForm['storage_type_string'] = $product->getStorageType()->getNameValue();
        }

        if ($product->getPackType() !== null)
        {
            $arrayForm['pack_type_id'] = $product->getPackType()->getNameId();
            $arrayForm['pack_type_string'] = $product->getPackType()->getNameValue();
        }

        $foodConsolidatedExtendedItem = new BrandbankFeedItem($arrayForm);
        return $foodConsolidatedExtendedItem;
    }


    protected function getAccessorFunctions(): array
    {
        return array(
            'barcode' => function() { return $this->m_barcode; },
            'pvid' => function() { return $this->m_pvid; },
            'storage_type_id' => function() { return $this->m_storageTypeId; },
            'storage_type_string' => function() { return $this->m_storageTypeString; },
            'pack_type_id' => function() { return $this->m_packTypeId; },
            'pack_type_string' => function() { return $this->m_packTypeString; },
            'preparation_instructions' => function() { return $this->m_preparationInstructions; },
            'updated_from_bb_at' => function() { return $this->m_updatedFromBbAt; },
        );
    }

    protected function getSetFunctions(): array
    {
        return array(
            'barcode' => function($x) { $this->m_barcode = $x; },
            'pvid' => function($x) { $this->m_pvid = $x; },
            'storage_type_id' => function($x) { $this->m_storageTypeId = $x; },
            'storage_type_string' => function($x) { $this->m_storageTypeString = $x; },
            'pack_type_id' => function($x) { $this->m_packTypeId = $x; },
            'pack_type_string' => function($x) { $this->m_packTypeString = $x; },
            'preparation_instructions' => function($x) { $this->m_preparationInstructions = $x; },
            'updated_from_bb_at' => function($x) { $this->m_updatedFromBbAt = $x; },
        );
    }


    public function toArray()
    {
        return array(
            'barcode' => $this->getBarcode(),
            'pvid' => $this->getPvid(),
            'storage_type_id' => $this->getStorageTypeId(),
            'storage_type_string' => $this->getStorageTypeString(),
            'pack_type_id' => $this->getPackTypeId(),
            'pack_type_string' => $this->getPackTypeString(),
            'preparation_instructions' => $this->getPreparationInstructions(),
            'updated_from_bb_at' => $this->getUpdatedFromBbAt(),
        );
    }



    public function getTableHandler(): \Programster\MysqlObjects\TableInterface
    {
        return BrandbankFeedTable::getInstance();
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


    # Accessors
    public function getBarcode() : string { return $this->m_barcode; }
    public function getPvid() : int { return $this->m_pvid; }
    public function getStorageTypeId() : ?int { return $this->m_storageTypeId; }
    public function getStorageTypeString() : ?string { return $this->m_storageTypeString; }
    public function getPackTypeId() : ?int { return $this->m_packTypeId; }
    public function getPackTypeString() : ?string { return $this->m_packTypeString; }
    public function getPreparationInstructions() : ?string { return $this->m_preparationInstructions; }
    public function getUpdatedFromBbAt() : int { return $this->m_updatedFromBbAt; }
}