<?php namespace Tetris;

final class Direction
{
    private function __construct(
        private string $direction
    ) {
    }

    public function isLeft(): bool
    {
        return $this->direction == 'left';
    }

    public function isRight(): bool
    {
        return $this->direction == 'right';
    }

    public static function left(): self
    {
        return new self('left');
    }

    public static function right(): self
    {
        return new self('right');
    }

    public function toString(): string
    {
        return $this->direction;
    }
}