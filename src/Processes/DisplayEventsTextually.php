<?php namespace Tetris\Processes;

use Tetris\Vector;
use Tetris\Events\TetriminoFell;
use Tetris\Events\GameWasStarted;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasRotated;
use Tetris\EventDispatch\EventListener;
use function PhAnsi\set_cursor_position;

final class DisplayEventsTextually implements EventListener
{
    /*
     * magic numbers, yay
     */
    private const LOG_LENGTH = 3;
    private const FRAME_MARGINS = 5;

    private Vector $matrixDimensions;
    private array $eventLog = [];

    function handle($event)
    {
        if ($event instanceof GameWasStarted) {
            $this->matrixDimensions = $event->matrix->dimensions();
            $this->display("The game started.");
        } elseif ($event instanceof TetriminoWasMoved) {
            $this->display("Tetrimino was moved {$event->direction->toString()}.");
        } elseif ($event instanceof TetriminoWasRotated) {
            $this->display("Tetrimino was rotated {$event->direction->toString()}.");
        } elseif ($event instanceof TetriminoFell) {
            /*
             * it's not as interesting to render 'fell' over and over
             */
            $this->renderLog();
        } else {
            $this->display(get_class($event));
        }
    }

    private function display(string $text): void
    {
        $this->updateLog($text);
        $this->renderLog();
    }

    private function updateLog(string $text): void
    {
        /*
         * append event to log
         */
        $this->eventLog[] = $text;

        /*
         * reduce log to the last N events
         */
        $this->eventLog = array_slice($this->eventLog, -3);
    }

    private function renderLog(): void
    {
        set_cursor_position(
            $this->matrixDimensions->y() + self::LOG_LENGTH + self::FRAME_MARGINS,
            0
        );

        echo implode("\n", $this->eventLog);
    }
}