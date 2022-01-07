<?php namespace Tetris;

final class Matrix
{
    private function __construct(
        private Vector $dimensions,
        private Minos  $minos,
        private Vector $spawnPosition
    )
    {
    }

    public function canFit(Tetrimino $tetrimino): bool
    {
        /*
         * boundary collisions
         */
        $boundaryCollisions = array_filter(
            $tetrimino->minosInMatrixSpace()->toArray(),
            function (Mino $mino) {
                // left wall
                if ($mino->position()->x() < 0) {
                    return true;
                }
                // right wall
                if ($mino->position()->x() >= $this->dimensions->x()) {
                    return true;
                }
                // ground
                if ($mino->position()->y() >= $this->dimensions->y()) {
                    return true;
                }
                return false;
            }
        );

        if (!empty($boundaryCollisions)) {
            return false;
        }

        /*
         * collisions with other minos
         */
        $minoCollisions = array_filter(
            $tetrimino->minosInMatrixSpace()->toArray(),
            fn(Mino $mino) => $this->minos->hasMino($mino)
        );

        return empty($minoCollisions);
    }

    public function lock(Tetrimino $tetrimino): self
    {
        return new self(
            $this->dimensions,
            $this->minos->add(
                $tetrimino->minosInMatrixSpace()
            ),
            $this->spawnPosition
        );
    }

    public function minos(): Minos
    {
        return $this->minos;
    }

    public function spawnPosition(): Vector
    {
        return $this->spawnPosition;
    }

    public function dimensions(): Vector
    {
        return $this->dimensions;
    }

    public static function withDimensions(
        int    $width,
        int    $height,
        Vector $spawnPosition
    ): self
    {
        return new self(
            Vector::fromInt($width, $height),
            Minos::empty(),
            $spawnPosition
        );
    }

    public function canClearLines(): bool
    {
        return !empty($this->linesToClear());
    }

    public function linesToClear(): array
    {
        $linesToClear = [];

        // foreach row
        foreach (range(0, $this->dimensions()->y()) as $rowNumber) {
            // is the row full of minos?
            if ($this->minos->countOfMinosInRow($rowNumber) == $this->dimensions->x()) {
                $linesToClear[] = $rowNumber;
            }
        }

        return $linesToClear;
    }

    public function clearLines(): self
    {
        // 1. remove all minos in the cleared lines
        $clearedRows = $this->linesToClear();

        $this->minos = $this->minos->filter(
            fn(Mino $mino) => !in_array($mino->position()->y(), $clearedRows)
        );

        $newMinos = $this->minos()->clone();

        // 2. starting with the furthest row down (highest y value)
        foreach (range($this->dimensions()->y() - 1, 0) as $rowNumber) {
            // if the row is empty
            if ($newMinos->countOfMinosInRow($rowNumber) == 0) {
                // move the first line above it into the empty row
                $firstRowAboveWithMinos = $newMinos->nextRowAboveWithMinos($rowNumber);

                if (is_null($firstRowAboveWithMinos)) {
                    return new Matrix(
                        $this->dimensions,
                        $newMinos,
                        $this->spawnPosition
                    );
                }

                $newMinos = $newMinos->map(
                    fn(Mino $mino) => $mino->position()->y() == $firstRowAboveWithMinos
                        ? Mino::at(Vector::fromInt($mino->position()->x(), $rowNumber))
                        : $mino
                );
            }
        }

        return new Matrix(
            $this->dimensions,
            $newMinos,
            $this->spawnPosition
        );
    }
}