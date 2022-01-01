<?php namespace Tetris\EventDispatch;

final class DispatchEvents
{
    public function __construct(
        private array $listeners
    ) {
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            foreach ($this->listeners as $listener) {
                $listener->handle($event);
            }
        }
    }
}