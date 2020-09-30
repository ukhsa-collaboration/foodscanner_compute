<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once(__DIR__ . '/../bootstrap.php');

$expectedNumFiles = count(file(__DIR__ . '/commands.sh'));
$startTime = time();
$numFiles = intval(shell_exec("ls -1 " . PROGRESS_FOLDER . " | wc -l"));
$startPercentage = $numFiles / $expectedNumFiles * 100;

while (true)
{
    $numFiles = intval(shell_exec("ls -1 " . PROGRESS_FOLDER . " | wc -l"));
    $percentage = $numFiles / $expectedNumFiles * 100;

    $timeDiff = time() - $startTime;
    $percentageDiff = $percentage - $startPercentage;

    if ($percentageDiff < 0.1)
    {
        $percentageDiff = 0.1;
    }

    $secondsPerPercentage = $timeDiff / $percentageDiff;
    $percentageRemaining = 100 - $percentage;
    $timeRemaining = $percentageRemaining * $secondsPerPercentage;

    $timeRemaining = getHumanReadableTimeRemaining($timeRemaining);
    $percentageString = number_format($percentage, 2) . '%';
    print "($percentageString) {$timeRemaining} remaining \r";

    //Programster\CoreLibs\CliLib::showProgressBar($percentage, $numDecimalPlaces=2);
    sleep(10);
}

function getHumanReadableTimeRemaining($diffInSeconds)
{
     if ($diffInSeconds < 60)
    {
        $resultString = "$diffInSeconds seconds";
    }
    elseif ($diffInSeconds < 3600)
    {
        $diffInMinutes = (int) ($diffInSeconds / 60);
        $remainder = (int) ($diffInSeconds % 60);
        $resultString = "$diffInMinutes mins $remainder secs";
    }
    elseif ($diffInSeconds < 86400)
    {
        $diffInHours = (int) ($diffInSeconds / 3600);
        $remainder = (int) ($diffInSeconds % 3600);
        $minutes = (int)($remainder / 60);
        $resultString = "$diffInHours hours $minutes mins";
    }
    else
    {
        $diffInDays = (int) ($diffInSeconds / 86400);
        $remainder = (int) ($diffInSeconds % 86400);
        $hours = (int)($remainder / 3600);
        $resultString = "$diffInDays days $hours hours";
    }

    return $resultString;
}
