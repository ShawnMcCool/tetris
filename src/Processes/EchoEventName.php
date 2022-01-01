<?php namespace Tetris\Processes;

use Tetris\EventDispatch\EventListener;

final class EchoEventName implements EventListener
{
    function handle($event)
    {
        echo get_class($event) . "\n";
    }
}