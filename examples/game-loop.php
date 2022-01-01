<?php

use Tetris\Time\FrameTimer;
use Tetris\Time\SystemClock;
use Tetris\Time\FrameCounter;
use Tetris\Time\NonBlockingTimer;
use Tetris\UI\Input\NonBlockingKeyboardPlayerInput;

require 'vendor/autoload.php';

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
$gameplayTimer = new NonBlockingTimer($clock, 1000);

$gameplayTimer->onTick(
    function () {
        echo "piece falls\n";
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
        echo "pressed '$pressedKey'\n";
    }
    
    // sleep until next frame
    $frameTimer->waitForNextFrame();
}