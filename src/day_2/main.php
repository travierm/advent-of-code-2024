
<?php
use App\Lib\Benchmark;

use function PHPUnit\Framework\assertEquals;

require_once 'vendor/autoload.php';

class ProblemState
{
    public string $inputFile = 'src/day_2/input.txt';
}

function assertStateAccurate(ProblemState $state)
{
    // /assertEquals()
}

$log = getLogger();
