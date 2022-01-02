<?php namespace Tetris\UI\Gameplay;

use Tetris\Vector;
use Tetris\Playfield;
use Tetris\ActiveTetrimino;
use Tetris\Events\TetriminoFell;
use Tetris\Events\GameWasStarted;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasSpawned;
use Tetris\Events\TetriminoWasRotated;
use Tetris\EventDispatch\EventListener;
use function PhAnsi\clear_screen;
use function PhAnsi\set_cursor_position;

final class Render implements EventListener
{
    private ?Playfield $playfield = null;
    private ?ActiveTetrimino $activeTetrimino = null;

    function handle($event)
    {
        if ($event instanceof GameWasStarted) {
            $this->playfield = $event->matrix();
            $this->render();
        } elseif ($event instanceof TetriminoWasSpawned) {
            $this->activeTetrimino = $event->tetrimino();
            $this->render();
        } elseif ($event instanceof TetriminoFell) {
            $this->activeTetrimino = $event->tetrimino();
            $this->render();
        } elseif ($event instanceof TetriminoWasRotated) {
            $this->activeTetrimino = $event->tetrimino();
            $this->render();
        } elseif ($event instanceof TetriminoWasMoved) {
            $this->activeTetrimino = $event->tetrimino();
            $this->render();
        }
    }

    private function render()
    {
        clear_screen();

        $this->renderTheMatrix();
        $this->renderActiveTetrimino();
    }

    private function renderTheMatrix()
    {
        $dimensions = $this->playfield->dimensions();

        set_cursor_position(0, 0);
        echo str_repeat('#', $dimensions->x() + 2) . "\n";
        foreach (range(1, $dimensions->y()) as $i) {
            echo '#' . str_repeat(' ', $dimensions->x()) . "#\n";
        }
        echo str_repeat('#', $dimensions->x() + 2) . "\n";
    }

    private function renderActiveTetrimino()
    {
        if ( ! $this->activeTetrimino) {
            return;
        }

        $minoPositions = $this->activeTetrimino->matrix()->minoPositions();

        /** @var Vector $mino */
        foreach ($minoPositions as $mino) {
            $minoPosition = $mino
                ->plus(Vector::fromInt(1, 1))
                ->plus($this->activeTetrimino->matrixPosition());

            set_cursor_position(
                $minoPosition->y(),
                $minoPosition->x()
            );
            
            echo "I";
        }
    }
}