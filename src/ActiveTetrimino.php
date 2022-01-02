<?php namespace Tetris;

final class ActiveTetrimino
{
    public function __construct(
        private Tetrimino $tetrimino,
        private Vector $position
    ) {
    }

    public function matrix(): Matrix
    {
        return $this->tetrimino->matrix();
    }
    
    public function matrixPosition(): Vector
    {
        return $this->position;
    }
    
    public function translate(Vector $vector): self
    {
        return new self(
            $this->tetrimino,
            $this->position->plus($vector)
        );
    }
    
    public function downOne(): self
    {
        return new self(
            $this->tetrimino,
            $this->position->plus(
                Vector::fromInt(0, 1)
            )
        );
    }

    public function rotate(Direction $direction): self
    {
        return new ActiveTetrimino(
            $this->tetrimino->rotate($direction),
            $this->position
        );
    }
}