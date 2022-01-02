<?php namespace Tetris\Events;

use Tetris\Direction;
use Tetris\ActiveTetrimino;

final class TetriminoWasRotated
{
    public function __construct(
        private ActiveTetrimino $tetrimino,
        private Direction $direction,
    ) {
    }

    public function tetrimino(): ActiveTetrimino
    {
        return $this->tetrimino;
    }

    public function direction(): Direction
    {
        return $this->direction;
    }
}