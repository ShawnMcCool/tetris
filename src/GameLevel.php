<?php

declare(strict_types=1);

namespace Tetris;

final class GameLevel
{
    private function __construct(
        private int $level
    ) {
    }

    public static function fromInt(int $level): self
    {
        return new self($level);
    }

    public static function forScore(LineScore $score): self
    {
        if ($score->toInteger() >= 15) {
            return GameLevel::fromInt(4);
        } elseif ($score->toInteger() >= 10) {
            return GameLevel::fromInt(3);
        } elseif ($score->toInteger() >= 5) {
            return GameLevel::fromInt(2);
        }

        return GameLevel::fromInt(1);
    }

    public function gravityIntervalSeconds(): float
    {
        return match ($this->level) {
            1 => 0.8,
            2 => 0.5,
            3 => 0.3,
            4 => 0.1,
        };
    }

    public function equals(self $that): bool
    {
        return $this->level == $that->level;
    }

    public function toInteger(): int
    {
        return $this->level;
    }
}