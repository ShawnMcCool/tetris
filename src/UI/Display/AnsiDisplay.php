<?php namespace Tetris\UI\Display;

use Closure;
use Tetris\Mino;
use Tetris\Minos;
use Tetris\Vector;
use Tetris\Matrix;
use Tetris\Tetrimino;
use Tetris\ShapeName;
use function Thermage\canvas;
use function PhAnsi\set_cursor_position;

final class AnsiDisplay
{
    private array $renderMatrix;
    private Vector $totalSize;
    private Vector $matrixPosition;

    private function __construct(
        private Vector $size,
        private Vector $padding,
        private Vector $wallThickness,
        private Closure $wallShader,
    ) {
        $this->totalSize = $this->padding
            ->add($this->wallThickness->times(2))
            ->add($this->size);

        $this->matrixPosition = $this->padding->add($this->wallThickness);
    }

    public function render(
        Matrix $matrix,
        ?Tetrimino $tetrimino,
    ): void {
        $this->clearRenderMatrix();
        
        // update the render matrix
        $this->blitWalls();
        $this->blitMinos($matrix->minos());

        if ($tetrimino) {
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
            array_fill(0, $this->totalSize->x(), '')
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

    private function blitMinos(Minos $minos): void
    {
        /** @var Mino $mino */
        foreach ($minos->toArray() as $mino) {
            $this->blitPixel(
                $this->matrixPosition->add($mino->position()),
                $this->colorForShape($mino->shapeName()),
            );
        }
    }

    private function colorForShape(ShapeName $shapeName): string
    {
        return match ($shapeName->toString()) {
            'i' => 'bright-blue',
            't' => 'magenta',
            'j' => 'blue',
            'l' => 'bright-red',
            'o' => 'yellow',
            's' => 'green',
            'z' => 'red',
            default => 'gray',
        };
    }

    public static function withConfiguration(
        Vector $size,
        ?Vector $wallThickness = null,
        ?Vector $padding = null,
        ?Closure $wallShader = null,
    ): self {
        return new self(
            $size,
            $wallThickness ?? Vector::one(),
            $padding ?? Vector::one(),
            $wallShader ?? fn($x) => $x,
        );
    }
}