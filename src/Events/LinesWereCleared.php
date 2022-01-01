<?php namespace Tetris\Events;

use Tetris\Matrix;

final class LinesWereCleared
{
    public function __construct(
        private array $clearedRowNumbers,
        private Matrix $resultingMatrix
    ) {
    }
}