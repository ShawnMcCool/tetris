<?php namespace Tetris\Processes;

use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasRotated;
use Tetris\EventDispatch\EventListener;

final class DisplayEventsTextually implements EventListener
{
    function handle($event)
    {
        if ($event instanceof TetriminoWasMoved) {
            echo "Tetrimino was moved {$event->direction()->toString()}.\n";
        } elseif ($event instanceof TetriminoWasRotated) {
            echo "Tetrimino was rotated {$event->direction()->toString()}.\n";
        } else {
            echo get_class($event) . "\n";
        }
    }
}