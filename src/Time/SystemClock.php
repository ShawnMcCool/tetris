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
}