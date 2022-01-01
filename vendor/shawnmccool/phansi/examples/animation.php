<?php

use function PhAnsi\move_cursor_backward;

include 'vendor/autoload.php';

$animation = ['🌑', '🌒', '🌓', '🌔', '🌕', '🌖', '🌗', '🌘'];

$frame = 0;

echo "\n";
while(true) {
    usleep(100000);
    move_cursor_backward(90);
    foreach(range(1, 10) as $i) {
        echo $animation[$frame] . ' ';
    }

    $frame++;
    if ($frame == count($animation)) {
        $frame = 0;
    }
}
