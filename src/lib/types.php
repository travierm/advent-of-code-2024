<?php

class Position
{
    public function __construct(public int $x, public int $y)
    {

    }

    public function __toArray()
    {
        return [$this->x, $this->y];
    }
}
