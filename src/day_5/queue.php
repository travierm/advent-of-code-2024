<?php

namespace App\Day5;

class Queue
{
    public array $data = [];

    public function __construct()
    {

    }

    public function push(int $num): void
    {
        $this->data[] = $num;
    }


    public function pushAfter(int $num, array $ruleMap)
    {
        if (count($this->data) === 0) {
            $this->data[] = $num;
            return;
        }

        $copy = $this->data;
        $addedNum = false;
        foreach ($this->data as $index => $existingNumber) {
            if (in_array($num, $ruleMap[$existingNumber] ?? [])) {
                // our number should copy before the current one
                $copy = array_slice($copy, 0, $index);
                $copy[$index] = $num;
                $copy[] = $existingNumber;

                $addedNum = true;
            }
        }

        if (!$addedNum) {
            $copy[] = $num;
        }

        $this->data = $copy;
    }
}
