<?php namespace Tetris;

/**
 * These are the playable pieces in Tetris. There are 7 different
 * pieces, O, I, T, L, J, S, and Z. These pieces fall toward the bottom
 * of the matrix and once they're locked in place, they cannot be moved.
 */
final class Tetrimino
{
    private const I =
        [
            [
                [0, 0, 0, 0],
                [1, 1, 1, 1],
                [0, 0, 0, 0],
                [0, 0, 0, 0],
            ],
            [
                [0, 0, 1, 0],
                [0, 0, 1, 0],
                [0, 0, 1, 0],
                [0, 0, 1, 0],
            ],
            [
                [0, 0, 0, 0],
                [0, 0, 0, 0],
                [1, 1, 1, 1],
                [0, 0, 0, 0],
            ],
            [
                [0, 1, 0, 0],
                [0, 1, 0, 0],
                [0, 1, 0, 0],
                [0, 1, 0, 0],
            ],
        ];

    public function __construct(
        private array $shapeArray,
        private int $currentIndex = 0,
    ) {
    }

    public static function I(): self
    {
        return new self(self::I);
    }

    public function rotateRight(): self
    {
        $newIndex = $this->currentIndex + 1;

        if ($newIndex > count($this->shapeArray) - 1) {
            $newIndex = 0;
        }
        
        return new self(
            $this->shapeArray,
            $newIndex,
        );
    }

    public function rotateLeft(): self
    {
        $newIndex = $this->currentIndex - 1;
        

        if ($newIndex < 0) {
            $newIndex = count($this->shapeArray);
        }
        
        return new self(
            $this->shapeArray,
            $newIndex,
        );
    }
}