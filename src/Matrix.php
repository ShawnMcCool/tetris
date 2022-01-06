<?php namespace Tetris;

final class Matrix
{
    private function __construct(
        private Vector $dimensions,
        private Minos $minos,
        private Vector $spawnPosition
    ) {
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
        
        if ( ! empty($boundaryCollisions)) {
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
                $tetrimino->minos()
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
        int $width,
        int $height,
        Vector $spawnPosition
    ): self {
        return new self(
            Vector::fromInt($width, $height),
            Minos::empty(),
            $spawnPosition
        );
    }
}