<?php

declare(strict_types=1);

namespace Glowy\Arrays;

use ArrayAccess;
use ArrayIterator;
use Glowy\Macroable\Macroable;
use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

use function array_chunk;
use function array_column;
use function array_combine;
use function array_diff;
use function array_filter;
use function array_flip;
use function array_intersect;
use function array_intersect_assoc;
use function array_intersect_key;
use function array_key_first;
use function array_key_last;
use function array_keys;
use function array_map;
use function array_merge;
use function array_merge_recursive;
use function array_pad;
use function array_product;
use function array_rand;
use function array_reduce;
use function array_replace;
use function array_replace_recursive;
use function array_reverse;
use function array_search;
use function array_shift;
use function array_slice;
use function array_sum;
use function array_unique;
use function array_unshift;
use function array_values;
use function array_walk;
use function array_walk_recursive;
use function arsort;
use function asort;
use function count;
use function current;
use function defined;
use function end;
use function explode;
use function http_build_query;
use function in_array;
use function is_array;
use function is_null;
use function iterator_to_array;
use function json_decode;
use function json_encode;
use function krsort;
use function ksort;
use function mb_internal_encoding;
use function mb_strlen;
use function mb_strpos;
use function mb_strtolower;
use function mb_substr;
use function mt_srand;
use function natsort;
use function next;
use function preg_match;
use function preg_replace;
use function prev;
use function print_r;
use function range;
use function rsort;
use function shuffle;
use function sort;
use function strncmp;
use function strpos;
use function strtotime;
use function strval;
use function uksort;
use function usort;

use const ARRAY_FILTER_USE_BOTH;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const PHP_QUERY_RFC3986;
use const SORT_NATURAL;
use const SORT_REGULAR;
use const SORT_STRING;

class Arrays implements ArrayAccess, Countable, IteratorAggregate
{
    use Macroable;

    /**
     * The underlying array items.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Create a new arrayable object from the given elements.
     *
     * Initializes a Arrays object and assigns $items the supplied values.
     *
     * @param mixed $items Items.
     */
    public function __construct($items = null)
    {
        $this->items = self::getArray($items);
    }

    /**
     * Create a new arrayable object from the given elements.
     *
     * Initializes a Arrays object and assigns $items the supplied values.
     *
     * @param mixed $items Items.
     *
     * @return self Returns instance of The Arrays class.
     */
    public static function create($items = null): self
    {
        return new static(self::getArray($items));
    }

    /**
     * Create a new arrayable object from the given JSON string.
     *
     * @param string $input A string containing JSON.
     * @param bool   $assoc Decode assoc. When TRUE, returned objects will be converted into associative arrays.
     * @param int    $depth Decode Depth. Set the maximum depth. Must be greater than zero.
     * @param int    $flags Bitmask consisting of decode options
     *
     * @return self Returns instance of The Arrays class.
     */
    public static function createFromJson(string $input, bool $assoc = true, int $depth = 512, int $flags = 0): self
    {
        return new static(json_decode($input, $assoc, $depth, $flags));
    }

    /**
     * Create a new arrayable object from the given string.
     *
     * @param string $string    Input string.
     * @param string $separator Elements separator.
     *
     * @return self Returns instance of The Arrays class.
     */
    public static function createFromString(string $string, string $separator): self
    {
        return new static(explode($separator, $string));
    }

    /**
     * Create a new arrayable object with a range of elements.
     *
     * @param float|int|string $low  First value of the sequence.
     * @param float|int|string $high The sequence is ended upon reaching the end value.
     * @param int              $step If a step value is given, it will be used as the increment between elements in the sequence.
     *                               step should be given as a positive number. If not specified, step will default to 1.
     *
     * @return self Returns instance of The Arrays class.
     */
    public static function createWithRange($low, $high, int $step = 1): self
    {
        return new static(range($low, $high, $step));
    }

    /**
     * Reduce the array to a single value iteratively combining all values using $callback.
     *
     * @param callable   $callback Callback with ($carry, $item)
     * @param mixed|null $initial  If the optional initial is available,
     *                             it will be used at the beginning of the process,
     *                             or as a final result in case the array is empty.
     *
     * @return mixed Returns the resulting value.
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  string|null $key   Key
     * @param  mixed       $value Value
     *
     * @return self Returns instance of The Arrays class.
     */
    public function set(?string $key, $value): self
    {
        $array = &$this->items;

        if (is_null($key)) {
            $this->items = $value;

            return $this;
        }

        $segments = explode('.', $key);

        foreach ($segments as $i => $segment) {
            if (count($segments) === 1) {
                break;
            }

            unset($segments[$i]);

            if (! isset($array[$segment]) || ! is_array($array[$segment])) {
                $array[$segment] = [];
            }

            $array = &$array[$segment];
        }

        $array[array_shift($segments)] = $value;

        return $this;
    }

