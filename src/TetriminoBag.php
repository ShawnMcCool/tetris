<?php

namespace Tetris;

final class TetriminoBag
{
    public function draw(): Tetrimino
    {
        return Tetrimino::i(Vector::zero());
    }
}