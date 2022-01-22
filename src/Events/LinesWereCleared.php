<?php namespace Tetris\Events;

use Tetris\Matrix;
use Tetris\LineScore;

final class LinesWereCleared
{
    public function __construct(
        public array $clearedRowNumbers,
        public LineScore $score,
        public Matrix $resultingMatrix
    ) {
    }
}