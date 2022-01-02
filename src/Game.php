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
    private ?ActiveTetrimino $activeTetrimino = null;

    public function __construct(
        private Playfield $playfield,
        private TetriminoBag $bag,
    ) {
    }

    public function processGravity(): void
    {
        if ( ! $this->activeTetrimino) {
            return;
        }

        $newlyPositionedTetrimino = $this->activeTetrimino->downOne();

        // if it can fit
        if ($this->playfield->canFit($newlyPositionedTetrimino)) {
            // make the change
            $this->activeTetrimino = $newlyPositionedTetrimino;

            // 
            $this->pendingEvents[] = new TetriminoFell(
                $this->activeTetrimino
            );

            return;
        }

        // otherwise lock it into place
        $this->playfield->lockTetrimino($this->activeTetrimino);

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

        // actually do the move
        $newlyPositionedTetrimino = $this->activeTetrimino->translate(
            $direction->isLeft()
                ? Vector::fromInt(-1, 0)
                : Vector::fromInt(1, 0)
        );

        if ( ! $this->playfield->canFit($newlyPositionedTetrimino)) {
            return;
        }

        // actually make the move
        $this->activeTetrimino = $newlyPositionedTetrimino;

        $this->pendingEvents[] = new TetriminoWasMoved(
            $this->activeTetrimino,
            $direction
        );
    }

    public function rotatePiece(Direction $direction): void
    {
        if ( ! $this->activeTetrimino) {
            return;
        }

        $newlyRotatedTetrimino = $this->activeTetrimino->rotate($direction);
        
        if ( ! $this->playfield->canFit($newlyRotatedTetrimino)) {
            return;
        }

        // actually do the rotation
        $this->activeTetrimino = $newlyRotatedTetrimino;
        
        $this->pendingEvents[] = new TetriminoWasRotated(
            $this->activeTetrimino,
            $direction
        );
    }

    public function spawnTetrimino(): void
    {
        if ($this->activeTetrimino) {
            return;
        }
        
        $this->activeTetrimino = new ActiveTetrimino(
            $this->bag->draw(),
            $this->playfield->tetriminoSpawnPosition()
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
        Playfield $matrix,
        TetriminoBag $bag
    ): self {
        $game = new self($matrix, $bag);
        $game->pendingEvents[] = new GameWasStarted($matrix);
        return $game;
    }
}