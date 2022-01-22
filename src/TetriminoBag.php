<?php

namespace Tetris;

interface TetriminoBag
{
    function draw(): Tetrimino;
    function next(): Tetrimino;
}