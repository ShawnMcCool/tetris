<?php namespace Tetris\Events;

use Tetris\ActiveTetrimino;

final class TetriminoBecameLocked
{
    public function __construct(
        private ActiveTetrimino $tetrimino
    ) {
    }
}