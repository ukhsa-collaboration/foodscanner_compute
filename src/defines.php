<?php

/*
 * Specify settings that should not change based on environment here.
 */

# Specify the name of the table that will be "built up" over time from the compute
define('SWAPS_CACHE_BUFFER_TABLE_NAME', "swaps_buffer");


# Specify the name of the table that the API uses to fetch swaps data. This will be swapped out with the buffer table
# when the buffer table has finished being built.
define('SWAPS_CACHE_TABLE_NAME', "swaps");


# Folder to store files to allow measuring progress made of multiprocess execution
define('PROGRESS_FOLDER', '/tmp/progress-folder');


# Specify how many barcodes should be handed to each python process to calculate results for.
# Increasing this number reduces the read-load on disk as the start of the process ingests the cache file
# but too large and you risk issues with importing the results into the database. (each barcode produces 30 rows to import)
define('NUM_BARCODES_PER_PYTHON_PROCESS', 20);