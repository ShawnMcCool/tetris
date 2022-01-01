<?php namespace Tetris;

/**
 * The matrix is a 10x40 playfield. Once any block is locked down above
 * the first row, the game is over.
 */
final class Matrix
{
    private function __construct(
        private Dimensions $dimensions,
        private Position $tetriminoSpawnPosition
    ) {
    }

    public static function withDimensions(
        Dimensions $dimensions,
        Position $tetriminoSpawnPosition
    ): self {
        return new self($dimensions, $tetriminoSpawnPosition);
    }

    public static function standard(): self
    {
        return self::withDimensions(
            Dimensions::fromInt(40, 10),
            Position::fromInt(1, 5),
        );
    }

    public function canFit(ActiveTetrimino $tetrimino): bool
    {
        return true;
    }

    public function dimensions(): Dimensions
    {
        return $this->dimensions;
    }

    public function tetriminoSpawnPosition(): Position
    {
        return $this->tetriminoSpawnPosition;
    }

    public function lock(ActiveTetrimino $tetrimino): void
    {
        //
    }
}