<?php namespace Tetris;

final class ActiveTetrimino
{
    public function __construct(
        private Tetrimino $tetrimino,
        private Position $position
    ) {
    }

    public function tetrimino(): Tetrimino
    {
        return $this->tetrimino;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function downOne(): self
    {
        return new self(
            $this->tetrimino,
            Position::fromInt(
                $this->position->x(),
                $this->position->y() + 1
            )
        );
    }

    public function rotate(Direction $direction): self
    {
        return new ActiveTetrimino(
            $direction->isLeft()
                ? $this->tetrimino->rotateLeft()
                : $this->tetrimino->rotateRight(),
            $this->position
        );
    }
}