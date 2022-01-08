<?php namespace Tetris;

use Exception;

final class Shape
{
    private function __construct(
        private array $minos,
    ) {
    }

    public function numberOfRotations(): int
    {
        return count($this->minos);
    }

    public function minos(int $rotationIndex): Minos
    {
        if ( ! isset($this->minos[$rotationIndex])) {
            throw new Exception('rotation index ' . $rotationIndex . ' is not valid.');
        }

        return $this->minos[$rotationIndex];
    }

    public static function i(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 1), ShapeName::i()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
                    Mino::at(Vector::fromInt(3, 1), ShapeName::i()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(2, 0), ShapeName::i()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
                    Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
                    Mino::at(Vector::fromInt(2, 3), ShapeName::i()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 2), ShapeName::i()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::i()),
                    Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
                    Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::i()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::i()),
                    Mino::at(Vector::fromInt(1, 3), ShapeName::i()),
                ),
            ]
        );
    }

    public static function j(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 0), ShapeName::j()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::j()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::j()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::j()),
                    Mino::at(Vector::fromInt(2, 0), ShapeName::j()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(2, 2), ShapeName::j()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::j()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::j()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::j()),
                    Mino::at(Vector::fromInt(0, 2), ShapeName::j()),
                ),
            ]
        );
    }
    
    public static function l(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(2, 0), ShapeName::l()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::l()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::l()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::l()),
                    Mino::at(Vector::fromInt(0, 0), ShapeName::l()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(0, 2), ShapeName::l()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::l()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::l()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::l()),
                    Mino::at(Vector::fromInt(2, 2), ShapeName::l()),
                ),
            ]
        );
    }

    public static function o(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::o()),
                    Mino::at(Vector::fromInt(2, 0), ShapeName::o()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::o()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::o()),
                ),
            ]
        );
    }
    
    public static function s(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::s()),
                    Mino::at(Vector::fromInt(2, 0), ShapeName::s()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::s()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::s()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(2, 2), ShapeName::s()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(0, 2), ShapeName::s()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::s()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 0), ShapeName::s()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::s()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::s()),
                ),
            ]
        );
    }
    
    public static function t(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::t()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::t()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::t()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::t()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::t()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::t()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::t()),
                ),
            ]
        );
    }
    
    public static function z(): self
    {
        return new self(
            [
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::z()),
                    Mino::at(Vector::fromInt(2, 0), ShapeName::z()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::z()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 0), ShapeName::z()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(2, 2), ShapeName::z()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(1, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(2, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(0, 2), ShapeName::z()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::z()),
                ),
                Minos::fromList(
                    Mino::at(Vector::fromInt(0, 0), ShapeName::z()),
                    Mino::at(Vector::fromInt(0, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(1, 1), ShapeName::z()),
                    Mino::at(Vector::fromInt(1, 2), ShapeName::z()),
                ),
            ]
        );
    }
}