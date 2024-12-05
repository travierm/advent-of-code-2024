
<?php
use App\Lib\Benchmark;
use Monolog\Logger;

use function PHPUnit\Framework\assertEquals;

require_once 'vendor/autoload.php';
require_once 'grid.php';

class ProblemState
{
    public string $inputFile = 'src/day_4/input.txt';

    /** @var array<array...> $grid */
    public array $gridData = [];
    public int $totalMatches = 0;
    public int $totalXMatches = 0;
}

function assertStateAccurate(ProblemState $state)
{
    assertEquals(2397, $state->totalMatches, 'totalMatches is accurate');
    assertEquals(1824, $state->totalXMatches, 'totalXMatches is accurate');
}

$log = getLogger();
$state = new ProblemState();

// read the file contents into memory
$file = new SplFileObject($state->inputFile, 'r');
$file->setFlags(SplFileObject::DROP_NEW_LINE);

while (!$file->eof()) {
    $state->gridData[] = str_split($file->current());

    $file->next();
}

$grid = new Grid($state->gridData);

foreach ($grid->positions() as $position) {
    $state->totalMatches += $grid->getCurrentPositionMatches('xmas');
    $state->totalXMatches += $grid->getPositionXPatternMatches('a', 'm', 's');
}

$log->info('found ' . $state->totalMatches . ' totalMatches');
$log->info('found ' . $state->totalXMatches . ' totalMatches');

assertStateAccurate($state);
