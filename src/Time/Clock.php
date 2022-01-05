<?php namespace Tetris\Time;

interface Clock
{
    function currentTimeWithMilliseconds(): float;
    function sleepUntil(float $timestampWithMilliseconds): void;
}