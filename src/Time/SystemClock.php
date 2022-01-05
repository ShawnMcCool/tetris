<?php namespace Tetris\Time;

final class SystemClock implements Clock
{
    function currentTimeWithMilliseconds(): float
    {
        return microtime(true);
    }

    function currentTimeInSeconds(): int
    {
        return time();
    }

    function sleepUntil(float $timestampWithMilliseconds): void
    {
        time_sleep_until($timestampWithMilliseconds);
    }
}