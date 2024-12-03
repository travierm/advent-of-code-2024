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
            return false;
        }

        if ($level == $nextLevel) {
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

function getReportVariations(array $report)
{
    $variations = [];
    foreach ($report as $index => $level) {
        $variations[] = arrayWithoutIndex($report, $index);
    }

    return $variations;
}
