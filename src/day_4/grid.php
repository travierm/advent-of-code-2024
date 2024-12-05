<?php

class Grid
{
    public GridIterator $iterator;

    public function __construct(array $data)
    {
        $this->iterator = new GridIterator($data);
    }

    public function positions()
    {
        return $this->iterator;
    }

    public function getPositionXPatternMatches(string $center, string $primary, string $secondary): int
    {
        $currentChar = strtolower($this->iterator->current());
        if ($currentChar !== $center) {
            return 0;
        }

        $matches = 0;
        $position = $this->iterator->key();
        $points = [
            // up left
            [
                new Position($position->x - 1, $position->y + 1),
                new Position($position->x + 1, $position->y - 1),
            ],
            // up right
            [
                new Position($position->x + 1, $position->y + 1),
                new Position($position->x - 1, $position->y - 1),
            ],
             // down left
            [
                new Position($position->x - 1, $position->y - 1),
                new Position($position->x + 1, $position->y + 1),
            ],
            // down right
            [
                new Position($position->x + 1, $position->y - 1),
                new Position($position->x - 1, $position->y + 1),
            ],
        ];

        $pointMatches = 0;
        foreach ($points as $point) {
            $primaryPoint = @$this->iterator->getByPosition($point[0]);
            $secondaryPoint = @$this->iterator->getByPosition($point[1]);

            if (!$primaryPoint || !$secondaryPoint) {
                continue;
            }

            if (strtolower($primaryPoint) == $primary && strtolower($secondaryPoint) == $secondary) {
                $pointMatches++;
            }
        }

        if ($pointMatches == 2) {
            return 1;
        }

        return 0;
    }

    public function getCurrentPositionMatches(string $match): int
    {
        $matchSplit = str_split(strtolower($match));
        $currentChar = strtolower($this->iterator->current());

        // if the current char isn't the first letter of our match then it can have no matches
        if ($currentChar !== $matchSplit[0]) {
            return 0;
        }

        $matchCount = 0;
        $remainingChars = array_slice($matchSplit, 1);
        if ($this->hasMatchForward($this->iterator->key(), $remainingChars)) {
            $matchCount++;
        }

        if ($this->hasReverseMatch($this->iterator->key(), $remainingChars)) {
            $matchCount++;
        }

        $matchCount += $this->verticalMatches($this->iterator->key(), $remainingChars);
        $matchCount += $this->diagMatches($this->iterator->key(), $remainingChars);

        return $matchCount;
    }

    public function diagMatches(Position $position, array $remainingChars): int
    {
        $matches = 0;

        // up/left
        $nextY = $position->y + 1;
        $nextX = $position->x - 1;
        $failedChecks = false;
        foreach ($remainingChars as $char) {
            try {
                $nextChar = @$this->iterator->get($nextX, $nextY);
                if (!$nextChar) {
                    $failedChecks = true;
                    break;
                }
                if (strtolower($nextChar) !== strtolower($char)) {
                    $failedChecks = true;
                    break;
                }

                $nextY++;
                $nextX--;
            } catch (Exception $e) {
                $failedChecks = true;
                break;
            }
        }

        if (!$failedChecks) {
            $matches++;
        }


        // up/right
        $nextY = $position->y + 1;
        $nextX = $position->x + 1;
        $failedChecks = false;
        foreach ($remainingChars as $char) {
            try {
                $nextChar = @$this->iterator->get($nextX, $nextY);
                if (!$nextChar) {
                    $failedChecks = true;
                    break;
                }
                if (strtolower($nextChar) !== strtolower($char)) {
                    $failedChecks = true;
                    break;
                }

                $nextY++;
                $nextX++;
            } catch (Exception $e) {
                $failedChecks = true;
                break;
            }
        }

        if (!$failedChecks) {
            $matches++;
        }
        // down/left
        $nextY = $position->y - 1;
        $nextX = $position->x - 1;
        $failedChecks = false;
        foreach ($remainingChars as $char) {
            try {
                $nextChar = @$this->iterator->get($nextX, $nextY);
                if (!$nextChar) {
                    $failedChecks = true;
                    break;
                }
                if (strtolower($nextChar) !== strtolower($char)) {
                    $failedChecks = true;
                    break;
                }

                $nextY--;
                $nextX--;
            } catch (Exception $e) {
                $failedChecks = true;
                break;
            }
        }

        if (!$failedChecks) {
            $matches++;
        }


        // down/right
        $nextY = $position->y - 1;
        $nextX = $position->x + 1;
        $failedChecks = false;
        foreach ($remainingChars as $char) {
            try {
                $nextChar = @$this->iterator->get($nextX, $nextY);
                if (!$nextChar) {
                    $failedChecks = true;
                    break;
                }
                if (strtolower($nextChar) !== strtolower($char)) {
                    $failedChecks = true;
                    break;
                }

                $nextY--;
                $nextX++;
            } catch (Exception $e) {
                $failedChecks = true;
                break;
            }
        }

        if (!$failedChecks) {
            $matches++;
        }

        return $matches;
    }

