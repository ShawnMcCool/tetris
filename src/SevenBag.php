<?php namespace Tetris;

final class SevenBag implements TetriminoBag
{
    private array $bag;

    public function draw(): Tetrimino
    {
        if (empty($this->bag)) {
            $this->fillBag();
        }
        
        return array_shift($this->bag);
    }

    private function fillBag(): void
    {
        $this->bag = [
            Tetrimino::withShape(Shape::i()),
            Tetrimino::withShape(Shape::j()),
            Tetrimino::withShape(Shape::l()),
            Tetrimino::withShape(Shape::o()),
            Tetrimino::withShape(Shape::s()),
            Tetrimino::withShape(Shape::t()),
            Tetrimino::withShape(Shape::z()),
        ];

        shuffle($this->bag);
    }
}