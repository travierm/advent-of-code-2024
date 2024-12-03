<?php

use Monolog\Logger;

/**
 * @param []int $report
 */
function reportIsSafe(array $report, Logger $log)
{
    $reportLength = count($report);

    // find reports with levels that differ by 3 or equal to each other
    foreach ($report as $index => $level) {
        if ($index === $reportLength - 1) {
            continue;
        }

        $nextLevel = $report[$index + 1];

        if (abs($level - $nextLevel) > 3) {
            //$log->debug(sprintf('report failed at %s %s', $level, $nextLevel), $report);
            return false;
        }

        if ($level == $nextLevel) {
            //$log->debug(sprintf('report failed at %s %s', $level, $nextLevel), $report);
            return false;
        }
    }

    unset($level);
    unset($index);

    // find if a report is increasing or decreasing and if levels match that status
    $isIncreasing = false;
    foreach ($report as $index => $level) {
        if ($index === $reportLength - 1) {
            continue;
        }

        $nextLevel = $report[$index + 1];
        if ($index === 0) {
            $isIncreasing = $level < $nextLevel;
            continue;
        }

        if ($isIncreasing && $level > $nextLevel) {
            return false;
        }

        if (!$isIncreasing && $level < $nextLevel) {
            return false;
        }
    }

    return true;
}

function arrayWithoutIndex(array $array, int $index)
{
    return array_merge(
        array_slice($array, 0, $index),
        array_slice($array, $index + 1)
    );
}

function getReportVariations(array $report)
{
    $variations = [];
    foreach ($report as $index => $level) {
        $variations[] = arrayWithoutIndex($report, $index);
    }

    return $variations;
}