    /**
     * Return an array of all values stored array.
     *
     * @return array Returns an indexed array of values.
     */
    public function getValues(): array
    {
        return array_values($this->items);
    }

    /**
     * Alias of search() method. Search for a given item and return
     * the index of its first occurrence.
     *
     * @param mixed $needle The searched value.
     *
     * @return mixed Returns the key for needle if it is found in the array, FALSE otherwise.
     */
    public function indexOf($needle)
    {
        return $this->search($needle);
    }

    /**
     * Check whether the array is empty or not.
     *
     * @return bool Returns TRUE whether the array is empty. FALSE otherwise.
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    /**
     * Searches the array for a given value and returns the first corresponding key if successful.
     *
     * @param  mixed $needle The searched value.
     *
     * @return mixed Returns the key for needle if it is found in the array, FALSE otherwise.
     */
    public function search($needle)
    {
        return array_search($needle, $this->items, true);
    }

    /**
     * Checks if the given dot-notated key exists in the array.
     *
     * @param  string|array $keys Keys
     *
     * @return bool Return TRUE key exists in the array, FALSE otherwise.
     */
    public function has($keys): bool
    {
        $array = $this->items;

        $keys = (array) $keys;

        if (! $array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (isset($array[$key])) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (! is_array($subKeyArray) || ! isset($subKeyArray[$segment])) {
                    return false;
                }

                $subKeyArray = $subKeyArray[$segment];
            }
        }

