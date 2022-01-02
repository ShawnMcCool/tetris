<?php namespace Tetris;

/**
 * The matrix is a 10x40 playfield. Once any block is locked down above
 * the first row, the game is over.
 */
final class Playfield
{
    private function __construct(
        private Matrix $matrix,
        private Vector $tetriminoSpawnPosition
    ) {}

    public function canFit(ActiveTetrimino $newlyPositionedTetrimino): bool
    {
        return ! $this->matrix->collidesWith(
            $newlyPositionedTetrimino->matrix(),
            $newlyPositionedTetrimino->matrixPosition()
        );
    }

    public function tetriminoSpawnPosition(): Vector
    {
        return $this->tetriminoSpawnPosition;
    }

    public function dimensions(): Vector
    {
        return $this->matrix->dimensions();
    }

    public function matrix(): Matrix
    {
        return $this->matrix;
    }
    
    public function lockTetrimino(ActiveTetrimino $tetrimino): void
    {
        
    }
    
    public static function fromMatrix(
        Matrix $matrix,
        Vector $tetriminoSpawnPosition
    ): self {
        return new self($matrix, $tetriminoSpawnPosition);
    }

    public static function standard(): self
    {
        return self::fromMatrix(
            Matrix::withDimensions(10, 40),
            Vector::fromInt(5, 3),
        );
    }
}