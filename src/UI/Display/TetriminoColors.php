<?php namespace Tetris\UI\Display;

use Tetris\ShapeName;

interface TetriminoColors
{
    function forShape(ShapeName $shapeName): string;
}