<?php namespace Tetris\UI\Display;

use Closure;
use Tetris\Matrix;
use Tetris\Mino;
use Tetris\Minos;
use Tetris\ShapeName;
use Tetris\Tetrimino;
use Tetris\Vector;
use function PhAnsi\set_cursor_position;
use function Thermage\canvas;

final class AnsiDisplay
{
    private array $renderMatrix;
    private Vector $totalSize;
    private Vector $matrixPosition;
    private ?Tetrimino $previousNext = null;
    private Vector $nextMinoTranslation;

    private function __construct(
        private Vector          $size,
        private Vector          $padding,
        private Vector          $wallThickness,
        private Closure         $wallShader,
        private TetriminoColors $colors,
    )
    {
        $this->totalSize = $this->padding
            ->add($this->wallThickness->times(2))
            ->add($this->size);

        $this->matrixPosition = $this->padding->add($this->wallThickness);

        $this->nextMinoTranslation = Vector::fromInt(12, 3);
    }

    public function render(
        Matrix     $matrix,
        ?Tetrimino $tetrimino,
        ?Tetrimino $nextTetrimino,
        int        $score,
        int        $level
    ): void
    {
        $this->clearRenderMatrix();

        // update the render matrix
        $this->blitWalls();
        $this->blitMinos($matrix->minos());

        if ($tetrimino) {
            if ($this->previousNext) {
                $this->clearNextDisplay($this->previousNext);
            }

            $this->previousNext = $nextTetrimino;
            $this->blitMinos($nextTetrimino->minos(), $this->nextMinoTranslation);

            $this->blitGhostPieceFor($tetrimino, $matrix);
            $this->blitMinos($tetrimino->minosInMatrixSpace());
        }

        // render to the terminal
        set_cursor_position(0, 0);
        echo canvas()
            ->size(
                $this->totalSize->x(),
                $this->totalSize->y(),
            )->pixels(
                $this->renderMatrix
            );

        $this->blitScore($score);
        $this->blitLevel($level);
    }

    public function wallShader(Closure $f): void
    {
        $this->wallShader = $f;
    }

    private function clearRenderMatrix(): void
    {
        $this->renderMatrix = array_fill(
            0,
            $this->totalSize->y(),
            // empty array of columns
            array_fill(0, $this->totalSize->x() + 5, '')
        );
    }

    private function blitWalls()
    {
        // don't blit walls if there aren't any
        if ($this->wallThickness->equals(Vector::zero())) {
            return;
        }

        // ceiling
        foreach (
            range(
                0, $this->padding
                ->add($this->size)
                ->x()
            ) as $x
        ) {
            $this->blitPixel(
                $this->padding->add(
                    Vector::fromInt($x, 0)
                ),
                ($this->wallShader)($x, 0)
            );
        }

        // floor
        foreach (range(0, $this->size->x()) as $x) {
            $this->blitPixel(
                $this->padding->add(
                    Vector::fromInt(
                        $x,
                        $this->size->add($this->wallThickness)->y()
                    )
                ),
                ($this->wallShader)($x, $this->size->y())
            );
        }

        // left wall
        foreach (range(0, $this->size->y()) as $y) {
            $this->blitPixel(
                $this->padding->add(
                    Vector::fromInt(
                        0,
                        $this->padding->y() + $y
                    )
                ),
                ($this->wallShader)(0, $y)
            );
        }

        // right wall
        foreach (range(0, $this->size->y()) as $y) {
            $this->blitPixel(
                Vector::fromInt(
                    $this->padding->add($this->wallThickness)->add($this->size)->x(),
                    $this->padding->add($this->wallThickness)->y() + $y,
                ),
                ($this->wallShader)($this->size->x(), $y)
            );
        }
    }

    private function blitPixel(Vector $position, string $color): void
    {
        $this->renderMatrix[$position->y()][$position->x()] = $color;
    }

    private function blitMinos(
        Minos   $minos,
        ?Vector $translation = null
    ): void
    {
        if ( ! $translation) {
            $translation = Vector::zero();
        }

        /** @var Mino $mino */
        foreach ($minos->toArray() as $mino) {
            $this->blitPixel(
                $this->matrixPosition->add($mino->position())->add($translation),
                $this->colors->forShape($mino->shapeName()),
            );
        }
    }

    public static function withConfiguration(
        Vector           $size,
        ?Vector          $wallThickness = null,
        ?Vector          $padding = null,
        ?Closure         $wallShader = null,
        ?TetriminoColors $color = null,
    ): self
    {
        return new self(
            $size,
            $wallThickness ?? Vector::one(),
            $padding ?? Vector::one(),
            $wallShader ?? fn($x) => $x,
            $color ?? new TerminalSpecificTetriminoColors()
        );
    }

    private function blitGhostPieceFor(Tetrimino $tetrimino, Matrix $matrix): void
    {
        $translationVector = Vector::zero();
        $ghostPieceTranslation = Vector::zero();

        // 1. determine the piece's position
        while (
        $matrix->canFit($tetrimino->translate($translationVector))
        ) {
            $ghostPieceTranslation = $translationVector;
            $translationVector = $translationVector->add(Vector::fromInt(0, 1));
        }

        // 2. write to buffer
        $this->blitMinos(
            $tetrimino->translate($ghostPieceTranslation)
                ->minosInMatrixSpace()
                ->withNewShapeName(ShapeName::ghostPiece())
        );
    }

    private function blitScore(int $score)
    {
        set_cursor_position(15, 45);
        echo "score";
        set_cursor_position(16, 47);
        echo $score;
    }

    private function blitLevel(int $level)
    {
        set_cursor_position(11, 45);
        echo "level";
        set_cursor_position(12, 47);
        echo $level;
    }

    private function clearNextDisplay(Tetrimino $previousNext)
    {
        /** @var Mino $mino */
        foreach ($previousNext->minos()->toArray() as $mino) {
            $this->blitPixel(
                $this->matrixPosition->add($mino->position())->add($this->nextMinoTranslation),
                $this->colors->forShape($mino->shapeName()),
            );
        }
    }
}