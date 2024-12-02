<?php

namespace App\Lib;

use Closure;
use Monolog\Logger;

class Benchmark
{
    private float $benchStart;
    private float $stepStart;
    private array $iterations = [];
    private ?string $currentStep = null;
    private array $currentSteps = [];
    private array $beforeEachCallbacks = [];

    public function __construct(private Logger $log, private int $numOfIterations = 100)
    {
    }

    public function beforeEach(Closure $fn)
    {
        $this->beforeEachCallbacks[] = $fn;
    }

    public function bench(string $label, Closure $benchmarkFn): array
    {
        $this->log->info("Starting benchmark: {$label} with {$this->numOfIterations} iterations");

        // Run the benchmark multiple times
        for ($i = 0; $i < $this->numOfIterations; $i++) {
            array_map(fn ($func) => $func(), $this->beforeEachCallbacks);

            $this->benchStart = microtime(true);
            $this->currentSteps = [];

            $benchmarkFn($this);

            $totalTime = (microtime(true) - $this->benchStart) * 1000;
            $this->iterations[] = [
                'steps' => $this->currentSteps,
                'total_time' => $totalTime
            ];
        }

        return $this->analyzeResults($label);
    }

    public function step(string $label, Closure $callback): void
    {
        $this->currentStep = $label;
        $this->stepStart = microtime(true);

        $callback();

        $this->endStep();
    }

    private function endStep(): void
    {
        if (!$this->currentStep) {
            return;
        }

        $timeElapsed = (microtime(true) - $this->stepStart) * 1000;

        $this->currentSteps[] = [
            'label' => $this->currentStep,
            'time' => $timeElapsed
        ];

        $this->currentStep = null;
    }

    private function analyzeResults(string $label): array
    {
        $totalTimes = array_column($this->iterations, 'total_time');
        $stepStats = $this->calculateStepStats();

        $stats = [
            'benchmark' => $label,
            'iterations' => count($this->iterations),
            'total_time' => [
                'avg' => $this->calculateAverage($totalTimes),
                'min' => min($totalTimes),
                'max' => max($totalTimes),
                'p90' => $this->calculatePercentile($totalTimes, 90),
                'p95' => $this->calculatePercentile($totalTimes, 95),
                'p99' => $this->calculatePercentile($totalTimes, 99),
            ],
            'steps' => $stepStats
        ];

        // Log results
        $this->logResults($stats);

        // Reset state
        $this->iterations = [];

        return $stats;
    }

    private function calculateStepStats(): array
    {
        $stepStats = [];

        // Get all unique step labels
        $stepLabels = array_unique(array_column(
            array_merge(...array_column($this->iterations, 'steps')),
            'label'
        ));

        foreach ($stepLabels as $label) {
            $stepTimes = [];

            // Collect times for this step across all iterations
            foreach ($this->iterations as $iteration) {
                foreach ($iteration['steps'] as $step) {
                    if ($step['label'] === $label) {
                        $stepTimes[] = $step['time'];
                    }
                }
            }

            $stepStats[$label] = [
                'avg' => $this->calculateAverage($stepTimes),
                'min' => min($stepTimes),
                'max' => max($stepTimes),
                'p90' => $this->calculatePercentile($stepTimes, 90),
                'p95' => $this->calculatePercentile($stepTimes, 95),
                'p99' => $this->calculatePercentile($stepTimes, 99),
            ];
        }

        return $stepStats;
    }

    private function calculateAverage(array $numbers): float
    {
        return array_sum($numbers) / count($numbers);
    }

    private function calculatePercentile(array $numbers, int $percentile): float
    {
        sort($numbers);
        $index = ceil(($percentile / 100) * count($numbers)) - 1;
        return $numbers[$index];
    }

    private function logResults(array $stats): void
    {
        $this->log->info("Benchmark results for: " . $stats['benchmark'], [
            'iterations' => $stats['iterations'],
            'avg_time' => round($stats['total_time']['avg'], 2) . ' ms',
            'p90_time' => round($stats['total_time']['p90'], 2) . ' ms',
            'p95_time' => round($stats['total_time']['p95'], 2) . ' ms',
            'p99_time' => round($stats['total_time']['p99'], 2) . ' ms',
        ]);

        foreach ($stats['steps'] as $stepLabel => $stepStats) {
            $this->log->debug("Step statistics: " . $stepLabel, [
                'avg_time' => round($stepStats['avg'], 2) . ' ms',
                'p90_time' => round($stepStats['p90'], 2) . ' ms',
                'min_time' => round($stepStats['min'], 2) . ' ms',
                'max_time' => round($stepStats['max'], 2) . ' ms',
            ]);
        }
    }
}
