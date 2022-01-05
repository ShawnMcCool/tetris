<?php

use Tests\TestDoubles\TestClock;
use Tetris\Time\NonBlockingTimer;
use Tests\TestDoubles\NumberRecorder;
use function Tests\it;
use function Tests\expectEqual;

it('maintains a steady framerate by sleeping between frames', function() {
    $clock = new TestClock(0);
    
    $timer = new NonBlockingTimer(
        $clock,
        1
    );
    
    $number = new NumberRecorder(0);
    
    $timer->onTick(
        fn() => $number->record($number->value() + 1)
    );
    
    // expect initial value
    expectEqual(0, $number->value());
    
    // start the timer
    $timer->start();
    
    // expect the same value - it hasn't ticked yet
    $clock->updateTimeTo(1);
    expectEqual(0, $number->value());
    
    // tick over
    $timer->tick();

    // NOW expect the new value
    expectEqual(1, $number->value());
    
    // update, tick, and test once more
    $clock->updateTimeTo(2);
    $timer->tick();
    expectEqual(2, $number->value());
});