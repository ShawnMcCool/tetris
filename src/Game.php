<?php namespace Tetris;

use Tetris\Events\TetriminoFell;
use Tetris\Events\GameWasStarted;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasSpawned;
use Tetris\Events\TetriminoWasRotated;
use Tetris\Events\TetriminoBecameLocked;

final class Game
{
    private array $pendingEvents = [];
    private ?Tetrimino $tetrimino = null;

    public function __construct(
        private Matrix $matrix,
        private TetriminoBag $bag,
    ) {
    }

    public function processGravity(): void
    {
        if ( ! $this->tetrimino) {
            return;
        }

        $newlyPositionedTetrimino = $this->tetrimino->translate(
            Vector::down()
        );

        // if it can fit
        if ($this->matrix->canFit($newlyPositionedTetrimino)) {
            // make the change
            $this->tetrimino = $newlyPositionedTetrimino;

            // 
            $this->pendingEvents[] = new TetriminoFell(
                $this->tetrimino
            );

            return;
        }

        // otherwise, lock it into place
        $this->matrix = $this->matrix->lock($this->tetrimino);

        $this->pendingEvents[] = new TetriminoBecameLocked(
            $this->tetrimino
        );

        $this->tetrimino = null;
    }

    public function movePiece(Direction $direction): void
    {
        if ( ! $this->tetrimino) {
            return;
        }

        // actually do the move
        $newlyPositionedTetrimino = $this->tetrimino->translate(
            $direction->isLeft()
                ? Vector::fromInt(-1, 0)
                : Vector::fromInt(1, 0)
        );

        if ( ! $this->matrix->canFit($newlyPositionedTetrimino)) {
            return;
        }

        // actually make the move
        $this->tetrimino = $newlyPositionedTetrimino;

        $this->pendingEvents[] = new TetriminoWasMoved(
            $this->tetrimino,
            $direction
        );
    }

    public function rotatePiece(Direction $direction): void
    {
        if ( ! $this->tetrimino) {
            return;
        }

        $newlyRotatedTetrimino = $this->tetrimino->rotate($direction);

        if ( ! $this->matrix->canFit($newlyRotatedTetrimino)) {
            return;
        }

        // actually do the rotation
        $this->tetrimino = $newlyRotatedTetrimino;

        $this->pendingEvents[] = new TetriminoWasRotated(
            $this->tetrimino,
            $direction
        );
    }

    public function spawnTetrimino(): void
    {
        if ($this->tetrimino) {
            return;
        }

        $this->tetrimino = $this->bag
            ->draw()
            ->translate(
                $this->matrix->spawnPosition()
            );

        $this->pendingEvents[] = new TetriminoWasSpawned(
            $this->tetrimino
        );
    }

    public function flushEvents(): array
    {
        $pendingEvents = $this->pendingEvents;
        $this->pendingEvents = [];
        return $pendingEvents;
    }

    public static function start(
        Matrix $matrix,
        TetriminoBag $bag
    ): self {
        $game = new self($matrix, $bag);
        $game->pendingEvents[] = new GameWasStarted($matrix);
        return $game;
    }
}