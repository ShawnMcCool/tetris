<?php

use Tetris\Game;
use Tetris\Matrix;
use Tetris\Vector;
use Tetris\Direction;
use Tetris\TetriminoBag;
use Tetris\Time\FrameTimer;
use Tetris\Time\SystemClock;
use Tetris\UI\Gameplay\Render;
use Tetris\Time\NonBlockingTimer;
use Tetris\Processes\SpawnNewTetrimino;
use Tetris\EventDispatch\DispatchEvents;
use Tetris\Processes\DisplayEventsTextually;
use Tetris\UI\Input\NonBlockingKeyboardPlayerInput;

require 'vendor/autoload.php';

/*
 * start the game
 */
$game = Game::start(
    Matrix::withDimensions(
        10, 20,
        Vector::fromInt(5, 0)
    ),
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
$gameplayTimer = new NonBlockingTimer($clock, .5);

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
        new Render(),
        new DisplayEventsTextually(),
        new SpawnNewTetrimino($game),
    ]
);

while (true) {

    /*
     * WASD = left / right controls
     * 
     * ;' keys (to the right of L = rotate left / right
     */
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