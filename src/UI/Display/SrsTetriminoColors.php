<?php namespace Tetris\UI\Display;

use Tetris\ShapeName;

final class SrsTetriminoColors implements TetriminoColors
{
    public function forShape(ShapeName $shapeName): string
    {
        return match ($shapeName->toString()) {
            ShapeName::i()->toString() => 'rgb(0, 255, 255)',
            ShapeName::t()->toString() => 'rgb(153, 0, 255)',
            ShapeName::j()->toString() => 'rgb(0, 0, 255)',
            ShapeName::l()->toString() => 'rgb(255, 170, 0)',
            ShapeName::o()->toString() => 'rgb(255, 255, 0)',
            ShapeName::s()->toString() => 'rgb(0, 255, 0)',
            ShapeName::z()->toString() => 'rgb(255, 0, 0)',
            default => 'gray',
        };
    }
}