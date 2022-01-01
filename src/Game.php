<?php namespace Tetris;

use Tetris\Events\TetriminoFell;
use Tetris\Events\GameWasStarted;
use Tetris\Events\TetriminoWasSpawned;
use Tetris\Events\TetriminoWasRotated;
use Tetris\Events\TetriminoBecameLocked;

final class Game
{
    private array $pendingEvents = [];
    private ?ActiveTetrimino $activeTetrimino;

    public function __construct(
        private Matrix $matrix,
        private TetriminoBag $bag,
    ) {
    }

    public function processGravity(): void
    {
        if ( ! $this->activeTetrimino) {
            return;
        }

        $newlyPositionedTetrimino = $this->activeTetrimino->downOne();

        if ($this->matrix->canFit($newlyPositionedTetrimino)) {
            $this->activeTetrimino = $newlyPositionedTetrimino;

            $this->pendingEvents[] = new TetriminoFell(
                $this->activeTetrimino
            );

            return;
        }

        $this->matrix->lock($this->activeTetrimino);

        $this->pendingEvents[] = new TetriminoBecameLocked(
            $this->activeTetrimino
        );

        $this->activeTetrimino = null;
    }

    public function movePiece(Direction $direction): void
    {
        if ( ! $this->activeTetrimino) {
            return;
        }
    }

    public function rotatePiece(Direction $direction): void
    {
        if ( ! $this->activeTetrimino) {
            return;
        }
        
        $this->activeTetrimino = $this->activeTetrimino->rotate($direction);
        
        $this->pendingEvents[] = new TetriminoWasRotated(
            $this->activeTetrimino,
            $direction
        );
    }

    public function spawnTetrimino(): void
    {
        $this->activeTetrimino = new ActiveTetrimino(
            $this->bag->draw(),
            $this->matrix->tetriminoSpawnPosition()
        );

        $this->pendingEvents[] = new TetriminoWasSpawned(
            $this->activeTetrimino
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