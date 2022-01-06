<?php

use Tetris\Vector;
use Tetris\Direction;
use Tetris\Events\TetriminoWasMoved;
use Tests\TestDoubles\NumberRecorder;
use Tetris\Events\TetriminoWasRotated;
use Tetris\Processes\DisplayEventsTextually;
use function Tests\it;
use function Tests\expectOutputStartsWith;

it('describes tetrimino movement', function() {
    
    $display = new DisplayEventsTextually();
    
    expectOutputStartsWith(
        'Tetrimino was moved',
        fn() => $display->handle(
            new TetriminoWasMoved(
                new ActiveTetrimino(
                    Tetrimino::I(),
                    Vector::zero()
                ), Direction::left()
            )
        )
    );
});

it('describes tetrimino rotation', function() {
    
    $display = new DisplayEventsTextually();
    
    expectOutputStartsWith(
        'Tetrimino was rotated',
        fn() => $display->handle(
            new TetriminoWasRotated(
                new ActiveTetrimino(
                    Tetrimino::I(),
                    Vector::zero()
                ), Direction::left()
            )
        )
    );
});

it('outputs the class name if the event wasn\'t specified.', function() {
    
    $display = new DisplayEventsTextually();
    
    expectOutputStartsWith(
        'Tests\TestDoubles\NumberRecorder',
        fn() => $display->handle(new NumberRecorder(1))
    );
});