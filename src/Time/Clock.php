<?php namespace Tetris\Time;

interface Clock
{
    function currentTimeInSeconds(): int;
    function currentTimeWithMilliseconds(): float;
}