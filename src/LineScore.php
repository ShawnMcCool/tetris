<?php

declare(strict_types=1);

namespace Tetris;

final class LineScore
{
    private function __construct(
        private int $score
    ) {
    }

    public static function empty(): self
    {
        return new self(0);
    }

    public function plus(int $count): self
    {
        return new self($this->score + $count);
    }

    public function toInteger(): int
    {
        return $this->score;
    }

    public function isGreaterThan(int $that): bool
    {
        return $this->score > $that;
    }
}