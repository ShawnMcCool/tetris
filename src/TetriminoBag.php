<?php

namespace Tetris;

final class TetriminoBag
{
    public function draw(): Tetrimino
    {
        return Tetrimino::t(Vector::zero());
    }
}