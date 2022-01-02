<?php namespace Tetris;

final class Matrix
{
    private function __construct(
        private array $matrix
    ) {
    }

    public function dimensions(): Vector
    {
        if (empty($this->matrix)) {
            return Vector::zero();
        }
        
        return Vector::fromInt(
            count($this->matrix[0]),
            count($this->matrix)
        );
    }
    
    public function subset(
        Vector $topLeft,
        Vector $bottomRight
    ): Matrix {
        /*
         * [0, 0, 0, 0, 0, 0],
         * [0, 1, 0, 0, 0, 0],
         * [0, 0, 0, 0, 0, 0],
         * [0, 0, 0, 0, 0, 0],
         * [0, 0, 0, 0, 1, 0],
         * [0, 0, 0, 0, 0, 0],
         * 
         * 1,1 - 4,4
         */
        $relevantRows = array_slice($this->matrix, $topLeft->y(), $bottomRight->y() - $topLeft->y());

        $newMatrix = array_map(
            fn(array $row) => array_slice($row, $topLeft->x(), $bottomRight->x()),
            $relevantRows
        );

        return Matrix::fromArray($newMatrix);
    }

    public function minoPositions(): array
    {
        $positions = [];
        
        foreach ($this->matrix as $rowCount => $rows) {
            foreach ($rows as $colCount => $space) {
                if ($space) {
                    $positions[] = Vector::fromInt($colCount, $rowCount);
                }
            }
        }
        
        return $positions;
    }

    private function hasMinoAt(Vector $position): bool
    {
        return
            isset($this->matrix[$position->y()][$position->x()]) &&
            $this->matrix[$position->y()][$position->x()] == 1;
    }

    public function collidesWith(
        Matrix $invader,
        Vector $invaderPosition
    ): bool {
        // loop through each row in the invader
        foreach ($invader->minoPositions() as $minoPosition) {
            
            $testPosition = $invaderPosition->plus($minoPosition);
            
            if ($this->hasMinoAt($testPosition)) {
                return true;
            }
        }
        
        return false;
    }

    public static function fromArray(array $matrix): self
    {
        return new self($matrix);
    }

    public static function withDimensions(int $x, int $y): self
    {
        return new self(
        // rows = y
            array_fill(
                0,
                $y,
                // cols = x
                array_fill(
                    0, $x, 0
                )
            )
        );
    }
}