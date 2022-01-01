<?php namespace Tetris\EventDispatch;

interface EventListener
{
    function handle($event);
}