<?php namespace Tetris\Events;

use Tetris\Playfield;

final class GameWasStarted
{
    public function __construct(
        private Playfield $matrix
    ) {
    }

    public function matrix(): Playfield
    {
        return $this->matrix;
    }
}