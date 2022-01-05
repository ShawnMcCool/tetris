<?php namespace Tetris\Time;

use Closure;

/**
 * This non-blocking timer's tick() method is run once during every game 'frame'.
 * The timer's 'onTick' function is called every "millisecondInterval" milliseconds.
 * This timer will run multiple onTick functions during a single tick() if the timer
 * is 'backed up'. So that on average the onTick functions per millisecondInterval
 * remains precise.
 *
 * I can word this better.
 */
class NonBlockingTimer
{
    private ?closure $onTickFunction = null;
    private bool $timerIsRunning = false;
    private float $timeOfLastTick = 0;

    public function __construct(
        private Clock $clock,
        private int $intervalSeconds
    ) {
    }

    public function start(): void
    {
        $this->timeOfLastTick = $this->clock->currentTimeWithMilliseconds();
        $this->timerIsRunning = true;
    }

    public function stop(): void
    {
        $this->timerIsRunning = false;
    }

    public function onTick(callable $f): void
    {
        $this->onTickFunction = $f;
    }

    public function tick(): void
    {
        if ( ! $this->timerIsRunning) {
            return;
        }

        while ($this->ticksAreQueued()) {
            $this->processTick();
        }
    }

    private function ticksAreQueued(): bool
    {
        return $this->clock->currentTimeWithMilliseconds() >= $this->timeOfNextTick();
    }

    private function processTick()
    {
        $this->timeOfLastTick = $this->timeOfNextTick();

        if ($this->onTickFunction) {
            ($this->onTickFunction)($this->timeOfLastTick);
        }
    }

    private function timeOfNextTick(): float
    {
        return $this->timeOfLastTick + $this->intervalSeconds;
    }
}