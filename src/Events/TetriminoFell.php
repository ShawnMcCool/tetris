<?php namespace Tetris\Events;

use Tetris\Tetrimino;

final class TetriminoFell
{
    public function __construct(
        public Tetrimino $tetrimino,
    ) {
    }
}