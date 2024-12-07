<?php

namespace App\Day6;

use PathFinder;
use Position;
use SplFileObject;

use function PHPUnit\Framework\assertEquals;

require_once 'vendor/autoload.php';
require_once 'path.php';

class ProblemState
{
    public string $inputFile = 'src/day_6/input.txt';
    public array $map = [];
    public int $totalPositions = 0;
    public array $startPos = [];
}

function assertStateAccurate(ProblemState $state)
{
    assertEquals(4722, $state->totalPositions);
}

$log = getLogger();
$state = new ProblemState();

// read the file contents into memory
$file = new SplFileObject($state->inputFile, 'r');
$file->setFlags(SplFileObject::DROP_NEW_LINE);

$onUpdates = false;
while (!$file->eof()) {
    $row = str_split($file->current());
    $state->map[] = $row;

    $xPos = array_search('^', $row);
    if ($xPos) {
        $state->startPos = [$xPos, count($state->map) - 1];
    }

    $file->next();
}

function printMap(array $map)
{
    foreach ($map as $row) {
        echo implode('', $row) . PHP_EOL;
    }
}

$log->info('found starting position of guard', $state->startPos);
printMap($state->map);

$path = new PathFinder($state->map, new Position($state->startPos[0], $state->startPos[1]), '#');
$shadowMap = $path->findShadowMap();
printMap($shadowMap);


$totalSpots = 0;
foreach ($shadowMap as $row) {
    $state->totalPositions += array_count_values($row)['X'] ?? 0;
}

$log->info('found totalPositions of ' . $state->totalPositions);
assertStateAccurate($state);
