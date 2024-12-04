
<?php

use App\Lib\Benchmark;

use function PHPUnit\Framework\assertEquals;

require_once 'vendor/autoload.php';

class ProblemState
{
    public string $inputFile = 'src/day_3/input.txt';
    public string $inputFileTwo = 'src/day_3/input2.txt';
    public string $corruptedMemory = '';
    public string $corruptedMemoryTwo = '';

    public int $totalValue = 0;
    public int $totalValueWithControlFlow = 0;
}

// (mul\()
// (mul\(\d+(,\d)*)\)
// (mul\(\d+(,\d+)*\))
// (?:do\()\)|(?:don't\()\)

function assertStateAccurate(ProblemState $state)
{
    assertEquals(159833790, $state->totalValue, 'total value is correct');
    assertEquals(89349241, $state->totalValueWithControlFlow, 'total value for part two is correct');
}

$log = getLogger();
$state = new ProblemState();
$benchmark = new Benchmark($log, 1);

$benchmark->bench('part_one', function () use (&$state, $log) {
    $file = new SplFileObject($state->inputFile, 'r');

    $file->setFlags(SplFileObject::DROP_NEW_LINE);
    while (!$file->eof()) {
        $state->corruptedMemory .= $file->current();

        $file->next();
    }


    $matches = [];
    preg_match_all("/(mul\(\d+(,\d+)*\))/", $state->corruptedMemory, $matches);

    $instructions = $matches[0];
    foreach ($instructions as $instruction) {
        $data = explode(',', $instruction);

        $firstNum = (int) str_replace("mul(", "", $data[0]);
        $secondNum = (int) str_replace(")", "", $data[1]);

        $state->totalValue += $firstNum * $secondNum;
    }

    $log->info('found total value of ' . $state->totalValue);
});

$benchmark->bench('part_two', function () use (&$state, $log) {
    $file = new SplFileObject($state->inputFileTwo, 'r');

    $file->setFlags(SplFileObject::DROP_NEW_LINE);
    while (!$file->eof()) {
        $state->corruptedMemoryTwo .= $file->current();

        $file->next();
    }


    $partTwoMatches = [];
    preg_match_all("/(?:do\()\)|(?:don't\()\)|mul\(\d+(,\d+)*\)/", $state->corruptedMemoryTwo, $partTwoMatches);
    $instructions = $partTwoMatches[0];

    $allowMuls = true;
    foreach ($instructions as $instruction) {
        if (str_contains($instruction, 'mul')) {
            if (!$allowMuls) {
                continue;
            }

            $data = explode(',', $instruction);
            $firstNum = (int) str_replace("mul(", "", $data[0]);
            $secondNum = (int) str_replace(")", "", $data[1]);

            $state->totalValueWithControlFlow += $firstNum * $secondNum;

            continue;
        }

        if ($instruction === "don't()") {
            $allowMuls = false;

            continue;
        }

        if ($instruction === 'do()') {
            $allowMuls = true;

            continue;
        }

        throw new \Exception("Found invalid instruction: " . $instruction);
    }


    $log->info('found totalValueWithControlFlow of ' . $state->totalValueWithControlFlow);

});

assertStateAccurate($state);
