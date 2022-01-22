<?php namespace Tetris;

final class ShapeName
{
    public function __construct(
        private string $name
    ) {
    }

    public function toString(): string
    {
        return $this->name;
    }

    public static function gameOver(): self
    {
        return new self('game over');
    }
    
    public static function none(): self
    {
        return new self('none');
    }

    public static function l(): self
    {
        return new self('l');
    }

    public static function t(): self
    {
        return new self('t');
    }

    public static function j(): self
    {
        return new self('j');
    }

    public static function o(): self
    {
        return new self('o');
    }

    public static function s(): self
    {
        return new self('s');
    }

    public static function z(): self
    {
        return new self('z');
    }

    public static function i(): self
    {
        return new self('i');
    }

    public static function ghostPiece(): self
    {
        return new self('ghost piece');
    }
}