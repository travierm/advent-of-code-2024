<?php

use App\Lib\Benchmark;

require_once 'vendor/autoload.php';


$log = getLogger();
$bench = new Benchmark($log);

/**
 * Plan of attack
 * sort both lists
 * run through the pairs and add update the distance
*/

$bench->start('parse input, build arrays and sort arrays.');

$content = file_get_contents('src/day_1/input.txt');
$rows = explode("\n", $content);

$leftList = [];
$rightList = [];
foreach ($rows as $row) {
    if ($row == "") {
        continue;
    }

    $pair = explode("   ", $row);
    $leftList[] = (int) $pair[0];
    $rightList[] = (int) $pair[1];
}

sort($leftList);
sort($rightList);

$bench->end();

$bench->start("calculate distance between each pair");
if (count($leftList) !== count($rightList)) {
    $log->error("the lists do not have equal lengths", [
        'leftList' => count($leftList),
        'rightList' => count($rightList)
    ]);
    exit(1);
}

$totalDistance = 0;
foreach ($leftList as $index => $leftInput) {
    $rightInput = $rightList[$index];
    $distance =  $rightInput - $leftInput;
    $totalDistance += $distance;
}

$bench->end();

$log->info('found total distance of ' . $totalDistance);
