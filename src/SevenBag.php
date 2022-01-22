<?php namespace Tetris;

final class SevenBag implements TetriminoBag
{
    private array $bag;
    private array $nextBag;

    public function __construct()
    {
        $this->nextBag = $this->shuffledBag();
    }

    public function draw(): Tetrimino
    {
        if (empty($this->bag)) {
            $this->fillBag();
        }

        return array_shift($this->bag);
    }

    public function next(): Tetrimino
    {
        if (empty($this->bag)) {
            return current($this->nextBag);
        }

        return current($this->bag);
    }

    private function fillBag(): void
    {
        $this->bag = $this->nextBag;
        $this->nextBag = $this->shuffledBag();
    }

    private function shuffledBag(): array
    {
        $newBag = [
            Tetrimino::withShape(Shape::i()),
            Tetrimino::withShape(Shape::j()),
            Tetrimino::withShape(Shape::l()),
            Tetrimino::withShape(Shape::o()),
            Tetrimino::withShape(Shape::s()),
            Tetrimino::withShape(Shape::t()),
            Tetrimino::withShape(Shape::z()),
        ];

        shuffle($newBag);

        return $newBag;
    }
}