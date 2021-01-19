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

            if ($db->connect_error)
            {
                SiteSpecific::getLogger()->error("Compute engine failed to connect to the swaps database.");
                die("Compute engine failed to connect to the swaps database.");
            }

            if ($db->character_set_name() !== "utf8mb4")
            {
                if (!$db->set_charset("utf8mb4"))
                {
                    SiteSpecific::getLogger()->error("Failed to set the swaps mysql connection character set to utf8mb4", ['mysqli_error' => $db->error]);
                    die("Failed to set the swaps mysql connection character set to utf8mb4");
                }
            }

            $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
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

            if ($db->connect_error)
            {
                SiteSpecific::getLogger()->error("Compute engine failed to connect to the ETL database.");
                die("Compute engine failed to connect to the ETL database.");
            }

            if ($db->character_set_name() !== "utf8mb4")
            {
                if (!$db->set_charset("utf8"))
                {
                    SiteSpecific::getLogger()->error("Failed to set the ETL mysql connection character set to utf8mb4", ['mysqli_error' => $db->error]);
                    die("Failed to set the ETL mysql connection character set to utf8mb4");
                }
            }

            $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
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
            $fileLogger = new Programster\Log\FileLogger(__DIR__ . '/../logs.csv');
            $mysqlLogger = new Programster\Log\MysqliLogger(SiteSpecific::getSwapsCacheDb(), "logs");
            $multiLogger = new Programster\Log\MultiLogger($fileLogger, $mysqlLogger);
            $logger = new Programster\Log\MinLevelLogger(1, $multiLogger);
        }

        return $logger;
    }
}
