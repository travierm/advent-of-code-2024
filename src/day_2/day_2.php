
<?php
use App\Lib\Benchmark;
use Monolog\Logger;

use function PHPUnit\Framework\assertEquals;

require_once 'vendor/autoload.php';
require_once 'utils.php';

class ProblemState
{
    public string $inputFile = 'src/day_2/input.txt';

    /** @var []int */
    public array $reports = [];
    public array $unsafeReports = [];
    public int $safeReports = 0;
    public int $safeReportsWithDampener = 0;
}

function assertStateAccurate(ProblemState $state)
{
    assertEquals(213, $state->safeReports, 'safe report count is accurate');
    assertEquals(285, $state->safeReportsWithDampener, 'safe report count with dampener is accurate');
}

$log = getLogger();
$state = new ProblemState();

// read the file contents into memory
$file = new SplFileObject($state->inputFile, 'r');

while (!$file->eof()) {
    $state->reports[] = array_map(fn ($n) => (int) $n, explode(' ', $file->current()));

    $file->next();
}

foreach ($state->reports as $report) {
    $isSafe = reportIsSafe($report, $log);
    if ($isSafe) {
        $state->safeReports++;
    } else {
        $state->unsafeReports[] = $report;
    }
}

$state->safeReportsWithDampener += $state->safeReports;
foreach ($state->unsafeReports as $report) {
    $variations = getReportVariations($report);

    foreach ($variations as $variation) {
        if (reportIsSafe($variation, $log)) {
            $state->safeReportsWithDampener++;

            continue 2;
        }
    }
}

$log->info('found ' . $state->safeReports . ' safe reports');
$log->info('found ' . $state->safeReportsWithDampener . ' safe reports with dampener');

assertStateAccurate($state);
