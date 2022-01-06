<?php namespace Tetris\Processes;

use Tetris\Game;
use Tetris\Events\GameWasStarted;
use Tetris\EventDispatch\EventListener;
use Tetris\Events\TetriminoBecameLocked;

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
        } elseif ($event instanceof TetriminoBecameLocked) {
            $this->game->spawnTetrimino();
        }
    }
}