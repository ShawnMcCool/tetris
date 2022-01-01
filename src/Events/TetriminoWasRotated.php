<?php namespace Tetris\Events;

use Tetris\Direction;
use Tetris\ActiveTetrimino;

final class TetriminoWasRotated
{
    public function __construct(
        private ActiveTetrimino $tetrimino,
        private Direction $direction,
    )
    {
    }
}