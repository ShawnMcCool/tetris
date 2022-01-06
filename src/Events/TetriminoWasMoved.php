<?php namespace Tetris\Events;

use Tetris\Direction;
use Tetris\Tetrimino;

final class TetriminoWasMoved
{
    public function __construct(
        public Tetrimino $tetrimino,
        public Direction $direction,
    ) {
    }
}