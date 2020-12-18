<?php

/*
 *
 */

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/defines.php');

new \iRAP\Autoloader\Autoloader([
    __DIR__ . '/exceptions',
    __DIR__ . '/libs',
    __DIR__ . '/models',
    __DIR__ . '/models/db',
]);

new ErrorLogger(SiteSpecific::getLogger(), "Swaps API");
