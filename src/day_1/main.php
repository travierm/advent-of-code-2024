<?php

use App\Lib\Benchmark;

require_once 'vendor/autoload.php';

class ProblemState
{
    public string $inputFile = 'src/day_1/input.txt';
    public int $totalDistance = 0;
    public int $similarityScore = 0;

    /** @var []int */
    public array $leftList = [];
    /** @var []int */
    public array $rightList = [];

}

$log = getLogger();
$state = new ProblemState();
$bench = new Benchmark($log);

$bench->start('parse input, build & sort arrays', function () use ($state) {

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
        $distance = abs($leftInput - $rightInput);

        $state->totalDistance += $distance;
    }
});

$log->info('found total distance of ' . $state->totalDistance);

$bench->start('check how many times numbers in the left list appear in the right list', function () use ($state, $log) {
    foreach ($state->leftList as $num) {
        $occurrences = count(array_filter($state->rightList, fn ($n) => $n === $num));

        $state->similarityScore += $num * $occurrences;
    }
});

$log->info('found similarityScore of ' . $state->similarityScore);
