<?php namespace Tetris\Time;

final class FrameCounter
{
    private array $timestamps = [];

    public function __construct(
        private Clock $clock
    ) {
    }

    public function tick(): void
    {
        $now = $this->clock->currentTimeInSeconds();
        
        if ( ! isset($this->timestamps[$now])) {
            $this->timestamps[$now] = 0;
        }
        
        $this->timestamps[$now]++;
        
        $this->clearFramesOlderThan($now);
    }

    public function fps(): int
    {
        $now = $this->clock->currentTimeInSeconds();

        $this->clearFramesOlderThan($now);
        
        if (isset($this->timestamps[$now])) {
            return $this->timestamps[$now];
        }

        return 0;
    }

    private function clearFramesOlderThan(int $now)
    {
        $secondsToRemove = array_filter(
            array_keys($this->timestamps),
            fn($second) => $second < $now
        );
        
        foreach ($secondsToRemove as $second) {
            unset($this->timestamps[$second]);
        }
    }
}