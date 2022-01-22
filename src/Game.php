<?php namespace Tetris;

use Tetris\Events\GameLevelIncreased;
use Tetris\Events\GameWasStarted;
use Tetris\Events\LinesWereCleared;
use Tetris\Events\PlayerLostTheGame;
use Tetris\Events\TetriminoBecameLocked;
use Tetris\Events\TetriminoFell;
use Tetris\Events\TetriminoWasMoved;
use Tetris\Events\TetriminoWasRotated;
use Tetris\Events\TetriminoWasSpawned;
use Tetris\Time\NonBlockingTimer;

final class Game
{
    private array $pendingEvents = [];
    private ?Tetrimino $tetrimino = null;
    private LineScore $score;
    private bool $gameIsOver = false;

    public function __construct(
        private Matrix           $matrix,
        private TetriminoBag     $bag,
        private NonBlockingTimer $gravityTimer
    )
    {
        $this->score = LineScore::empty();
    }

    private function processGravity(): void
    {
        if ($this->gameIsOver) {
            return;
        }
        if (!$this->tetrimino) {
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

        $this->lockTetrimino($this->tetrimino);
    }

    public function movePiece(Direction $direction): void
    {
        if ($this->gameIsOver) {
            return;
        }
        if (!$this->tetrimino) {
            return;
        }

        // actually do the move
        $newlyPositionedTetrimino = $this->tetrimino->translate(
            $direction->isLeft()
                ? Vector::fromInt(-1, 0)
                : Vector::fromInt(1, 0)
        );

        if (!$this->matrix->canFit($newlyPositionedTetrimino)) {
            return;
        }

        // actually make the move
        $this->tetrimino = $newlyPositionedTetrimino;

        $this->pendingEvents[] = new TetriminoWasMoved(
            $this->tetrimino,
            $direction
        );
    }

    public function hardDrop(): void
    {
        if ($this->gameIsOver) {
            return;
        }
        if (!$this->tetrimino) {
            return;
        }

        $translationVector = Vector::zero();
        $targetDropTranslation = Vector::zero();

        // 1. determine the piece's position
        while ($this->matrix->canFit($this->tetrimino->translate($translationVector))
        ) {
            $targetDropTranslation = $translationVector;
            $translationVector = $translationVector->add(Vector::fromInt(0, 1));
        }

        $this->lockTetrimino(
            $this->tetrimino->translate($targetDropTranslation)
        );
    }

    public function rotatePiece(Direction $direction): void
    {
        if ($this->gameIsOver) {
            return;
        }
        if (!$this->tetrimino) {
            return;
        }

        $newlyRotatedTetrimino = $this->tetrimino->rotate($direction);

        if (!$this->matrix->canFit($newlyRotatedTetrimino)) {
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
        if ($this->gameIsOver) {
            return;
        }
        if ($this->tetrimino) {
            return;
        }

        $this->tetrimino = $this->bag
            ->draw()
            ->translate(
                $this->matrix->spawnPosition()
            );

        $this->pendingEvents[] = new TetriminoWasSpawned(
            $this->tetrimino,
            $this->bag->next()
        );

        // end the game
        if (!$this->matrix->canFit($this->tetrimino)) {
            $this->gameIsOver = true;
            $this->pendingEvents[] = new PlayerLostTheGame($this->matrix);
        }
    }

    public function flushEvents(): array
    {
        $pendingEvents = $this->pendingEvents;
        $this->pendingEvents = [];
        return $pendingEvents;
    }

    public static function start(
        Matrix           $matrix,
        TetriminoBag     $bag,
        NonBlockingTimer $gravityTimer
    ): self
    {
        $game = new self($matrix, $bag, $gravityTimer);

        $gravityTimer->onTick(
            fn () => $game->processGravity()
        );

        $gravityTimer->start();

        $game->pendingEvents[] = new GameWasStarted($matrix);
        return $game;
    }

    private function lockTetrimino(Tetrimino $tetrimino): void
    {
        // otherwise, lock it into place
        $this->matrix = $this->matrix->lock($tetrimino);

        $this->pendingEvents[] = new TetriminoBecameLocked(
            $tetrimino,
            $this->matrix,
        );

        $this->tetrimino = null;

        // check if there are line clears

        $this->clearLines();

        /*
        - does any row have x minos (x = full width)
        - remove all minos in that row
        - one by one drop rows
        */
    }

    private function clearLines(): void
    {
        if ($this->matrix->canClearLines()) {
            $preClearLevel = GameLevel::forScore($this->score);

            $linesToClear = $this->matrix->linesToClear();
            $this->matrix = $this->matrix->clearLines();

            $this->score = $this->score->plus(count($linesToClear));

            $this->pendingEvents[] = new LinesWereCleared(
                $linesToClear,
                $this->score,
                $this->matrix
            );

            $postClearLevel = GameLevel::forScore($this->score);

            if ( ! $preClearLevel->equals($postClearLevel)) {
                $this->pendingEvents[] = new GameLevelIncreased(
                    $postClearLevel->toInteger()
                );
                $this->gravityTimer = $this->gravityTimer->withNewInterval(
                    $postClearLevel->gravityIntervalSeconds()
                );
                $this->gravityTimer->start();
            }
        }
    }

    public function tick(): void
    {
        $this->gravityTimer->tick();
    }
}