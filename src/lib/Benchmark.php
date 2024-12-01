<?php

namespace App\Lib;

use Monolog\Logger;

class Benchmark
{
    private float $startTime;
    private int $startMemory;
    private ?string $currentStep = null;

    public function __construct(private Logger $log)
    {
    }

    public function start(string $label)
    {
        $this->currentStep = $label;
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();

        $this->log->info('starting step: ' . $label);
    }

    public function end()
    {
        if (!$this->currentStep) {
            return;
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $timeElapsed = ($endTime - $this->startTime) * 1000; // Convert to milliseconds
        $memoryDiff = $endMemory - $this->startMemory;

        $this->log->info("end of step benchmark", [
            'time' => round($timeElapsed, 2) . ' ms',
            'memory' => round($memoryDiff / 1024, 2) . ' KB'
        ]);

        $this->currentStep = null;
    }
}
