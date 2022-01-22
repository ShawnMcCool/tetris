<?php namespace Tetris\Processes;

use Tetris\Events\GameLevelIncreased;
use Tetris\LineScore;
use Tetris\Matrix;
use Tetris\Tetrimino;
use Tetris\Events\TetriminoFell;
use Tetris\Events\GameWasStarted;
use Tetris\UI\Display\AnsiDisplay;
use Tetris\Events\LinesWereCleared;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasRotated;
use Tetris\Events\TetriminoWasSpawned;
use Tetris\EventDispatch\EventListener;
use Tetris\Events\TetriminoBecameLocked;
use function PhAnsi\clear_screen;

final class RenderWithCanvas implements EventListener
{
    private Matrix $matrix;
    private Tetrimino $tetrimino;
    private AnsiDisplay $display;
    private int $score = 0;
    private int $level = 1;
    private ?Tetrimino $nextTetrimino = null;

    function handle($event)
    {
        if ($event instanceof GameWasStarted) {
            $this->matrix = $event->matrix;

            $this->display = AnsiDisplay::withConfiguration($this->matrix->dimensions());

            $this->display->wallShader(
                fn(int $x, int $y) => 'white'
            );

            clear_screen();
            $this->render();
        } elseif ($event instanceof TetriminoWasSpawned) {
            $this->tetrimino = $event->tetrimino;
            $this->nextTetrimino = $event->nextTetrimino;
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
            $this->score = $event->score->toInteger();
            $this->render();
        } elseif ($event instanceof GameLevelIncreased) {
            $this->level = $event->newLevel;
        }
    }

    private function render(): void
    {
        $this->display->render(
            $this->matrix,
            $this->tetrimino ?? null,
            $this->nextTetrimino,
            $this->score,
            $this->level
        );
    }
}