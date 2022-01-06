<?php namespace Tetris\Events;

use Tetris\Matrix;
use Tetris\Tetrimino;

final class TetriminoBecameLocked
{
    public function __construct(
        public Tetrimino $tetrimino,
        public Matrix $resultingMatrix,
    ) {
    }
}