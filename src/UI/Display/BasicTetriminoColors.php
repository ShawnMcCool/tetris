<?php namespace Tetris\UI\Display;

use Tetris\ShapeName;

final class BasicTetriminoColors implements TetriminoColors
{
    public function forShape(ShapeName $shapeName): string
    {
        return match ($shapeName->toString()) {
            'i' => 'rgb(0, 255, 255)',
            't' => 'rgb(153, 0, 255)',
            'j' => 'rgb(0, 0, 255)',
            'l' => 'rgb(255, 170, 0)',
            'o' => 'rgb(255, 255, 0)',
            's' => 'rgb(0, 255, 0)',
            'z' => 'rgb(255, 0, 0)',
            default => 'gray',
        };
    }
}