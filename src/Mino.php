<?php namespace Tetris;

final class Mino
{
    private function __construct(
        private Vector $position
    ) {
    }

    public function position(): Vector
    {
        return $this->position;
    }

    public static function at(Vector $position): self
    {
        return new self($position);
    }

    public function equals(self $that): bool
    {
        return $this->position->equals($that->position);
    }
    
    public function translate(Vector $vector): self
    {
        return new self(
            $this->position->add($vector)
        );
    }
}