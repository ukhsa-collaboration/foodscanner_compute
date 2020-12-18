<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(__DIR__ . '/../bootstrap.php');

SiteSpecific::getLogger()->info("Swaps compute engine - swapping cache tables.");

$queries = array(
    "RENAME TABLE `swaps` to `swaps_buffer_replacement`",
    "RENAME TABLE `swaps_buffer` to `swaps`",
    "RENAME TABLE `swaps_buffer_replacement` to `swaps_buffer`",
);

# Running a multi-query instead of a transaction because table renames have an implicit commit.
$multiquery = new iRAP\MultiQuery\MultiQuery(SiteSpecific::getSwapsCacheDb(), $queries);

if ($multiquery->wasSuccessful() === false)
{
    SiteSpecific::getLogger()->error("Swapping cache tables failed.", ['errors' => $multiquery->getErrors()]);
}
else
{
    SiteSpecific::getLogger()->info("Swaps compute engine - finished swapping cache tables.");
}

