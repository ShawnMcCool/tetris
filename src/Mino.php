<?php namespace Tetris;

final class Mino
{
    private function __construct(
        private Vector $position,
        private ShapeName $shapeName,
    ) {
    }

    public function position(): Vector
    {
        return $this->position;
    }

    public function shapeName(): ShapeName
    {
        return $this->shapeName;
    }

    public static function at(Vector $position, ?ShapeName $shapeName): self
    {
        return new self($position, $shapeName ?? ShapeName::none());
    }

    public function sharesAPositionWith(self $that): bool
    {
        return $this->position->equals($that->position);
    }

    public function translate(Vector $vector): self
    {
        return new self(
            $this->position->add($vector),
            $this->shapeName,
        );
    }
}