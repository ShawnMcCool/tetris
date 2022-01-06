<?php namespace Tetris\Events;

use Tetris\Matrix;

final class LinesWereCleared
{
    public function __construct(
        public array $clearedRowNumbers,
        public Matrix $resultingMatrix
    ) {
    }
}