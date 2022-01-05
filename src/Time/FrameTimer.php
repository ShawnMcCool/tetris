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
        $this->recordTheFrameStartTime();
    }

    public function waitForNextFrame(): void
    {
        if ( ! $this->timerIsRunning) {
            return;
        }

        $this->waitUntilTheEndOfThisFrame();
        $this->recordTheFrameStartTime();
    }

    private function waitUntilTheEndOfThisFrame(): void
    {
        $secondsToSleep = $this->secondsRemainingInThisFrame();

        if ($secondsToSleep > 0) {
            $this->clock->sleepUntil(
                $this->clock->currentTimeWithMilliseconds() + $secondsToSleep
            );
        }
    }

    private function recordTheFrameStartTime(): void
    {
        $this->lastFrameTime = $this->clock->currentTimeWithMilliseconds();
    }

    private function frameDeltaTime(): float
    {
        return $this->clock->currentTimeWithMilliseconds() - $this->lastFrameTime;
    }

    private function secondsRemainingInThisFrame(): float
    {
        $secondsPerFrame = 1 / $this->framesPerSecond;
        return $secondsPerFrame - $this->frameDeltaTime();
    }
}