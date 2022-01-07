<?php namespace Tetris\UI\Gameplay;

use Tetris\EventDispatch\EventListener;
use Tetris\Events\GameWasStarted;
use Tetris\Events\LinesWereCleared;
use Tetris\Events\TetriminoBecameLocked;
use Tetris\Events\TetriminoFell;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasRotated;
use Tetris\Events\TetriminoWasSpawned;
use Tetris\Matrix;
use Tetris\Mino;
use Tetris\Tetrimino;
use Tetris\Vector;
use function PhAnsi\clear_screen;
use function PhAnsi\set_cursor_position;

final class Render implements EventListener
{
    private Matrix $matrix;
    private Tetrimino $tetrimino;
    private Vector $wallMargins;
    private Vector $matrixPosition;

    public function __construct()
    {
        /*
         * 2 characters of padding on the left, one on the top of the matrix
         */
        $this->wallMargins = Vector::fromInt(2, 3);
        $this->matrixPosition = $this->wallMargins->add(
            Vector::fromInt(1, 1)
        );
    }

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
        } elseif ($event instanceof LinesWereCleared) {
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
        /*
         * render is 1 indexed, the top left character is at 1,1
         */
        $dimensions = $this->matrix->dimensions();

        $ceilingPosition = $this->wallMargins;

        $floorPosition = Vector::fromInt(
            $this->wallMargins->x(),
            $this->matrixPosition->add($dimensions)->y(),
        );

        $rightWallPosition = Vector::fromInt(
            $this->matrixPosition->add($dimensions)->x(),
            $this->wallMargins->y(),
        );

        $leftWallPosition = Vector::fromInt(
            $this->wallMargins->x(),
            $this->wallMargins->y(),
        );

        /*
         * ceiling
         */
        $this->draw(
            $ceilingPosition,
            // a bar all the way across including
            // over the 2 sides of the frame
            str_repeat(
                '-',
                $dimensions->add($this->wallMargins)->x()
            )
        );

        // draw left wall
        foreach (range(1, $dimensions->y()) as $i) {
            $this->draw(
                $leftWallPosition->add(Vector::fromInt(0, $i)),
                '|'
            );
        }

        // draw right wall
        foreach (range(1, $dimensions->y()) as $i) {
            $this->draw(
                $rightWallPosition->add(Vector::fromInt(0, $i)),
                '|'
            );
        }

        // draw floor
        $this->draw(
            $floorPosition,
            // a bar all the way across including
            // over the 2 sides of the frame
            str_repeat(
                '-',
                $dimensions->add($this->wallMargins)->x()
            )
        );

        /** @var Mino $mino */
        foreach ($this->matrix->minos()->toArray() as $mino) {
            $this->draw(
                $mino->position()
                    ->add(
                        $this->matrixPosition
                    ),
                '0'
            );
        }
    }

    private function renderTetrimino()
    {
        if (!isset($this->tetrimino)) {
            return;
        }

        $minos = $this->tetrimino
            ->minosInMatrixSpace()
            ->translate(
                $this->matrixPosition
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