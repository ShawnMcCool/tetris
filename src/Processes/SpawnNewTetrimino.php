<?php namespace Tetris\Processes;

use Tetris\Game;
use Tetris\Events\GameWasStarted;
use Tetris\EventDispatch\EventListener;

final class SpawnNewTetrimino implements EventListener
{
    public function __construct(
        private Game $game
    ) {
    }

    function handle($event)
    {
        if ($event instanceof GameWasStarted) {
            $this->game->spawnTetrimino();
        }
    }
}