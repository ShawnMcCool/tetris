<?php

use Tetris\Game;
use Tetris\Playfield;
use Tetris\Direction;
use Tetris\TetriminoBag;
use Tetris\Time\FrameTimer;
use Tetris\Time\SystemClock;
use Tetris\UI\Gameplay\Render;
use Tetris\Time\NonBlockingTimer;
use Tetris\Processes\SpawnNewTetrimino;
use Tetris\EventDispatch\DispatchEvents;
use Tetris\UI\Input\NonBlockingKeyboardPlayerInput;

require 'vendor/autoload.php';

/*
 * start the game
 */
$game = Game::start(
    Playfield::standard(),
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
$gameplayTimer = new NonBlockingTimer($clock, 1);

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
        //new DisplayEventsTextually(),
        new SpawnNewTetrimino($game),
        new Render(),
    ]
);

while (true) {

    // player input
    $pressedKey = $playerInput->pressedKey();
    if ($pressedKey) {
        switch ($pressedKey) {
            case 'a': // dvorak and qwerty left arrow, A in wasd
                $game->movePiece(Direction::left());
                break;
            case 'e': // dvorak right arrow, D in wasd
            case 'd': // qwerty right arrow
                $game->movePiece(Direction::right());
                break;
            case 's': // dvorak rotate left
            case ';': // qwerty rotate left
                $game->rotatePiece(Direction::left());
                break;
            case '-': // dvorak rotate right
            case "'": // qwerty rotate right
                $game->rotatePiece(Direction::right());
                break;
            case 'q': // dvorak
            case 'x': // qwerty exit
                die('exit');
        }
    }

    // process game time
    $gameplayTimer->tick();

    // dispatch state changes
    $events->dispatch(
        $game->flushEvents()
    );

    // sleep until next frame
    $frameTimer->waitForNextFrame();
}