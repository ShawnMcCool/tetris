<?php namespace Tests\TestDoubles;

use Tetris\Time\Clock;

final class TestClock implements Clock
{
    private float $secondsSlept = 0;
    
    public function __construct(
        private float $timestampWithMilliseconds
    ) {
    }

    public function currentTimeWithMilliseconds(): float
    {
        return $this->timestampWithMilliseconds;
    }

    public function sleepUntil(float $timestampWithMilliseconds): void
    {
        $this->secondsSlept = $timestampWithMilliseconds;
    }

    public function updateTimeTo(float $timestampWithMilliseconds): void
    {
        $this->timestampWithMilliseconds = $timestampWithMilliseconds;
    }

    public static function setTo(float $timestampWithMilliseconds): self
    {
        return new self($timestampWithMilliseconds);
    }

    public function secondsSlept(): float
    {
        return $this->secondsSlept;
    }
}