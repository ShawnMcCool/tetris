<?php

use Tetris\Game;
use Tetris\Matrix;
use Tetris\TetriminoBag;
use Tetris\Time\FrameTimer;
use Tetris\Time\SystemClock;
use Tetris\Time\NonBlockingTimer;
use Tetris\Processes\EchoEventName;
use Tetris\Processes\SpawnNewTetrimino;
use Tetris\EventDispatch\DispatchEvents;
use Tetris\UI\Input\NonBlockingKeyboardPlayerInput;
use function PhAnsi\set_cursor_position;

require 'vendor/autoload.php';

/*
 * start the game
 */
$game = Game::start(
    Matrix::standard(),
    new TetriminoBag()
);

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
 * gameplay timer
 */
$gameplayTimer = new NonBlockingTimer($clock, 800);

$gameplayTimer->onTick(
    function () use ($game) {
        $game->processGravity();
    }
);

$gameplayTimer->start();

/*
 * player input
 */
$playerInput = new NonBlockingKeyboardPlayerInput();

/*
 * processes
 */
$events = new DispatchEvents(
    [
        new EchoEventName(),
        new SpawnNewTetrimino($game),
    ]
);


while (true) {
    // process game time
    $gameplayTimer->tick();
    
    // dispatch state changes
    $events->dispatch(
        $game->flushEvents()
    );
    
    // sleep until next frame
    $frameTimer->waitForNextFrame();
}