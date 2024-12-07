<?php

namespace App\Day5;

use SplFileObject;
use Monolog\Logger;
use App\Lib\Benchmark;
use Exception;

use function PHPUnit\Framework\assertEquals;

require_once 'vendor/autoload.php';
require_once 'queue.php';

class ProblemState
{
    public string $inputFile = 'src/day_5/input.txt';
    public array $orderingRules = [];
    public array $updates = [];
    public array $correctUpdates = [];
    public array $incorrectUpdates = [];
    public array $correctedUpdate = [];
    public array $ruleMap = [];

    public int $totalMiddleValue = 0;
    public int $totalMiddleValueCorrected = 0;
}

function assertStateAccurate(ProblemState $state)
{
    assertEquals(4662, $state->totalMiddleValue);
}

$log = getLogger();
$state = new ProblemState();

// read the file contents into memory
$file = new SplFileObject($state->inputFile, 'r');
$file->setFlags(SplFileObject::DROP_NEW_LINE);

$onUpdates = false;
while (!$file->eof()) {
    if ($file->current() === "" && !$onUpdates) {
        $onUpdates = true;
        $file->next();
    }

    if (!$onUpdates) {
        $nums = explode("|", $file->current());
        $state->orderingRules[] = [(int) $nums[0], (int) $nums[1]];
    } else {
        $state->updates[] = array_map(function ($item) {
            return (int) $item;
        }, explode(',', $file->current()));
    }


    $file->next();

}

// prepare rules
foreach ($state->orderingRules as $rule) {
    $primary = $rule[0];
    $secondary = $rule[1];

    if (!isset($state->ruleMap[$secondary])) {
        $state->ruleMap[$secondary] = [];
    }

    $state->ruleMap[$secondary][] = $primary;
}

function updateIsInvalid(array $update, $state)
{
    $isBadUpdate = false;
    foreach ($update as $pageIndex => $page) {
        $nextPage = $update[$pageIndex + 1] ?? null;

        if ($nextPage && in_array($nextPage, $state->ruleMap[$page] ?? [])) {
            // $log->info($nextPage . ' is in rules for ' . $page, [$update]);
            // $log->info('rule map', $state->ruleMap[$page]);
            $isBadUpdate = true;
            break;
        }
    }

    return $isBadUpdate;
}

// validate updates
foreach ($state->updates as $index => $update) {
    $isBadUpdate = updateIsInvalid($update, $state);
    if (!$isBadUpdate) {
        $state->correctUpdates[] = $update;
    } else {
        $state->incorrectUpdates[] = $update;
    }
}

// get incorrectUpdates
foreach ($state->incorrectUpdates as $index => $update) {
    $queue = new Queue();

    $update = [97,13,75,29,47];
    foreach ($update as $page) {
        $queue->pushAfter($page, $state->ruleMap);
    }

    if (updateIsInvalid($queue->data, $state)) {
        $log->error("could not correct update");
        $log->error("old", $update);
        $log->error("new", $queue->data);

        exit();
    }

    dump($update);
    dd($queue->data);

    $state->correctedUpdate[] = $queue->data;
}


// get middle value of correctUpdates
foreach ($state->correctUpdates as $update) {
    $middleIndex = (int) floor(count($update) / 2);

    $state->totalMiddleValue += $update[$middleIndex];
}

// get middle value of correctedUpdates
foreach ($state->correctedUpdate as $update) {
    $middleIndex = (int) floor(count($update) / 2);

    $state->totalMiddleValueCorrected += $update[$middleIndex];
}

$log->info('found totalMiddleValue of ' . $state->totalMiddleValue);
$log->info('found totalMiddleValueCorrected of ' . $state->totalMiddleValueCorrected);

assertStateAccurate($state);
