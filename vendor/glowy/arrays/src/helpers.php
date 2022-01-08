<?php

declare(strict_types=1);

use Glowy\Arrays\Arrays;

if (! function_exists('arrays')) {
    /**
     * Create a new arrayable object from the given elements.
     *
     * Initializes a Arrays object and assigns $items the supplied values.
     *
     * @param  mixed $items Items
     *
     * @return Glowy\Arrays\Arrays<Arrays>
     */
    function arrays($items = null): Arrays
    {
        return Arrays::create($items);
    }
}

if (! function_exists('arraysFromJson')) {
    /**
     * Create a new arrayable object from the given JSON string.
     *
     * @param string $input A string containing JSON.
     * @param bool   $assoc Decode assoc. When TRUE, returned objects will be converted into associative arrays.
     * @param int    $depth Decode Depth. Set the maximum depth. Must be greater than zero.
     * @param int    $flags Bitmask consisting of decode options
     *
     * @return Glowy\Arrays\Arrays<Arrays>
     */
    function arraysFromJson(string $input, bool $assoc = true, int $depth = 512, int $flags = 0): Arrays
    {
        return Arrays::createFromJson($input, $assoc, $depth, $flags);
    }
}

if (! function_exists('arraysFromString')) {
    /**
     * Create a new arrayable object from the given string.
     *
     * @param string $string    Input string.
     * @param string $separator Elements separator.
     *
     * @return Glowy\Arrays\Arrays<Arrays>
     */
    function arraysFromString(string $string, string $separator): Arrays
    {
        return Arrays::createFromString($string, $separator);
    }
}

if (! function_exists('arraysWithRange')) {
    /**
     * Create a new arrayable object with a range of elements.
     *
     * @param float|int|string $low  First value of the sequence.
     * @param float|int|string $high The sequence is ended upon reaching the end value.
     * @param int              $step If a step value is given, it will be used as the increment between elements in the sequence.
     *                               step should be given as a positive number. If not specified, step will default to 1.
     *
     * @return Glowy\Arrays\Arrays<Arrays>
     */
    function arraysWithRange($low, $high, int $step = 1): Arrays
    {
        return Arrays::createWithRange($low, $high, $step);
    }
}
