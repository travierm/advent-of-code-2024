<?php

class PathFinder
{
    public array $shadowMap = [];
    public string $direction = '^';

    private int $mapHeight;
    private int $mapLength;
    private bool $outsideOfMap = false;
    private Position $currentPos;

    public function __construct(
        public array $map,
        public Position $startPos,
        public string $obstacleChar = '#'
    ) {
        $this->mapHeight = count($this->map) - 1;
        $this->mapLength = count($map[0]) - 1;

        $this->shadowMap = $this->map;
    }

    public function posIsOnMap(Position $pos)
    {
        return $pos->x <= $this->mapLength && $pos->y <= $this->mapHeight;
    }

    public function findShadowMap()
    {
        $log = getLogger();
        $this->currentPos = $this->startPos;
        while (!$this->outsideOfMap) {
            $nextPos = match($this->direction) {
                '^' => $this->getNextPosVertical($this->currentPos, true),
                'v' => $this->getNextPosVertical($this->currentPos, false),
                '>' => $this->getNextPosHorizontal($this->currentPos, true),
                '<' => $this->getNextPosHorizontal($this->currentPos, false),
            };

            $nexPosChar = $this->getCharAtPos($nextPos);
            if (!$nexPosChar) {
                $this->setShadowSpotChar($this->currentPos);
                $this->outsideOfMap = true;
                continue;
            }

            if ($nexPosChar === $this->obstacleChar) {
                $this->direction = $this->getNextDirection();
                $log->info('changing direction to ' . $this->direction);
            } else {
                $this->setShadowSpotChar($this->currentPos);
                $this->currentPos = $nextPos;

                $log->info('moved to position', (array) $this->currentPos);
            }
        }

        return $this->shadowMap;
    }

    public function getNextDirection()
    {
        return match($this->direction) {
            '^' => '>',
            '>' => 'v',
            'v' => '<',
            '<' => '^'
        };
    }

    public function setShadowSpotChar(Position $pos)
    {
        $this->shadowMap[$pos->y][$pos->x] = 'X';
    }

    public function getCharAtPos(Position $pos): ?string
    {
        return $this->map[$pos->y][$pos->x] ?? null;
    }

    public function getNextPosHorizontal(Position $pos, bool $right): Position
    {
        $currentX = $pos->x;
        $nextX = $right ? $currentX + 1 : $currentX - 1;

        return new Position($nextX, $pos->y);
    }

    public function getNextPosVertical(Position $pos, bool $up): Position
    {
        $currentY = $pos->y;
        $nextY = $up ? $currentY - 1 : $currentY + 1;

        return new Position($pos->x, $nextY);
    }
}
