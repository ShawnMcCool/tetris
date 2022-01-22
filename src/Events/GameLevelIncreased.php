<?php

declare(strict_types=1);

namespace Tetris\Events;

final class GameLevelIncreased
{
    public function __construct(
        public int $newLevel
    ) {
    }
}