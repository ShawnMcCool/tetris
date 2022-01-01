<?php namespace Tetris\Events;

use Tetris\Position;
use Tetris\Tetrimino;
use Tetris\Direction;

final class TetriminoWasMoved
{
    public function __construct(
        private Tetrimino $tetrimino,
        private Direction $direction,
        private Position $newPosition,
    ) {
    }
}