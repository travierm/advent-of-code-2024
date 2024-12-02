<?php

use App\Lib\Benchmark;

use function PHPUnit\Framework\assertEquals;

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

function assertStateAccurate(ProblemState $state)
{
    assertEquals($state->totalDistance, 2430334, 'total distance');
    assertEquals($state->similarityScore, 28786472, 'similarity score');
}

$log = getLogger();

$state = new ProblemState();

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

sort($state->leftList, SORT_NUMERIC);
sort($state->rightList, SORT_NUMERIC);


foreach ($state->leftList as $index => $leftInput) {
    $rightInput = $state->rightList[$index];
    $distance = abs($leftInput - $rightInput);

    $state->totalDistance += $distance;
}

$log->info('found total distance of ' . $state->totalDistance);

$frequencies = array_count_values($state->rightList);

foreach ($state->leftList as $num) {
    if (isset($frequencies[$num])) {
        $state->similarityScore += $num * $frequencies[$num];
    }
}


$log->info('found similarityScore of ' . $state->similarityScore);


assertStateAccurate($state);
