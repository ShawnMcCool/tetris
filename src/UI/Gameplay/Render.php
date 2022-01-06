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
use Tetris\Events\TetriminoBecameLocked;
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
        } elseif ($event instanceof TetriminoBecameLocked) {
            $this->matrix = $event->resultingMatrix;
            $this->render();
        }
    }

    private function render(): void
    {
        clear_screen();
        
        $this->renderMatrix();
        $this->renderTetrimino();
    }

    private function renderMatrix()
    {
        $dimensions = $this->matrix->dimensions();
        $frameMargin = Vector::fromInt(1, 1);

        /*
         * ceiling
         */
        $this->draw(
        // top left of the screen
            Vector::one(),
            // a bar all the way across including
            // over the 2 sides of the frame
            str_repeat(
                '-',
                $dimensions->add(
                    $frameMargin->times(2)
                )->x()
            )
        );

        // draw left wall
        foreach (range(1, $dimensions->y()) as $i) {
            $this->draw(
                Vector::fromInt(
                    1,
                    $i + $frameMargin->y()
                ),
                '|'
            );
        }

        // draw right wall
        foreach (range(1, $dimensions->y()) as $i) {
            $this->draw(
                Vector::fromInt(
                    $dimensions->add($frameMargin->times(2))->x(),
                    $i + $frameMargin->y()
                ),
                '|'
            );
        }

        // draw floor
        $this->draw(
        // bottom left of the screen
            Vector::fromInt(
                0,
                $dimensions->add($frameMargin->times(2))->y(),
            ),
            // a bar all the way across including
            // over the 2 sides of the frame
            str_repeat(
                '-',
                $dimensions->add(
                    $frameMargin->times(2)
                )->x()
            )
        );

        /** @var Mino $mino */
        foreach ($this->matrix->minos()->toArray() as $mino) {
            $this->draw(
                $mino->position()->add($frameMargin->times(2)),
                '0'
            );
        }
    }

    private function renderTetrimino()
    {
        if ( ! $this->tetrimino) {
            return;
        }
        
        $minos = $this->tetrimino
            ->minosInMatrixSpace()
            ->translate(
                Vector::fromInt(2, 2)
            );

        /** @var Mino $mino */
        foreach ($minos->toArray() as $mino) {
            $this->draw($mino->position(), 'O');
        }
    }

    private function draw(Vector $position, string $text): void
    {
        set_cursor_position(
            $position->y(),
            $position->x(),
        );
        echo $text;
    }
}