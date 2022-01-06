<?php namespace Tetris\Events;

use Tetris\Tetrimino;

final class TetriminoWasSpawned
{
    public function __construct(
        public Tetrimino $tetrimino,
    ) {
    }
}