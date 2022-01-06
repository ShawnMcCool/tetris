<?php namespace Tetris\UI\Gameplay;

use Tetris\Mino;
use Tetris\Vector;
use Tetris\Matrix;
use Tetris\Tetrimino;
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
    private ?Matrix $matrix = null;
    private ?Tetrimino $tetrimino = null;

    function handle($event)
    {
        if ($event instanceof GameWasStarted) {
            $this->matrix = $event->matrix;
            $this->render();
        } elseif ($event instanceof TetriminoWasSpawned) {
            $this->tetrimino = $event->tetrimino;
            $this->render();
        } elseif ($event instanceof TetriminoFell) {
            $this->tetrimino = $event->tetrimino;
            $this->render();
        } elseif ($event instanceof TetriminoWasRotated) {
            $this->tetrimino = $event->tetrimino;
            $this->render();
        } elseif ($event instanceof TetriminoWasMoved) {
            $this->tetrimino = $event->tetrimino;
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
        $dimensions = $this->matrix->dimensions();

        set_cursor_position(0, 0);
        echo str_repeat('#', $dimensions->x() + 2) . "\n";
        foreach (range(1, $dimensions->y()) as $i) {
            echo '#' . str_repeat(' ', $dimensions->x()) . "#\n";
        }
        echo str_repeat('#', $dimensions->x() + 2) . "\n";
        
        /** @var Mino $mino */
        foreach ($this->matrix->minos() as $mino) {
            set_cursor_position(
                $mino->position()->y(),
                $mino->position()->x(),
            );
            echo '0';
        }
    }

    private function renderActiveTetrimino()
    {
        if ( ! $this->tetrimino) {
            return;
        }
        
        $minos = $this->tetrimino->minosInMatrixSpace()->translate(Vector::fromInt(2, 2));
        
        /** @var Mino $mino */
        foreach ($minos->toArray() as $mino) {
            set_cursor_position(
                $mino->position()->y(),
                $mino->position()->x(),
            );
            
            echo 'O';
        }
    }
}