<?php namespace Tetris\Events;

use Tetris\ActiveTetrimino;

final class TetriminoFell
{
    public function __construct(
        private ActiveTetrimino $tetrimino
    ) {
    }

    public function tetrimino(): ActiveTetrimino
    {
        return $this->tetrimino;
    }
}