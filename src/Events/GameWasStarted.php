<?php namespace Tetris\Events;

use Tetris\Matrix;

final class GameWasStarted
{
    public function __construct(
        public Matrix $matrix
    ) {
    }
}