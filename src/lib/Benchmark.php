<?php

namespace App\Lib;

use Closure;
use Monolog\Logger;

class Benchmark
{
    private float $benchStart;    // Overall benchmark start time
    private float $stepStart;     // Current step start time
    private ?string $currentStep = null;
    private array $steps = [];
    private ?string $currentBench = null;

    public function __construct(private Logger $log)
    {
    }

    public function startBench(string $label)
    {
        $this->currentBench = $label;
        $this->steps = [];
        $this->benchStart = microtime(true);

        //$this->log->info("recording benchmark: " . $label);
    }

    public function endBench()
    {
        if (!$this->currentBench) {
            return;
        }

        $totalTime = (microtime(true) - $this->benchStart) * 1000;
        $stepTimes = array_column($this->steps, 'time');

        // Find slowest step
        $slowestStep = $this->steps[0];
        foreach ($this->steps as $step) {
            if ($step['time'] > $slowestStep['time']) {
                $slowestStep = $step;
            }
        }

        $this->log->debug("benchmark complete: " . $this->currentBench, [
            'total_time' => round($totalTime, 2) . ' ms',
            'step_count' => count($this->steps),
            'average_step_time' => round(array_sum($stepTimes) / max(count($stepTimes), 1), 2) . ' ms'
        ]);

        $this->log->warning("slowest step: " . $slowestStep['label'] . ' (' . round($slowestStep['time'], 2) . ' ms)');

        $this->currentBench = null;
        $this->steps = [];
    }

    public function step(string $label, Closure $callback)
    {
        $this->currentStep = $label;
        $this->stepStart = microtime(true);

        $callback();

        $this->endStep();
    }

    private function endStep()
    {
        if (!$this->currentStep) {
            return;
        }

        $timeElapsed = (microtime(true) - $this->stepStart) * 1000;

        $this->steps[] = [
            'label' => $this->currentStep,
            'time' => $timeElapsed
        ];

        $this->log->debug("step complete: " . $this->currentStep, [
            'time' => round($timeElapsed, 2) . ' ms'
        ]);

        $this->currentStep = null;
    }
}
