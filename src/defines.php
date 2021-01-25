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



# Specify if we are to override the PHE_cat column with the machine learning category, or whether we will add the
# machine learning category as another column called phe_ml_cat.
define('OVERRIDE_PHE_CAT_COLUMN', true);


# This only applies if OVERRIDE_PHE_CAT_COLUMN is set to true.
# Specify whether we want to set the PHE_cat to null if we don't have the product in the machine learning categorization
# if this is set to false and a barcode is not there, we will leave the categorization value to what it was before.
# If you don't understand, best to set to false as this essentially acts like a "fallback" to previous categorization
# logic.
define('ONLY_USE_ML_CAT_IF_OVERRIDING_PHE_CAT', false);


# Define the paths to the folders that will be used for storing the outputs of running the machine learning code
# We do this so we can have the multiple processes output in parallel, and then collect/import them all together in
# one go, resulting in a single insert query.
define('SPACY_OUTPUT_FOLDER', '/root/spacy-output');
define('SKLEARN_OUTPUT_FOLDER', '/root/sklearn-output');
define('SPACY_ERROR_OUTPUT_FOLDER', '/root/spacy-error-output');
define('SKLEARN_ERROR_OUTPUT_FOLDER', '/root/sklearn-error-output');


# Specify the number of products per single json config
define('ML_NUM_PRODUCTS_PER_JSON_INPUT_CONFIG', 20);


# Specify whether you want to strip out all the products that are category none or not, from the input to the swaps
# the result being that any product with category none will not produce any swaps, and they will also never appear
# as swaps for other products.
# The reasoning for stripping out such products is because brandbank have been told to just categorize "alcohol related"
# products as none. E.g. an alcohol-free beer, or a "mixer".
define('SWAPS_STRIP_OUT_CATEGORY_NONE', true);


# Specify whether you want to strip out all the products that are category "other" from the input to the swaps
# the result being that any product with category "other" will not produce any swaps, and they will also never appear
# as swaps for other products.
define('SWAPS_STRIP_OUT_CATEGORY_OTHER', true);