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

    /**
     * @return array<Mino>
     */
    public function toArray(): array
    {
        return $this->minos;
    }

    public function equals(Minos $that): bool
    {
        if (count($this->minos) != count($that->minos)) {
            return false;
        }

        $arrayOfFoundMinos = [];

        /** @var Mino $mino */
        foreach ($this->minos as $i => $mino) {
            if ($that->hasMinoSharingAPositionWith($mino)) {
                $arrayOfFoundMinos[] = $that;
            }
        }

        return count($arrayOfFoundMinos) == count($that->minos);
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

    public function count(): int
    {
        return count($this->minos);
    }

    public function hasMinoSharingAPositionWith(Mino $needle): bool
    {
        /** @var Mino $mino */
        foreach ($this->minos as $mino) {
            if ($mino->sharesAPositionWith($needle)) {
                return true;
            }
        }
        return false;
    }

    public function filter(callable $f): self
    {
        return new self(
            array_filter($this->minos, $f)
        );
    }

    public function map(callable $f): self
    {
        return self::fromList(...array_map($f, $this->minos));
    }

    public function clone(): self
    {
        return new self($this->minos);
    }

    public function countOfMinosInRow(int $rowNumber): int
    {
        return $this->filter(
            fn(Mino $mino) => $mino->position()->y() == $rowNumber
        )->count();
    }

    public function nearestRowAboveContainingMinos(int $clearedRowNumber): ?int
    {
        foreach (range($clearedRowNumber - 1, 0) as $rowNumberToCheck) {
            if ($this->countOfMinosInRow($rowNumberToCheck) > 0) {
                return $rowNumberToCheck;
            }
        }
        return null;
    }

    public static function fromList(Mino ...$minos): self
    {
        return new self($minos);
    }

    public static function empty(): self
    {
        return new self([]);
    }
}