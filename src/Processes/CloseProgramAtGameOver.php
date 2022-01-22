<?php

declare(strict_types=1);

namespace Tetris\Processes;

use Tetris\EventDispatch\EventListener;
use Tetris\Events\PlayerLostTheGame;

final class CloseProgramAtGameOver implements EventListener
{
    function handle($event)
    {
        if ($event instanceof PlayerLostTheGame) {
            die("\n\nGAME OVER\n\n");
        }
    }
}