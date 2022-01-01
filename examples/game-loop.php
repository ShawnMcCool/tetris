<?php

use Tetris\Time\FrameTimer;
use Tetris\Time\SystemClock;
use Tetris\Time\FrameCounter;
use Tetris\Time\NonBlockingTimer;
use Tetris\UI\Input\NonBlockingKeyboardPlayerInput;
use function PhAnsi\clear_screen;
use function PhAnsi\set_cursor_position;

require 'vendor/autoload.php';

/**
 * This is a basic example of ui rendering, player input, and a game timer working together.
 * 
 * There's a lot of hard coding regarding rendering the display.  
 * 
 * In this example, the nonblocking timer set to 500 ms represents the rate at which
 * the tetriminos fall.
 * 
 * The frame timer is the number of times per second that the game loop is processed. The
 * frame rate is locked so that the cpu doesn't just burn to a crisp. It's otherwise fine
 * to unlock the framerate because all gameplay processes rely on their own timers.
 */

/*
 * system clock
 */
$clock = new SystemClock();

/*
 * frame timer
 */
$frameTimer = new FrameTimer($clock, 20);
$frameTimer->start();

/*
 * frame counter
 */
$frameCounter = new FrameCounter($clock);

/*
 * gameplay timer
 */
$gameplayTimer = new NonBlockingTimer($clock, 500);

$animationFrames = ['🌑', '🌒', '🌓', '🌔', '🌕', '🌖', '🌗', '🌘'];
$animationFrameCounter = 0;

$gameplayTimer->onTick(
    function () use (&$animationFrameCounter, $animationFrames) {
        // update display
        set_cursor_position(0, 1);
        if ($animationFrameCounter == count($animationFrames)) {
            $animationFrameCounter = 0;
        }
        echo $animationFrames[$animationFrameCounter++];
        set_cursor_position(0, 5);
    }
);

$gameplayTimer->start();

/*
 * player input
 */
$playerInput = new NonBlockingKeyboardPlayerInput();

/*
 * game loop
 */
clear_screen();
while (true) {
    // frame update
    $pressedKey = $playerInput->pressedKey();
    $gameplayTimer->tick();

    // manage input
    if ($pressedKey) {
        if ($pressedKey == 'q') {
            echo "quitting...\n";
            break;
        }

        set_cursor_position(0, 4);
        echo $pressedKey;
    }

    // sleep until next frame
    $frameTimer->waitForNextFrame();
}