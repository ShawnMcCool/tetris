<?php

use Tetris\UI\Input\NonBlockingKeyboardPlayerInput;

require 'vendor/autoload.php';

$input = new NonBlockingKeyboardPlayerInput();

while (true) {
    $key = $input->pressedKey();
    
    if ($key) {
        echo "$key\n";
    }
}