<?php namespace Tetris;

final class Tetrimino
{
    public function __construct(
        private Shape $shape,
        private Vector $position,
        private int $rotationIndex = 0,
    ) {
    }

    public function translate(Vector $vector): self
    {
        return new self(
            $this->shape,
            $this->position->add($vector),
            $this->rotationIndex,
        );
    }

    public function rotate(Direction $direction): self
    {
        $newIndex = $this->rotationIndex + ($direction->isLeft() ? -1 : 1);

        if ($newIndex < 0) {
            $newIndex = $this->shape->numberOfRotations() - 1;
        } elseif ($newIndex == $this->shape->numberOfRotations()) {
            $newIndex = 0;
        }

        return new self(
            $this->shape,
            $this->position,
            $newIndex,
        );
    }

    public function minos(): Minos
    {
        return $this->shape->minos($this->rotationIndex);
    }

    public function minosInMatrixSpace(): Minos
    {
        return $this->minos()
                    ->translate(
                        $this->position()
                    );
    }

    public function position(): Vector
    {
        return $this->position;
    }

    public static function withShape(Shape $shape): self
    {
        return new self($shape, Vector::zero());
    }
}