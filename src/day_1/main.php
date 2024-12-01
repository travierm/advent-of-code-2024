<?php

use App\Lib\Benchmark;

require_once 'vendor/autoload.php';


$log = getLogger();
$bench = new Benchmark($log);

class ProblemState
{
    public string $inputFile = 'src/day_1/input.txt';
    public array $leftList = [];
    public array $rightList = [];
    public int $totalDistance = 0;
}

$state = new ProblemState();

$bench->start('parse input, build arrays and sort arrays.', function () use ($state) {

    $content = file_get_contents($state->inputFile);
    $rows = explode("\n", $content);

    foreach ($rows as $row) {
        if ($row == "") {
            continue;
        }

        $pair = explode("   ", $row);
        $state->leftList[] = (int) $pair[0];
        $state->rightList[] = (int) $pair[1];
    }

    sort($state->leftList);
    sort($state->rightList);
});


$bench->start('calculate distance between each pair', function () use ($state, $log) {
    if (count($state->leftList) !== count($state->rightList)) {
        $log->error("the lists do not have equal lengths", [
            'leftList' => count($state->leftList),
            'rightList' => count($state->rightList)
        ]);
        exit(1);
    }

    foreach ($state->leftList as $index => $leftInput) {
        $rightInput = $state->rightList[$index];
        $distance =  abs($leftInput - $rightInput);

        $state->totalDistance += $distance;
    }

});

$log->info('found total distance of ' . $state->totalDistance);
