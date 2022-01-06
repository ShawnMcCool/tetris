<?php namespace Tetris;

final class Vector
{
    public function __construct(
        private int $x,
        private int $y
    ) {
    }

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }

    public function equals(self $that): bool
    {
        return $this->x == $that->x && $this->y == $that->y;
    }

    public function add(self $addend): self
    {
        return new self(
            $this->x + $addend->x,
            $this->y + $addend->y
        );
    }

    public function times(int $multiplier): self
    {
        return new self(
            $this->x * $multiplier,
            $this->y * $multiplier,
        );
    }

    public static function fromInt(int $x, int $y): self
    {
        return new self($x, $y);
    }

    public static function zero(): self
    {
        return new self(0, 0);
    }

    public static function one(): self
    {
        return new self(1, 1);
    }

    public static function down(): self
    {
        return new self(0, 1);
    }
}