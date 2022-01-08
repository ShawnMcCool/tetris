<?php namespace Tetris\UI\Display;

use Tetris\ShapeName;

final class TerminalSpecificTetriminoColors implements TetriminoColors
{
    function forShape(ShapeName $shapeName): string
    {
        return match ($shapeName->toString()) {
            ShapeName::i()->toString() => 'bright-blue',
            ShapeName::t()->toString() => 'magenta',
            ShapeName::j()->toString() => 'blue',
            ShapeName::l()->toString() => 'bright-red',
            ShapeName::o()->toString() => 'yellow',
            ShapeName::s()->toString() => 'green',
            ShapeName::z()->toString() => 'red',
            default => 'gray',
        };
    }
}