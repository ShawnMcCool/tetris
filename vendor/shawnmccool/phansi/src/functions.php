<?php namespace PhAnsi;

function ansi(int $code)
{
    $code = str_pad($code, 3, 0, STR_PAD_LEFT);
    return "\x1b[{$code}m";
}

function off() {
    return '\u001b[0m';
}

function bold($string)
{
    return ansi(AnsiCodes::$bold) . $string . ansi(AnsiCodes::$off);
}

function italic($string)
{
    return ansi(AnsiCodes::$italic) . $string . ansi(AnsiCodes::$off);
}

function underline($string)
{
    return ansi(AnsiCodes::$underline) . $string . ansi(AnsiCodes::$off);
}

function blink($string)
{
    return ansi(AnsiCodes::$blink) . $string . ansi(AnsiCodes::$off);
}

function inverse($string)
{
    return ansi(AnsiCodes::$inverse) . $string . ansi(AnsiCodes::$off);
}

function hidden($string)
{
    return ansi(AnsiCodes::$hidden) . $string . ansi(AnsiCodes::$off);
}

function black($string)
{
    return ansi(AnsiCodes::$black) . $string . ansi(AnsiCodes::$off);
}

function red($string)
{
    return ansi(AnsiCodes::$red) . $string . ansi(AnsiCodes::$off);
}

function green($string)
{
    return ansi(AnsiCodes::$green) . $string . ansi(AnsiCodes::$off);
}

function yellow($string)
{
    return ansi(AnsiCodes::$yellow) . $string . ansi(AnsiCodes::$off);
}

function blue($string)
{
    return ansi(AnsiCodes::$blue) . $string . ansi(AnsiCodes::$off);
}

function magenta($string)
{
    return ansi(AnsiCodes::$magenta) . $string . ansi(AnsiCodes::$off);
}

function cyan($string)
{
    return ansi(AnsiCodes::$cyan) . $string . ansi(AnsiCodes::$off);
}

function white($string)
{
    return ansi(AnsiCodes::$white) . $string . ansi(AnsiCodes::$off);
}

function brightBlack($string)
{
    return ansi(AnsiCodes::$brightBlack) . $string . ansi(AnsiCodes::$off);
}

function brightWhite($string)
{
    return ansi(AnsiCodes::$brightWhite) . $string . ansi(AnsiCodes::$off);
}

function bgBlack($string)
{
    return ansi(AnsiCodes::$blackBg) . $string . ansi(AnsiCodes::$off);
}

function bgRed($string)
{
    return ansi(AnsiCodes::$redBg) . $string . ansi(AnsiCodes::$off);
}

function bgGreen($string)
{
    return ansi(AnsiCodes::$greenBg) . $string . ansi(AnsiCodes::$off);
}

function bgYellow($string)
{
    return ansi(AnsiCodes::$yellowBg) . $string . ansi(AnsiCodes::$off);
}

function bgBlue($string)
{
    return ansi(AnsiCodes::$blueBg) . $string . ansi(AnsiCodes::$off);
}

function bgMagenta($string)
{
    return ansi(AnsiCodes::$magentaBg) . $string . ansi(AnsiCodes::$off);
}

function bgCyan($string)
{
    return ansi(AnsiCodes::$cyanBg) . $string . ansi(AnsiCodes::$off);
}

function bgWhite($string)
{
    return ansi(AnsiCodes::$whiteBg) . $string . ansi(AnsiCodes::$off);
}

function set_cursor_position($lineNumber, $columnNumber)
{
    echo "\033[{$lineNumber};{$columnNumber}H";
}

function move_cursor_up($lineCount)
{
    echo "\033[{$lineCount}A";
}

function move_cursor_down($lineCount)
{
    echo "\033[{$lineCount}B";
}

function move_cursor_forward($columnCount)
{
    echo "\033[{$columnCount}C";
}

function move_cursor_backward($columnCount)
{
    echo "\033[{$columnCount}D";
}

function clear_screen()
{
    echo "\033[2J";
}

function erase_to_end_of_line()
{
    echo "\033[K";
}

function ns_save_cursor_position()
{
    echo "\033[s";
}

function ns_restore_cursor_position()
{
    echo "\033[u";
}

function terminal_cursor_position()
{
    $ttyprops = trim(`stty -g`);
    system('stty -icanon -echo');

    $term = fopen('/dev/tty', 'w');
    fwrite($term, "\033[6n");
    fclose($term);

    $buf = fread(STDIN, 16);

    system("stty '$ttyprops'");

    #echo bin2hex($buf) . "\n";

    $matches = [];
    preg_match('/^\033\[(\d+);(\d+)R$/', $buf, $matches);

    return [
        intval($matches[2]),
        intval($matches[1]),
    ];
}

function terminal_width()
{
    return `tput cols`;
}

function terminal_height()
{
    return `tput lines`;
}