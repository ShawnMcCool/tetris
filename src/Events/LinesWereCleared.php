<?php namespace Tetris\Events;

use Tetris\Playfield;

final class LinesWereCleared
{
    public function __construct(
        private array $clearedRowNumbers,
        private Playfield $resultingMatrix
    ) {
    }
}