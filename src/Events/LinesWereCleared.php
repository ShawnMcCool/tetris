<?php namespace Tetris\Events;

use Tetris\LineScore;
use Tetris\Matrix;

final class LinesWereCleared
{
    public function __construct(
        public array $clearedRowNumbers,
        public LineScore $score,
        public Matrix $resultingMatrix
    ) {
    }
}