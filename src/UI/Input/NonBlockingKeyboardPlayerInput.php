<?php namespace Tetris\UI\Input;

use Tetris\EventDispatch\DispatchEvents;

final class NonBlockingKeyboardPlayerInput
{
    private $stdin;

    /**
     * It's true that this is very global, I'm ok with it.
     */
    public function __construct(
        private DispatchEvents $events
    )
    {
        readline_callback_handler_install('', fn() => null);
        $this->stdin = fopen('php://stdin', 'r');
    }

    public function __destruct()
    {
        readline_callback_handler_remove();
        fclose($this->stdin);
    }

    public function check(): void
    {
        $key = $this->pressedKey();

        $this->events->dispatch([]);
    }
    public function pressedKey(): string|null
    {
        $readStreams = [$this->stdin];
        $writeStreams = null;
        $exceptStreams = null;
        $seconds = 0;

        $numberOfChangedStreams = stream_select(
            $readStreams,
            $writeStreams,
            $exceptStreams,
            $seconds
        );

        if ($numberOfChangedStreams > 0) {
            return stream_get_contents($this->stdin, 1);
        }
        
        return null;
    }
}