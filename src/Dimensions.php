<?php namespace Tetris;

final class Dimensions
{
    public function __construct(
        private int $x,
        private int $y,
    ) {
    }

    public static function fromInt(int $x, int $y): self
    {
        return new self($x, $y);
    }

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }
}