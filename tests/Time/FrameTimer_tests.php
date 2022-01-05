<?php

use Tetris\Time\FrameTimer;
use Tests\TestDoubles\TestClock;
use function Tests\it;
use function Tests\expectfloat;

it('can track specified frame rates', function () {

    // a clock set to zero will wait 1/1 second on a 1 FPS timer 
    $clock = TestClock::setTo(0);

    $timer = new FrameTimer(
        $clock,
        1
    );

    $timer->start();
    $timer->waitForNextFrame();

    expectFloat(
        1 / 1,
        $clock->secondsSlept()
    );

    // a clock set to zero will wait 1/2 seconds on a 2 FPS timer 
    $clock = TestClock::setTo(0);

    $timer = new FrameTimer(
        $clock,
        2
    );

    $timer->start();
    $timer->waitForNextFrame();

    expectFloat(
        1 / 2,
        $clock->secondsSlept()
    );

    // a clock set to zero will wait 1/20 seconds on a 20 FPS timer 
    $clock = TestClock::setTo(0);

    $timer = new FrameTimer(
        $clock,
        20
    );

    $timer->start();
    $timer->waitForNextFrame();

    expectFloat(
        1 / 20,
        $clock->secondsSlept()
    );
});