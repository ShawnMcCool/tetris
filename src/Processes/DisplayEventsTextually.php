<?php namespace Tetris\Processes;

use Tetris\Vector;
use Tetris\Events\GameWasStarted;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasRotated;
use Tetris\EventDispatch\EventListener;
use function PhAnsi\set_cursor_position;

final class DisplayEventsTextually implements EventListener
{
    private Vector $matrixDimensions;

    function handle($event)
    {
        if ($event instanceof GameWasStarted) {
            $this->matrixDimensions = $event->matrix->dimensions();
            $this->output("The game started.");
        } elseif ($event instanceof TetriminoWasMoved) {
            $this->output("Tetrimino was moved {$event->direction->toString()}.");
        } elseif ($event instanceof TetriminoWasRotated) {
            $this->output("Tetrimino was rotated {$event->direction->toString()}.");
        } else {
            $this->output(get_class($event));
        }
    }

    private function output(string $text): void
    {
        set_cursor_position(
            $this->matrixDimensions->y() + 3, 0
        );
        echo $text;
    }
}