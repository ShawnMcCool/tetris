<?php

use function PhAnsi\terminal_width;
use function PhAnsi\terminal_height;
use function PhAnsi\terminal_cursor_position;

require 'vendor/autoload.php';

echo terminal_width();
echo terminal_height();
[$x, $y] = terminal_cursor_position();