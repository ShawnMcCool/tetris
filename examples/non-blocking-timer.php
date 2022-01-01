<?php

use Tetris\Time\SystemClock;
use Tetris\Time\NonBlockingTimer;

require 'vendor/autoload.php';

$timer = new NonBlockingTimer(
    new SystemClock(),
    800
);

echo "assigning tick function...\n";

$timer->onTick(
    function () {
        echo "tick\n";
    }
);

echo "starting timer...\n";

$timer->start();

while (true) {
    $timer->tick();
}