        return true;
    }

    /**
     * Passes the array to the given callback and return the result.
     *
     * @param Closure $callback Function with arrays as parameter which returns arbitrary result.
     *
     * @return mixed Result returned by the callback.
     */
    public function pipe(Closure $callback)
    {
        return $callback($this);
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  string|int|null $key     Key
     * @param  mixed           $default Default value
     *
     * @return mixed Item from an array.
     */
    public function get($key, $default = null)
    {
        $array = $this->items;

        if (! is_array($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        if (strpos((string) $key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (! is_array($array) || ! isset($array[$segment])) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Delete the given key or keys using "dot notation".
     *
     * @param  array|int|string $keys Keys
     *
     * @return self Returns instance of The Arrays class.
     */
    public function delete($keys): self
    {
        $keys = (array) $keys;

        if (count($keys) === 0) {
            return $this;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $this->items)) {
                unset($this->items[$key]);
                continue;
            }

            $items = &$this->items;
            $segments = explode('.', $key);
            $lastSegment = array_pop($segments);

            foreach ($segments as $segment) {
                if (!isset($items[$segment]) || !is_array($items[$segment])) {
                    continue 2;
                }

                $items = &$items[$segment];
            }

            unset($items[$lastSegment]);
        }

        return $this;
    }

    /**
     * Extract the items from the current array using "dot" notation for further manipulations.
     *
     * @param  string|int|null $key     Key.
     * @param  mixed           $default Default value.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function extract($key, $default = null): self
    {
        $this->items = $this->get($key);

        return $this;
    }

    /**
     * Push an item into the end of an array.
     *
     * @param mixed $value The new item to append
     *
     * @return self Returns instance of The Arrays class.
     */
    function append($value = null): self
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * Push an item into the beginning of an array.
     *
     * @param mixed $value The new item to append
     *
     * @return self Returns instance of The Arrays class.
     */
    function prepend($value = null): self
    {
        array_unshift($this->items, $value);

        return $this;
    }

    /**
     * Expands a dot notation array into a full multi-dimensional array.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function undot(): self
    {
        $array = $this->items;

        $this->items = [];

        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  string $prepend Prepend string
     *
     * @return self Returns instance of The Arrays class.
     */
    public function dot(string $prepend = ''): self
    {
        $_dot = static function ($array, $prepend) use (&$_dot) {
            $results = [];

            foreach ($array as $key => $value) {
                if (is_array($value) && ! empty($value)) {
                    $results = array_merge($results, $_dot($value, $prepend . $key . '.'));
                } else {
                    $results[$prepend . $key] = $value;
                }
            }

            return $results;
        };

        $this->items = $_dot($this->items, $prepend);

        return $this;
    }

    /**
     * Flush all values from the array.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function flush(): self
    {
        $this->items = [];

        return $this;
    }

    /**
     * Groups the array items by a given key.
     *
     * @param  string $key Key
     *
     * @return self Returns instance of The Arrays class.
     */
    public function groupBy(string $key): self
    {
        $result = [];

        foreach ($this->items as $value) {
            $result[$value[$key]][] = $value;
        }

        $this->items = $result;

        return $this;
    }

    /**
     * Sorts a associative array by a certain key.
     *
     * @param  string $key       The name of the key.
     * @param  string $direction Order type DESC (descending) or ASC (ascending)
     * @param  int    $sortFlags A PHP sort method flags.
     *                           https://www.php.net/manual/ru/function.sort.php
     *
     * @return self Returns instance of The Arrays class.
     */
    public function sortBy(string $key, string $direction = 'ASC', int $sortFlags = SORT_REGULAR): self
    {
        $array     = $this->items;
        $direction = mb_strtolower($direction);
        $result    = [];

        if (count($array) <= 0) {
            return $this;
        }

        foreach ($array as $k => $row) {
            $helper[$k] = mb_strtolower(strval(static::create($row)->get($key)));
        }

        if ($sortFlags === SORT_NATURAL) {
            natsort($helper);
            ($direction === 'desc') and $helper = array_reverse($helper);
        } elseif ($direction === 'desc') {
            arsort($helper, $sortFlags);
        } else {
            asort($helper, $sortFlags);
        }

        foreach ($helper as $k => $val) {
            $result[$k] = $array[$k];
        }

        $this->items = $result;

        return $this;
    }

    /**
     * Sorts a associative array by a certain key in descending order.
     *
     * @param  string $key       The name of the key.
     * @param  int    $sortFlags A PHP sort method flags.
     *                           https://www.php.net/manual/ru/function.sort.php
     *
     * @return self Returns instance of The Arrays class.
     */
    public function sortByDesc(string $key, int $sortFlags = SORT_REGULAR): self
    {
        return $this->sortBy($key, 'DESC', $sortFlags);
    }

    /**
     * Sorts a associative array by a certain key in ascending order.
     *
     * @param  string $key       The name of the key.
     * @param  int    $sortFlags A PHP sort method flags.
     *                           https://www.php.net/manual/ru/function.sort.php
     *
     * @return self Returns instance of The Arrays class.
     */
    public function sortByAsc(string $key, int $sortFlags = SORT_REGULAR): self
    {
        return $this->sortBy($key, 'ASC', $sortFlags);
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  string $key     Key
     * @param  mixed  $default Default value
     *
     * @return mixed Value from the array.
     */
    public function pull(string $key, $default = null)
    {
        $value = $this->get($key, $default);

        $this->delete($key);

        return $value;
    }

    /**
     * Divide an array into two arrays.
     * One with keys and the other with values.
     *
     * @return array Returns result array.
     */
    public function divide(): array
    {
        return [array_keys($this->items), array_values($this->items)];
    }

    /**
     * Return the number of items in a given key.
     *
     * @param  int|string|null $key Key
     *
     * @return int Returns count of items.
     */
    public function count($key = null): int
    {
        return count($this->get($key));
    }

    /**
     * Check if the current array is equal to the given $array or not.
     *
     * @param array $array Array to check.
     *
     * @return bool Returns TRUE if current array is equal to the given $array. FALSE otherwise.
     */
    public function isEqual(array $array): bool
    {
        return $this->toArray() === $array;
    }

    /**
     * Determines if an array is associative.
     *
     * @return bool Returns TRUE if an array is associative. FALSE otherwise.
     */
    public function isAssoc(): bool
    {
        $keys = array_keys($this->toArray());

        return array_keys($keys) !== $keys;
    }

    /**
     *  Get all items from stored array.
     *
     * @return array Returns all items from stored array.
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return mixed Returns the array value in the next place that's pointed
     *               to by the internal array pointer, or FALSE if there are no more elements.
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * Rewind the internal iterator position and returns this element.
     *
     * @return mixed Returns the array value in the previous place that's pointed
     *               to by the internal array pointer, or FALSE if there are no more elements.
     */
    public function prev()
    {
        return prev($this->items);
    }

    /**
     * Gets the element of the array at the current internal iterator position.
     *
     * @return mixed Returns the value of the array element that's currently
     *               being pointed to by the internal pointer. It does not move
     *               the pointer in any way. If the internal pointer points beyond
     *               the end of the elements list or the array is empty, returns FALSE.
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * Get the first value from the current array.
     *
     * @return mixed Returns the value of the array.
     */
    public function first()
    {
        $key_first = $this->firstKey();

        if ($key_first === null) {
            return null;
        }

        return $this->get($key_first);
    }

    /**
     * Get the first key from the current array.
     *
     * @return mixed Returns the first key of array if the array is not empty; NULL otherwise.
     */
    public function firstKey()
    {
        return array_key_first($this->toArray());
    }

    /**
     * Get the last value from the current array.
     *
     * @return mixed Returns the value of the array.
     */
    public function last()
    {
        $key_last = $this->lastKey();

        if ($key_last === null) {
            return null;
        }

        return $this->get($key_last);
    }

    /**
     * Get the last key from the current array.
     *
     * @return mixed Returns the last key of array if the array is not empty; NULL otherwise.
     */
    public function lastKey()
    {
        return array_key_last($this->toArray());
    }

    /**
     * Create a chunked version of current array.
     *
     * @param int  $size         Size of each chunk.
     * @param bool $preserveKeys Whether array keys are preserved or no.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function chunk(int $size, bool $preserveKeys = false): self
    {
        $this->items = array_chunk($this->items, $size, $preserveKeys);

        return $this;
    }

    /**
     * Create an array using the current array as keys and the other array as values.
     *
     * @param mixed $items Items.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function combine($items = null): self
    {
        $array = self::getArray($items);

        if (count($this->items) === count($array)) {
            $result = array_combine($this->items, $array);
            if ($result === false) {
                $this->items = [];
            } else {
                $this->items = $result;
            }
        } else {
            $this->items = [];
        }

        return $this;
    }

    /**
     * Compute the current array values which not present in the given one.
     *
     * @param mixed $items Items for diff.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function diff($items = null): self
    {
        $array = self::getArray($items);

        $this->items = array_diff($this->items, $array);

        return $this;
    }

    /**
     * Filter the current array for elements satisfying the predicate $callback function.
     *
     * @param callable $callback The callback function.
     * @param int      $flag     Determining what arguments are sent to callback:
     *                             ARRAY_FILTER_USE_KEY - pass key as the only argument
     *                                                    to callback instead of the value.
     *                             ARRAY_FILTER_USE_BOTH - pass both value and key as arguments
     *                                                     to callback instead of the value.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function filter(callable $callback, int $flag = ARRAY_FILTER_USE_BOTH): self
    {
        $this->items = array_filter($this->items, $callback, $flag);

        return $this;
    }

    /**
     * Exchanges all keys of current array with their associated values.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function flip(): self
    {
        $this->items = array_flip($this->items);

        return $this;
    }

    /**
     * Compute the current array values which present in the given one.
     *
     * @param mixed $items Items for intersect.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function intersect($items = null): self
    {
        $array = self::getArray($items);

        $this->items = array_intersect($this->items, $array);

        return $this;
    }

    /**
     * Compute the current array values with additional index check.
     *
     * @param array $items Items for intersect.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function intersectAssoc($items = null): self
    {
        $array = self::getArray($items);

        $this->items = array_intersect_assoc($this->items, $array);

        return $this;
    }

    /**
     * Compute the current array using keys for comparison which present in the given one.
     *
     * @param mixed $items Items for intersect.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function intersectKey($items = null): self
    {
        $array = self::getArray($items);
        
        $this->items = array_intersect_key($this->items, $array);

        return $this;
    }

    /**
     * Apply the given $callback function to the every element of the current array,
     * collecting the results.
     *
     * @param callable $callback The callback function.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function map(callable $callback): self
    {
        $this->items = array_map($callback, $this->items);

        return $this;
    }

    /**
     * Merge the current array with the provided one.
     *
     * @param mixed $items     Items to merge with (overwrites).
     * @param bool  $recursive Whether array will be merged recursively or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function merge($items = null, bool $recursive = false): self
    {
        $array = self::getArray($items);

        if ($recursive) {
            $this->items = array_merge_recursive($this->items, $array);
        } else {
            $this->items = array_merge($this->items, $array);
        }

        return $this;
    }

    /**
     * Pad the current array to the specified size with a given value.
     *
     * @param int   $size  Size of the result array.
     * @param mixed $value Empty value by default.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function pad(int $size, $value): self
    {
        $this->items = array_pad($this->items, $size, $value);

        return $this;
    }

    /**
     * Returns one or a specified number of items randomly from the array.
     *
     * @param int|null $number Number of items to return.
     *
     * @return mixed Returns the value of the array.
     */
    public function random(?int $number = null)
    {
        $array = $this->toArray();

        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            $number = $count;
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ((int) $number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        foreach ((array) $keys as $key) {
            $results[$key] = $array[$key];
        }

        return $results;
    }

    /**
     * Create a numerically re-indexed array based on the current array.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function reindex(): self
    {
        $this->items = array_values($this->items);

        return $this;
    }

    /**
     * Calculate the product of values in the current array.
     *
     * @return float|int Returns the product as an integer or float.
     */
    public function product()
    {
        return array_product($this->items);
    }

    /**
     * Calculate the sum of values in the current array.
     *
     * @return float|int Returns the sum as an integer or float.
     */
    public function sum()
    {
        return array_sum($this->items);
    }

    /**
     * Replace values in the current array with values in the given one
     * that have the same key.
     *
     * @param mixed $items     Items of replacing values.
     * @param bool  $recursive Whether array will be replaced recursively or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function replace($items = null, bool $recursive = false): self
    {
        $array = self::getArray($items);

        if ($recursive) {
            $this->items = array_replace_recursive($this->items, $array);
        } else {
            $this->items = array_replace($this->items, $array);
        }

        return $this;
    }

    /**
     * Reverse the values order of the current array.
     *
     * @param bool $preserveKeys Whether array keys are preserved or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function reverse(bool $preserveKeys = false): self
    {
        $this->items = array_reverse($this->items, $preserveKeys);

        return $this;
    }

    /**
     * Extract a slice of the current array.
     *
     * @param int      $offset       Slice begin index.
     * @param int|null $length       Length of the slice. Default is null.
     * @param bool     $preserveKeys Whether array keys are preserved or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        $this->items = array_slice($this->items, $offset, $length, $preserveKeys);

        return $this;
    }

    /**
     * Skip the first count items.
     *
     * @param  int $count Count of first items to skip.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function skip(int $count): self
    {
        return $this->slice($count);
    }

    /**
     * Verifies that all elements pass the test of the given callback.
     *
     * @param Closure $callback Function with (value, key) parameters and returns TRUE/FALSE
     *
     * @return bool TRUE if all elements pass the test, FALSE if if fails for at least one element
     */
    public function every(Closure $callback): bool
    {
        foreach ($this->items as $key => $value) {
            if ($callback($value, $key) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extract a slice of the current array with specific offset.
     *
     * @param int  $offset       Slice begin index.
     * @param bool $preserveKeys Whether array keys are preserved or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function offset(int $offset, bool $preserveKeys = false): self
    {
        $this->items = array_slice($this->items, $offset, null, $preserveKeys);

        return $this;
    }

    /**
     * Extract a slice of the current array with offset 0 and specific length.
     *
     * @param int|null $length       Length of the slice. Default is null.
     * @param bool     $preserveKeys Whether array keys are preserved or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function limit(?int $length = null, bool $preserveKeys = false): self
    {
        $this->items = array_slice($this->items, 0, $length, $preserveKeys);

        return $this;
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param  int|null $seed An arbitrary integer seed value.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function shuffle(?int $seed = null): self
    {
        $array = $this->items;

        if (is_null($seed)) {
            shuffle($array);
        } else {
            mt_srand($seed);
            shuffle($array);
            mt_srand();
        }

        $this->items = $array;

        return $this;
    }

    /**
     * Convert the current array into a query string.
     *
     * @return string Returns query string.
     */
    public function toQuery(): string
    {
        return http_build_query($this->toArray(), '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Get all items from stored array and convert them to array.
     *
     * @return array Returns array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Convert the current array to JSON.
     *
     * @param int $options Bitmask consisting of encode options
     * @param int $depth   Encode Depth. Set the maximum depth. Must be greater than zero.
     *
     * @return string Returns current array as json.
     */
    public function toJson(int $options = 0, int $depth = 512): string
    {
        $options = ($options ? 0 : JSON_UNESCAPED_UNICODE)
            | JSON_UNESCAPED_SLASHES
            | ($options ? JSON_PRETTY_PRINT : 0)
            | (defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0);

        $result = json_encode($this->toArray(), $options, $depth);

        if ($result === false) {
            return '';
        }

        return $result;
    }

    /**
     * Convert the current array to string recursively implodes an array with optional key inclusion.
     *
     * @param string $glue        Value that glues elements together.
     * @param bool   $includeKeys Include keys before their values.
     * @param bool   $trimAll     Trim ALL whitespace from string.
     *
     * @return string Returns current array as string.
     */
    public function toString(string $glue = ',', bool $includeKeys = false, bool $trimAll = true): string
    {
        $string = '';

        $array = $this->toArray();

        // Recursively iterates array and adds key/value to glued string
        array_walk_recursive($array, static function ($value, $key) use ($glue, $includeKeys, &$string): void {
            $includeKeys and $string .= $key . $glue;
            $string                  .= $value . $glue;
        });

        // Removes last $glue from string
        mb_strlen($glue) > 0 and $string = mb_substr($string, 0, -mb_strlen($glue));

        // Trim ALL whitespace
        $trimAll and $string = preg_replace('/(\s)/ixsm', '', $string);

        if (is_null($string)) {
            $string = '';
        }

        return $string;
    }

    /**
     * Remove duplicate values from the current array.
     *
     * @param int $sortFlags Sort flags used to modify the sorting behavior.
     *                       Sorting type flags:
     *                       https://www.php.net/manual/en/function.array-unique
     *
     * @return self Returns instance of The Arrays class.
     */
    public function unique(int $sortFlags = SORT_STRING): self
    {
        $this->items = array_unique($this->items, $sortFlags);

        return $this;
    }

    /**
     * Apply the given function to the every element of the current array,
     * discarding the results.
     *
     * @param callable $callback  The callback function.
     * @param bool     $recursive Whether array will be walked recursively or no. Default is false.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function walk(callable $callback, bool $recursive = false): self
    {
        if ($recursive) {
            array_walk_recursive($this->items, $callback);
        } else {
            array_walk($this->items, $callback);
        }

        return $this;
    }

    /**
     * Get the values of a single column from an arrays items.
     *
     * @param mixed $columnKey The column of values to return.
     *                         This value may be an integer key of the column you wish to retrieve,
     *                         or it may be a string key name for an associative array or property name.
     *                         It may also be NULL to return complete arrays or objects
     *                         (this is useful together with index_key to reindex the array).
     * @param mixed $indexKey  The column to use as the index/keys for the returned array.
     *                         This value may be the integer key of the column, or it may be the string key name.
     *                         The value is cast as usual for array keys (however, objects supporting conversion to string are also allowed).
     *
     * @return self Returns instance of The Arrays class.
     */
    public function column($columnKey = null, $indexKey = null): self
    {
        $this->items = array_column($this->items, $columnKey, $indexKey);

        return $this;
    }

    /**
     * Return slice of an array with just a given keys.
     *
     * @param array $keys List of keys to return.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function only(array $keys): self
    {
        $this->items = array_intersect_key($this->items, array_flip($keys));

        return $this;
    }

    /**
     * Return slice of an array with just a given keys.
     *
     * @param array $keys List of keys to return.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function except(array $keys): self
    {
        $this->items = $this->delete($keys)->toArray();
        
        return $this;
    }

    /**
     * Creates a new Arrays object with the same items.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function copy(): self
    {
        return clone $this;
    }

    /**
     * Extract array items with every nth item from the array.
     *
     * @param int $step   Step width.
     * @param int $offset Number of items to start from. Default is 0.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function nth(int $step, int $offset = 0): self
    {
        $pos    = 0;
        $result = [];

        foreach ($this->items as $key => $item) {
            if ($pos++ % $step !== $offset) {
                continue;
            }

            $result[$key] = $item;
        }

        $this->items = $result;

        return $this;
    }

    /**
     * Sorts array by values.
     *
     * @param  string $direction    Order type DESC (descending) or ASC (ascending)
     * @param  int    $sortFlags    A PHP sort method flags.
     *                              https://www.php.net/manual/ru/function.sort.php
     * @param bool   $preserveKeys Maintain index association
     *
     * @return self Returns instance of The Arrays class.
     */
    public function sort(string $direction = 'ASC', int $sortFlags = SORT_REGULAR, bool $preserveKeys = false): self
    {
        switch ($direction) {
            case 'DESC':
                if ($preserveKeys) {
                    arsort($this->items, $sortFlags);
                } else {
                    rsort($this->items, $sortFlags);
                }

                break;

            case 'ASC':
            default:
                if ($preserveKeys) {
                    asort($this->items, $sortFlags);
                } else {
                    sort($this->items, $sortFlags);
                }
        }

        return $this;
    }

    /**
     * Sorts array by keys.
     *
     * @param  string $direction Order type DESC (descending) or ASC (ascending)
     * @param  int    $sortFlags A PHP sort method flags.
     *                           https://www.php.net/manual/ru/function.sort.php
     *
     * @return self Returns instance of The Arrays class.
     */
    public function sortKeys(string $direction = 'ASC', int $sortFlags = SORT_REGULAR): self
    {
        switch ($direction) {
            case 'DESC':
                krsort($this->items, $sortFlags);
                break;

            case 'ASC':
            default:
                ksort($this->items, $sortFlags);
        }

        return $this;
    }

    /**
     * Sorts the array values with a user-defined comparison function and maintain index association.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function customSortValues(callable $callback): self
    {
        usort($this->items, $callback);

        return $this;
    }

    /**
     * Sorts the array keys with a user-defined comparison function and maintain index association.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function customSortKeys(callable $callback): self
    {
        uksort($this->items, $callback);

        return $this;
    }

    /**
     * Whether an offset exists.
     *
     * @param mixed $offset An offset to check for.
     *
     * @return bool Return TRUE key exists in the array, FALSE otherwise.
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Returns the value of the array.
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Assign a value to the specified offset.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @return void Return void.
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Unset an offset.
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void Return void.
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }

    /**
     * Dumps the arrays items using the given function (print_r by default).
     *
     * @param callable $callback Function receiving the arrays items as parameter.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function dump(?callable $callback = null): self
    {
        $callback ? $callback($this->items) : print_r($this->items);

        return $this;
    }

    /**
     * Dumps the arrays items using the given function (print_r by default) and die.
     *
     * @param callable $callback Function receiving the arrays items as parameter.
     *
     * @return void Return void.
     */
    public function dd(?callable $callback = null): void
    {
        $this->dump($callback);

        die;
    }

    /**
     * Filters the array items by a given condition.
     *
     * @param string $key      Key of the array for comparison.
     * @param string $operator Operator used for comparison.
     *                         operators: in, nin, lt, <, lte,
     *                                    >, gt, gte, >=, contains, ncontains
     *                                    >=, <=, like, nlike, regexp, nregexp,
     *                                    eq, =, neq, !=, starts_with,
     *                                    ends_with, between, nbetween, older, newer
     * @param mixed  $value    Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function where(string $key, string $operator, $value): self
    {
        $encoding = mb_internal_encoding();
        $operator = mb_strtolower($operator, $encoding);

        $this->items = array_filter(
            $this->items,
            static function ($item) use ($key, $operator, $value, $encoding) {
                $item = (array) $item;

                if (! static::create($item)->has($key)) {
                    return false;
                }

                $valueToCompare = static::create($item)->get($key);

                switch ($operator) {
                    case 'in':
                        return (bool) (in_array($valueToCompare, (array) $value));

                    case 'nin':
                        return (bool) (! in_array($valueToCompare, (array) $value));

                    case 'lt':
                    case '<':
                        return (bool) ($valueToCompare < $value);

                    case 'gt':
                    case '>':
                        return (bool) ($valueToCompare > $value);

                    case 'lte':
                    case '<=':
                        return (bool) ($valueToCompare <= $value);

                    case 'gte':
                    case '>=':
                        return (bool) ($valueToCompare >= $value);

                    case 'eq':
                    case '=':
                        return (bool) ($valueToCompare === $value);

                    case 'neq':
                    case '<>':
                    case '!=':
                        return (bool) ($valueToCompare !== $value);

                    case 'contains':
                    case 'like':
                        return (bool) (mb_strpos($valueToCompare, $value, 0, $encoding) !== false);

                    case 'ncontains':
                    case 'nlike':
                        return (bool) (mb_strpos($valueToCompare, $value, 0, $encoding) === false);

                    case 'between':
                        $value = (array) $value;

                        return (bool) (($valueToCompare >= current($value) && $valueToCompare <= end($value)) !== false);

                    case 'nbetween':
                        $value = (array) $value;

                        return (bool) (($valueToCompare >= current($value) && $valueToCompare <= end($value)) === false);

                    case 'starts_with':
                        return (bool) (strncmp($valueToCompare, $value, mb_strlen($value)) === 0);

                    case 'ends_with':
                        return (bool) (mb_substr($valueToCompare, -mb_strlen($value), null, $encoding) === $value);

                    case 'newer':
                        return (bool) (strtotime($valueToCompare) > strtotime($value));

                    case 'older':
                        return (bool) (strtotime($valueToCompare) < strtotime($value));

                    case 'regexp':
                        return (bool) (preg_match("/{$value}/ium", $valueToCompare));

                    case 'nregexp':
                        return (bool) (! preg_match("/{$value}/ium", $valueToCompare));

                    default:
                        return false;
                }
            },
            ARRAY_FILTER_USE_BOTH
        );

        return $this;
    }

    /**
     * Filters the array items by the given key value pair.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereIn(string $key, $value): self
    {
        return $this->where($key, 'in', $value);
    }

    /**
     * Filters the array items by the given key value pair.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereNotIn(string $key, $value): self
    {
        return $this->where($key, 'nin', $value);
    }

    /**
     * Filters the array items by the given key is between the given values.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereBetween(string $key, $value): self
    {
        return $this->where($key, 'between', $value);
    }

    /**
     * Filters the array items by the given key is not between the given values.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereNotBetween(string $key, $value): self
    {
        return $this->where($key, 'nbetween', $value);
    }

    /**
     * Filters the array items by the given key is less the given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereLess(string $key, $value): self
    {
        return $this->where($key, 'lt', $value);
    }

    /**
     * Filters the array items by the given key is less or equal the given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereLessOrEqual(string $key, $value): self
    {
        return $this->where($key, 'lte', $value);
    }

    /**
     * Filters the array items by the given key is greater the given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereGreater(string $key, $value): self
    {
        return $this->where($key, 'gt', $value);
    }

    /**
     * Filters the array items by the given key is greater or equal the given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereGreaterOrEqual(string $key, $value): self
    {
        return $this->where($key, 'gte', $value);
    }

    /**
     * Filters the array items by the given key is contains given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereContains(string $key, $value): self
    {
        return $this->where($key, 'contains', $value);
    }

    /**
     * Filters the array items by the given key is not contains given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereNotContains(string $key, $value): self
    {
        return $this->where($key, 'ncontains', $value);
    }

    /**
     * Filters the array items by the given key is equal given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereEqual(string $key, $value): self
    {
        return $this->where($key, 'eq', $value);
    }

    /**
     * Filters the array items by the given key is not equal given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereNotEqual(string $key, $value): self
    {
        return $this->where($key, 'neq', $value);
    }

    /**
     * Filters the array items by the given key is starts with given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereStartsWith(string $key, $value): self
    {
        return $this->where($key, 'starts_with', $value);
    }

    /**
     * Filters the array items by the given key is ends with given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereEndsWith(string $key, $value): self
    {
        return $this->where($key, 'ends_with', $value);
    }

    /**
     * Filters the array items by the given key is newer given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereNewer(string $key, $value): self
    {
        return $this->where($key, 'newer', $value);
    }

    /**
     * Filters the array items by the given key is older given value.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereOlder(string $key, $value): self
    {
        return $this->where($key, 'older', $value);
    }

    /**
     * Filters the array items by the given key is matches to given regexp.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereRegexp(string $key, $value): self
    {
        return $this->where($key, 'regexp', $value);
    }

    /**
     * Filters the array items by the given key is not matches to given regexp.
     *
     * @param string $key   Key of the array for comparison.
     * @param mixed  $value Value used for comparison.
     *
     * @return self Returns instance of The Arrays class.
     */
    public function whereNotRegexp(string $key, $value): self
    {
        return $this->where($key, 'nregexp', $value);
    }

    /**
     * Create a new iterator from an ArrayObject instance
     *
     * @return ArrayIterator Returns instance of The ArrayIterator class.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Returns array of the given items.
     *
     * @param mixed $items Items
     *
     * @return array Returns array of the given items.
     */
    protected static function getArray($items): array
    {
        $isJson = function ($items) {
            json_decode($items);
            return json_last_error() === JSON_ERROR_NONE;
        };

        if (is_string($items) && $isJson($items)) {
            return self::createFromJson($items)->toArray();
        }

        if (is_array($items)) {
            return $items;
        }

        if ($items instanceof self) {
            return $items->toArray();
        }

        if ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return $items !== null ? [$items] : [];
    }
}
