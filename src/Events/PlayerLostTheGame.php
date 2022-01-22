<?php

declare(strict_types=1);

namespace Tetris\Events;

use Tetris\Matrix;

final class PlayerLostTheGame
{
    public function __construct(
        public Matrix $matrix
    ) {
    }
}