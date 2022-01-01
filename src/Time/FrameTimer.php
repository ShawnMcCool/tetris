<?php namespace Tetris\Time;

class FrameTimer
{
    private float $lastFrameTime = 0;
    private bool $timerIsRunning = false;

    public function __construct(
        private Clock $clock,
        private int $framesPerSecond = 30
    ) {
    }

    public function start(): void
    {
        $this->timerIsRunning = true;
        $this->lastFrameTime = $this->clock->currentTimeWithMilliseconds();
    }

    public function waitForNextFrame(): void
    {
        if ( ! $this->timerIsRunning) {
            return;
        }

        $now = $this->clock->currentTimeWithMilliseconds();

        $deltaTime = $now - $this->lastFrameTime;

        $secondsPerFrame = 1 / $this->framesPerSecond;

        $secondsToSleep = $secondsPerFrame - $deltaTime;
        
        if ($secondsToSleep > 0) {
            
            time_sleep_until($this->clock->currentTimeWithMilliseconds() + $secondsToSleep);
        }

        $this->lastFrameTime = $now;
    }
}