    public function verticalMatches(Position $position, array $remainingChars): int
    {
        $remainingCharCount = count($remainingChars);
        $matches = 0;

        // upward
        $failedChecks = false;
        $nextY = $position->y - 1;
        foreach ($remainingChars as $char) {
            try {
                $nextChar = @$this->iterator->get($position->x, $nextY);
                if (!$nextChar) {
                    $failedChecks = true;
                    break;
                }
                if (strtolower($nextChar) !== strtolower($char)) {
                    $failedChecks = true;
                    break;
                }

                $nextY--;
            } catch (Exception $e) {
                $failedChecks = true;
                break;
            }
        }

        if (!$failedChecks) {
            $matches++;
        }


        // downward
        $failedChecks = false;
        $nextY = $position->y + 1;
        foreach ($remainingChars as $char) {
            try {
                $nextChar = @$this->iterator->get($position->x, $nextY);
                if (!$nextChar) {
                    $failedChecks = true;
                    break;
                }
                if (strtolower($nextChar) !== strtolower($char)) {
                    $failedChecks = true;
                    break;
                }

                $nextY++;
            } catch (Exception $e) {
                $failedChecks = true;
                break;
            }
        }

        if (!$failedChecks) {
            $matches++;
        }


        return $matches;
    }

    public function hasMatchForward(Position $position, array $remainingChars)
    {
        $remainingCharCount = count($remainingChars);
        $neededSpaces = $position->x + $remainingCharCount;
        if ($neededSpaces > $this->iterator->width) {
            return false;
        }

        $nextX = $position->x + 1;
        foreach ($remainingChars as $char) {

            $nextChar = $this->iterator->get($nextX, $position->y);

            if (strtolower($nextChar) !== strtolower($char)) {
                return false;
            }

            $nextX++;
        }

        return true;
    }

    public function hasReverseMatch(Position $position, array $remainingChars)
    {
        $remainingCharCount = count($remainingChars);
        if ($position->x < $remainingCharCount) {
            return false;
        }

        $nextX = $position->x - 1;
        foreach ($remainingChars as $char) {
            $nextChar = $this->iterator->get($nextX, $position->y);
            if (strtolower($nextChar) !== strtolower($char)) {
                return false;
            }

            $nextX--;
        }

        return true;
    }
}

class Position
{
    public function __construct(public int $x, public int $y)
    {

    }
}

class GridIterator implements Iterator
{
    public int $height = 0;
    public int $width = 0;

    public int $currentX = 0;
    public int $currentY = 0;

    public function __construct(public array $data)
    {
        $this->height = count($this->data) - 1;
        $this->width = count($this->data[0]) - 1;
    }

    public function getByPosition(Position $p)
    {
        return $this->data[$p->y][$p->x];
    }

    public function get(int $x, int $y)
    {
        return $this->data[$y][$x];
    }

    public function rewind(): void
    {
        $this->currentX = 0;
        $this->currentY = 0;
    }

    public function next(): void
    {
        if ($this->currentX === $this->width) {
            $this->currentY++;
            $this->currentX = 0;
        } else {
            $this->currentX++;
        }
    }

    public function key(): Position
    {
        return new Position($this->currentX, $this->currentY);
    }

    public function valid(): bool
    {
        return isset($this->data[$this->currentY][$this->currentX]);
    }

    public function current(): string
    {
        return $this->data[$this->currentY][$this->currentX];
    }
}
