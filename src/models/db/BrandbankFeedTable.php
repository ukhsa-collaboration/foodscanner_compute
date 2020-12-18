<?php

/*
 * An object to represent the food_consolidated table in the ETL database.
 */

declare(strict_types = 1);


class BrandbankFeedTable extends Programster\MysqlObjects\AbstractTable
{
    public function getDb(): \mysqli
    {
        return SiteSpecific::getSwapsCacheDb();
    }


    public function getFieldsThatAllowNull(): array
    {
        return array(
            'preparation_instructions',
            'pack_type_string',
            'pack_type_id',
            'storage_type_string',
            'storage_type_id',
        );
    }


    public function getFieldsThatHaveDefaults()
    {
        return array(
            'preparation_instructions',
            'pack_type_string',
            'pack_type_id',
            'storage_type_string',
            'storage_type_id',
        );
    }


    public function getObjectClassName()
    {
        return BrandbankFeedItem::class;
    }


    public function getTableName() { return 'brandbank_feed'; }


    public function validateInputs(array $data): array
    {
        return $data;
    }


    /**
     * Fetch a single product by its barcode
     * @param string $pvid
     * @return \FoodConsolidatedItem
     * @throws ExceptionProductNotFound - if the product with the provided barcode could not be found.
     */
    public function findByPvid(string $pvid) : BrandbankFeedItem
    {
        $products = $this->loadWhereAnd(['pvid' => $pvid]);

        if (count($products) !== 1)
        {
            throw new ExceptionProductNotFound();
        }

        return $products[0];
    }


    /**
     * Fetches only the latest data on a per-barcode basis. E.g. if a barcode is in there twice, it will only
     * return the row with the highest pvid, which will be the last update recieved.
     * @param bool $inFoodConsolidatedTable - whether to only fetch rows that have barcodes in the food_consolidated
     * table as well.
     * @return array - an array of FoodConsolidatedExtended items mapped to by their barcodes.
     */
    public function fetchLatestBarcodeData() : array
    {
        $db = $this->getDb();
        $result = $db->query("SELECT * FROM `{$this->getTableName()}` ORDER BY `pvid` ASC");

        if ($result === false)
        {
            $context = array('query' => $query, 'error' => $db->error);
            SiteSpecific::getLogger()->error(__CLASS__ . " failed to run fetchLatestBarcodeData()", $context);
            throw new Exception("Failed to run fetchLatestBarcodeData");
        }

        $items = $this->convertMysqliResultToObjects($result);
        $mappedItems = array();

        // loop through overwriting by barcode, that way if there is a later pvid, that one replaces old one.
        // this is why we sorted by pvid ASC.
        foreach ($items as $item)
        {
            /* @var $item BrandbankFeedItem */
            $mappedItems[$item->getBarcode()] = $item;
        }

        return $mappedItems;
    }



    /**
     *
     * WARNING - this actually deletes any pre-existing pvids before inserting for speed (2 queries instead of hundreds
     * of updates).
     * If we start using FKs in future, this will likely neeed changing. This also prevents any issues arising from
     * a barcode not lining up with a pvid.
     * @param BrandbankFeedItem $items
     */
    public function batchInsert(BrandbankFeedItem ...$items) : void
    {
        $pvidMap = array();
        $barcodeMap = array();
        $insertData = array();

        foreach ($items as $item)
        {
            $pvidMap[$item->getPvid()] = $item;
            $barcodeMap[$item->getBarcode()] = $item;

            // using pvid as index prevents dupes. BB API is very dumb and will send dupes if you send ID in a "resend"
            // request
            $insertData[$item->getPvid()] = $item->toArray();
        }

        $pvids = array_keys($pvidMap);
        $barcodes = array_keys($barcodeMap);

        $wherePairs = [
            'pvid' => $pvids
        ];

        $this->deleteWhereOr($wherePairs);

        $insertQuery = \Programster\MysqliLib\MysqliLib::generateBatchInsertQuery(
            array_values($insertData),
            $this->getTableName(),
            $this->getDb()
        );

        //$transaction = new
        $transaction = new \iRAP\MultiQuery\Transaction($this->getDb(), [$insertQuery], 3);
        $result = $transaction->wasSuccessful();

        if ($transaction->wasSuccessful() === false)
        {
            $context = ['query' => $insertQuery, 'error' => $this->getDb()->error];
            SiteSpecific::getLogger()->error("Failed to batch insert food consolidated extended items", $context);
            throw new Exception("Failed to batch insert food consolidated extended items into database.");
        }
    }


