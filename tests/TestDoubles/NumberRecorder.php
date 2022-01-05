<?php namespace Tests\TestDoubles;

final class NumberRecorder
{
    public function __construct(
        private int $value
    ) {
    }

    public function record(int $newValue): void
    {
        $this->value = $newValue;
    }

    public function value(): int
    {
        return $this->value;
    }
}