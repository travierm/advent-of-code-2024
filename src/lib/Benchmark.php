<?php

namespace App\Lib;

use Monolog\Logger;

class Benchmark
{
    private $startTimes = [];
    private $endTimes = [];
    private $memoryUsage = [];

    public function __construct(private Logger $log)
    {
    }

    public function start(string $label)
    {
        $this->startTimes[$label] = microtime(true);
        $this->memoryUsage[$label]['start'] = memory_get_usage();
    }

    public function end(string $label)
    {
        $endTime = microtime(true);
        $memoryUsed = memory_get_usage();

        $this->endTimes[$label] = $endTime;
        $this->memoryUsage[$label]['end'] = $memoryUsed;

        if ($this->log) {
            $timeElapsed = ($endTime - $this->startTimes[$label]) * 1000; // Convert to milliseconds
            $memoryDiff = $memoryUsed - $this->memoryUsage[$label]['start'];

            $this->log->info("benchmark for " . $label, [
                'time' => round($timeElapsed, 2) . ' ms',
                'memory' => round($memoryDiff / 1024, 2) . ' KB'
            ]);
        }
    }

    public function getResults()
    {
        $results = [];

        foreach ($this->startTimes as $label => $startTime) {
            if (isset($this->endTimes[$label])) {
                $timeElapsed = ($this->endTimes[$label] - $startTime) * 1000; // Convert to milliseconds
                $memoryUsed = $this->memoryUsage[$label]['end'] - $this->memoryUsage[$label]['start'];

                $results[$label] = [
                    'time' => round($timeElapsed, 2) . ' ms',
                    'memory' => round($memoryUsed / 1024, 2) . ' KB'
                ];
            }
        }

        return $results;
    }
}