    /**
     * Fetch the lowest pvid in the database.
     * @return int
     * @throws Exception
     */
    public function getLowestPvid() : int
    {
        $query = "SELECT `pvid` FROM `{$this->getTableName()} ORDER BY `pvid` ASC limit 1";
        $result = $this->getDb()->query($query);

        if ($result === false)
        {
            throw new Exception("Failed to select lowest pvid");
        }

        /* @var $result mysqli_result */
        if ($result->num_rows !== 1)
        {
            throw new Exception("There are no PVIDs in the system.");
        }

        $row = $result->fetch_assoc();
        return $row['pvid'];
    }


    /**
     * Fetches the PVIDs that we are missing in gaps.
     * @return array
     * @throws Exception
     */
    public function getPvidHoles() : array
    {
        $query =
            "SELECT DISTINCT (`pvid`) FROM (
                SELECT `pvid` FROM `{$this->getTableName()}`
                UNION
                SELECT `pvid` FROM  `empty_pvids`
            ) AS inputTable
            ORDER BY `pvid` ASC";

        $result = $this->getDb()->query($query);

        if ($result === false)
        {
            throw new Exception("Failed to select pvids from database.");
        }

        /* @var $result mysqli_result */
        $missingPvids = array();
        $existingPvids = array();

        while (($row = $result->fetch_assoc()) !== null)
        {
            $existingPvids[] = $row['pvid'];
        }

        $lowest = $existingPvids[0];
        $highest = $existingPvids[count($existingPvids) - 1];
        $allPvids = range($lowest, $highest);
        $missingPvids = array_diff($allPvids, $existingPvids);
        return $missingPvids;
    }


    /**
     * Fetches FoodConsolidatedExtendedItem objects that were last updated over the age of BB_MAX_AGE_BEFORE_RECHECK
     * @return array - the products.
     */
    public function getOldBrandbankProducts() : array
    {
        $minTime = time() - BB_MAX_AGE_BEFORE_RECHECK;
        $whereClause = "`updated_from_bb_at` < {$minTime}";
        return $this->loadWhereExplicit($whereClause);
    }


    /**
     * Fetch the PVIDs that do not return any data from brandbank.
     * @return array - a collection of pvids that are empty.
     */
    public function getEmptyPvids() : array
    {
        $pvids = array();
        $query = "SELECT * FROM `empty_pvids`";
        $db = $this->getDb();
        $result = $db->query($query);

        if ($result === false)
        {
            SiteSpecific::getLogger()->error("Failedt to insert empty pvids", ['error' => $db->error]);
            throw new Exception("Failed to insert empty pvids.");
        }

        /* @var $result mysqli_result */
        while (($row = $result->fetch_assoc()) !== null)
        {
            $pvids[] = $row['pvid'];
        }

        return $pvids;
    }


    /**
     * Insert pvids into the empty_pvids table, marking them as null in brandbank (e.g. there is no data against them
     * so don't keep trying to fetch against them).
     * @param int $pvids - any number of pvids to mark as null/empty.
     * @throws Exception
     */
    public function insertEmptyPvids(int ... $pvids)
    {
        if (count($pvids) > 0)
        {
            $query = "INSERT INTO `empty_pvids` (`pvid`) VALUES(" . implode(", ", $pvids) . ") ON DUPLICATE KEY IGNORE";
            $db = $this->getDb();
            $result = $db->query($query);

            if ($result === false)
            {
                SiteSpecific::getLogger()->error("Failedt to insert empty pvids", ['error' => $db->error, 'query' => $query]);
                throw new Exception("Failed to insert empty pvids.");
            }
        }
        else
        {
            // do nothing, nothing to insert.
        }
    }
}

