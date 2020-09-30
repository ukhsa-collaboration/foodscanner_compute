<?php

class SiteSpecific
{
    /**
     * Get the mysqli connection to our swaps database.
     * @return \mysqli
     */
    public static function getSwapsCacheDb() : mysqli
    {
        $db = null;

        if ($db === null)
        {
            $db = new mysqli(
                getenv('SWAPS_CACHE_DB_HOST'),
                getenv('SWAPS_CACHE_DB_USER'),
                getenv('SWAPS_CACHE_DB_PASSWORD'),
                getenv('SWAPS_CACHE_DB_NAME'),
                getenv('SWAPS_CACHE_DB_PORT')
            );
        }

        return $db;
    }


    /**
     * Get the mysqli connection to the ETL database.
     * @return \mysqli
     */
    public static function getEtlDb() : mysqli
    {
        $db = null;


        if ($db === null)
        {
            $db = new mysqli(
                getenv('ETL_DB_HOST'),
                getenv('ETL_DB_USER'),
                getenv('ETL_DB_PASSWORD'),
                getenv('ETL_DB_NAME'),
                getenv('ETL_DB_PORT')
            );
        }

        return $db;
    }


    /**
     * Get a logger for reporting issues.
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger() : \Psr\Log\LoggerInterface
    {
        static $logger = null;

        if ($logger === null)
        {
            $logger = new Programster\Log\MysqliLogger(SiteSpecific::getSwapsCacheDb(), "logs");
        }

        return $logger;
    }
}
