<?php namespace Tetris;

final class Tetrimino
{
    public function __construct(
        private array $minos,
        private Vector $position,
        private int $currentMinoRotationIndex,
    ) {
    }

    public function translate(Vector $vector): self
    {
        return new self(
            $this->minos,
            $this->position->add($vector),
            $this->currentMinoRotationIndex
        );
    }

    public function rotate(Direction $direction): self
    {
        $newIndex = $this->currentMinoRotationIndex + ($direction->isLeft() ? -1 : 1);

        if ($newIndex < 0) {
            $newIndex = count($this->minos) - 1;
        } elseif ($newIndex == count($this->minos)) {
            $newIndex = 0;
        }

        return new self(
            $this->minos,
            $this->position,
            $newIndex
        );
    }

    public function minos(): Minos
    {
        return $this->minos[$this->currentMinoRotationIndex];
    }

    public function minosInMatrixSpace(): Minos
    {
        return $this->minos()
                    ->translate($this->position());
    }

    public function position(): Vector
    {
        return $this->position;
    }

    public static function t(Vector $position): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0)),
                    Mino::at(Vector::fromInt(0, 1)),
                    Mino::at(Vector::fromInt(1, 1)),
                    Mino::at(Vector::fromInt(2, 1)),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0)),
                    Mino::at(Vector::fromInt(1, 1)),
                    Mino::at(Vector::fromInt(2, 1)),
                    Mino::at(Vector::fromInt(1, 2)),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 1)),
                    Mino::at(Vector::fromInt(1, 1)),
                    Mino::at(Vector::fromInt(2, 1)),
                    Mino::at(Vector::fromInt(1, 2)),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0)),
                    Mino::at(Vector::fromInt(0, 1)),
                    Mino::at(Vector::fromInt(1, 1)),
                    Mino::at(Vector::fromInt(1, 2)),
                ),
            ],
            $position,
            0
        );
    }

    public static function i(Vector $position): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 1)),
                    Mino::at(Vector::fromInt(1, 1)),
                    Mino::at(Vector::fromInt(2, 1)),
                    Mino::at(Vector::fromInt(3, 1)),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(2, 0)),
                    Mino::at(Vector::fromInt(2, 1)),
                    Mino::at(Vector::fromInt(2, 2)),
                    Mino::at(Vector::fromInt(2, 3)),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 2)),
                    Mino::at(Vector::fromInt(1, 2)),
                    Mino::at(Vector::fromInt(2, 2)),
                    Mino::at(Vector::fromInt(3, 2)),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0)),
                    Mino::at(Vector::fromInt(1, 1)),
                    Mino::at(Vector::fromInt(1, 2)),
                    Mino::at(Vector::fromInt(1, 3)),
                ),
            ],
            $position,
            0
        );
    }
}