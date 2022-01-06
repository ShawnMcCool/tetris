<?php namespace Tetris;

final class Minos
{
    private function __construct(
        private array $minos
    ) {
    }

    public function add(Minos $minos): self
    {
        $newMinos = $this->minos;
        
        foreach ($minos->toArray() as $mino) {
            $newMinos[] = $mino;
        }
        
        return new self(
            $newMinos
        );
    }

    public static function fromList(Mino ...$minos): self
    {
        return new self($minos);
    }

    public function toArray(): array
    {
        return $this->minos;
    }

    public function equals(Minos $that): bool
    {
        if (count($this->minos) != count($that->minos)) {
            return false;
        }
        
        /** @var Mino $mino */
        foreach ($this->minos as $i => $mino) {
            if ( ! $mino->equals($that->minos[$i])) {
                return false;
            }
        }
        
        return true;
    }
    
    public function translate(Vector $vector): self
    {
        return new self(
            array_map(
                fn(Mino $mino) => $mino->translate($vector),
                $this->minos
            )
        );
    }

    public function hasMino(Mino $needle): bool
    {
        /** @var Mino $mino */
        foreach ($this->minos as $mino) {
            if ($mino->equals($needle)) {
                return true;
            }
        }
        return false;
    }

    public static function empty(): self
    {
        return new self([]);
    }
}