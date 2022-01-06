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
        
        // draw ceiling
        $this->drawCharacter(
            Vector::fromInt(0, 1),
            str_repeat('-', $dimensions->x() + 2)
        );
        
        // draw left wall
        foreach (range(2, $dimensions->y()+2) as $i) {
            $this->drawCharacter(
                Vector::fromInt(0, $i),
                '|'
            );
        }
        
        // draw left wall
        foreach (range(2, $dimensions->y()+2) as $i) {
            $this->drawCharacter(
                Vector::fromInt($dimensions->x()+2, $i),
                '|'
            );
        }

        // draw floor
        $this->drawCharacter(
            Vector::fromInt(0,$dimensions->y()+2),
            str_repeat('-', $dimensions->x() + 2)
        );

        /** @var Mino $mino */
        foreach ($this->matrix->minos() as $mino) {
            $this->drawCharacter($mino->position(), '0');
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
            $this->drawCharacter($mino->position(), 'O');
        }
    }

    private function drawCharacter(Vector $position, string $character): void
    {
        set_cursor_position(
            $position->y(),
            $position->x(),
        );
        echo $character;
    }
}