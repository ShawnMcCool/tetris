<?php

declare(strict_types=1);

namespace Glowy\Strings;

use ArrayAccess;
use ArrayIterator;
use Glowy\Macroable\Macroable;
use Closure;
use Countable;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;

use function abs;
use function array_count_values;
use function array_merge;
use function array_pop;
use function array_reverse;
use function array_shift;
use function array_walk;
use function arsort;
use function base64_decode;
use function base64_encode;
use function count;
use function ctype_lower;
use function end;
use function explode;
use function filter_var;
use function floatval;
use function func_get_args;
use function hash;
use function hash_algos;
use function implode;
use function in_array;
use function intval;
use function is_array;
use function is_numeric;
use function is_object;
use function json_decode;
use function json_last_error;
use function lcfirst;
use function ltrim;
use function mb_convert_case;
use function mb_ereg_match;
use function mb_internal_encoding;
use function mb_split;
use function mb_strimwidth;
use function mb_stripos;
use function mb_strlen;
use function mb_strpos;
use function mb_strripos;
use function mb_strrpos;
use function mb_strtolower;
use function mb_strtoupper;
use function mb_strwidth;
use function mb_substr;
use function mb_substr_count;
use function method_exists;
use function number_format;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function preg_split;
use function random_int;
use function range;
use function rsort;
use function rtrim;
use function shuffle;
use function similar_text;
use function sort;
use function sprintf;
use function str_pad;
use function str_repeat;
use function str_replace;
use function strip_tags;
use function strncmp;
use function strpos;
use function strrpos;
use function strstr;
use function strval;
use function substr_replace;
use function trim;
use function ucwords;
use function unserialize;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;
use const FILTER_VALIDATE_EMAIL;
use const FILTER_VALIDATE_IP;
use const FILTER_VALIDATE_MAC;
use const FILTER_VALIDATE_URL;
use const JSON_ERROR_NONE;
use const MB_CASE_TITLE;
use const PREG_SPLIT_NO_EMPTY;
use const STR_PAD_BOTH;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

class Strings implements ArrayAccess, Countable, IteratorAggregate
{
    use Macroable;

    /**
     * The underlying string value.
     */
    protected string $string;

    /**
     * The string's encoding, which should be one of the mbstring module's
     * supported encodings.
     */
    protected string $encoding;

    /**
     * Initializes a Strings object and assigns both $string and $encoding properties
     * the supplied values. $string is cast to a string prior to assignment. Throws
     * an InvalidArgumentException if the first argument is an array or object
     * without a __toString method.
     *
     * @param mixed $string   Value to modify, after being cast to string. Default: ''
     * @param mixed $encoding The character encoding. Default: UTF-8
     *
     * @return void
     */
    public function __construct($string = '', $encoding = 'UTF-8')
    {
        if (is_array($string)) {
            throw new InvalidArgumentException('Passed value cannot be an array');
        }

        if (
            is_object($string)
            &&
            ! method_exists($string, '__toString')
        ) {
            throw new InvalidArgumentException('Passed object must have a __toString method');
        }

        if ($encoding === null) {
            $this->encoding = mb_internal_encoding();
        } else {
            $this->encoding = (string) $encoding;
        }

        $this->string = (string) $string;
    }

    /**
     * Returns the value in $string.
     *
     * @return string Returns string.
     */
    public function __toString(): string
    {
        return (string) $this->string;
    }

    /**
     * Create a new stringable object from the given string.
     *
     * Initializes a Strings object and assigns both $string and $encoding properties
     * the supplied values. $string is cast to a string prior to assignment. Throws
     * an InvalidArgumentException if the first argument is an array or object
     * without a __toString method.
     *
     * @param mixed  $string   Value to modify, after being cast to string. Default: ''
     * @param string $encoding The character encoding. Default: UTF-8
     *
     * @return self Returns instance of The Strings class.
     */
    public static function create($string = '', string $encoding = 'UTF-8'): self
    {
        return new Strings($string, $encoding);
    }

    /**
     * Set the character encoding.
     *
     * @param string $encoding Character encoding.
     *
     * @return self Returns instance of The Strings class.
     */
    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Get character encoding.
     *
     * @return string Returns internal encoding.
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Removes any leading and traling slashes from a string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function trimSlashes(): self
    {
        $this->string = (string) $this->trim('/');

        return $this;
    }

    /**
     * Reduces multiple slashes in a string to single slashes.
     *
     * @return self Returns instance of The Strings class.
     */
    public function reduceSlashes(): self
    {
        $this->string = preg_replace('#(?<!:)//+#', '/', $this->string);

        return $this;
    }

    /**
     * Removes single and double quotes from a string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function stripQuotes(): self
    {
        $this->string = str_replace(['"', "'"], '', $this->string);

        return $this;
    }

    /**
     * Convert single and double quotes to entities.
     *
     * @param  string $string String with single and double quotes
     *
     * @return self Returns instance of The Strings class.
     */
    public function quotesToEntities(): self
    {
        $this->string = str_replace(["\'", '"', "'", '"'], ['&#39;', '&quot;', '&#39;', '&quot;'], $this->string);

        return $this;
    }

    /**
     * Standardize line endings to unix-like.
     *
     * @return self Returns instance of The Strings class.
     */
    public function normalizeNewLines(): self
    {
        $this->string = str_replace(["\r\n", "\r"], "\n", $this->string);

        return $this;
    }

    /**
     * Normalize white-spaces to a single space.
     *
     * @return self Returns instance of The Strings class.
     */
    public function normalizeSpaces(): self
    {
        $this->string = preg_replace('/\s+/', ' ', $this->string);

        return $this;
    }

    /**
     * Creates a random string of characters.
     *
     * @param  int    $length   The number of characters. Default is 16
     * @param  string $keyspace The keyspace
     *
     * @return self Returns instance of The Strings class.
     */
    public function random(int $length = 64, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): self
    {
        if ($length <= 0) {
            $length = 1;
        }

        $pieces = [];
        $max    = static::create($keyspace, '8bit')->length() - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        $this->string = implode('', $pieces);

        return $this;
    }

    /**
     * Add's _1 to a string or increment the ending number to allow _2, _3, etc.
     *
     * @param  int    $first     Start with
     * @param  string $separator Separator
     *
     * @return self Returns instance of The Strings class.
     */
    public function increment(int $first = 1, string $separator = '_'): self
    {
        preg_match('/(.+)' . $separator . '([0-9]+)$/', $this->string, $match);

        $this->string = isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $this->string . $separator . $first;

        return $this;
    }

    /**
     * Returns a repeated string given a multiplier.
     *
     * @param int $multiplier The number of times to repeat the string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function repeat(int $multiplier): self
    {
        $this->string = str_repeat($this->string, $multiplier);

        return $this;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param  int    $limit  Limit of characters
     * @param  string $append Text to append to the string IF it gets truncated
     *
     * @return self Returns instance of The Strings class.
     */
    public function limit(int $limit = 100, string $append = '...'): self
    {
        if (mb_strwidth($this->string, 'UTF-8') <= $limit) {
            $this->string = $this->string;
        } else {
            $this->string = static::create(mb_strimwidth($this->string, 0, $limit, '', $this->encoding), $this->encoding)->trimRight() . $append;
        }

        return $this;
    }

    /**
     * Masks a portion of a string with a repeated character.
     *
     * @param  string   $character Character.
     * @param  int      $index     Index.
     * @param  int|null $length    Length.
     * 
     * @return self Returns instance of The Strings class.
     */
    public function mask(string $character, int $index, $length = null)
    {
        if ($character === '') {
            return $this->string;
        }

        if ($length === null) {
            $length = mb_strlen($this->string, $this->encoding);
        }

        $segment = static::create($this->string, $this->encoding)->substr($index, $length)->toString();

        if ($segment === '') {
            return $this->string;
        }

        $start = mb_substr($this->toString(), 0, mb_strpos($this->toString(), $segment, 0, $this->encoding), $this->encoding);
        $end = mb_substr($this->toString(), mb_strpos($this->toString(), $segment, 0, $this->encoding) + mb_strlen($segment, $this->encoding));

        return $start . str_repeat(mb_substr($character, 0, 1, $this->encoding), mb_strlen($segment, $this->encoding)) . $end;
    }

    /**
     * Convert the given string to title case for each word.
     *
     * @return self Returns instance of The Strings class.
     */
    public function headline(): self
    {
        $parts = static::create($this->string)->replace(' ', '_')->segments('_');

        if (count($parts) > 1) {
            $capParts = [];
            foreach($parts as $part) {
                $capParts[] = static::create($part)->capitalize()->toString();
            }
        }

        $this->string = implode(' ', preg_split('/(?=[A-Z])/', static::create(implode($capParts))->studly()->toString(), -1, PREG_SPLIT_NO_EMPTY));

        return $this;
    }

    /**
     * Transform the given string with random capitalization applied.
     *
     * @return self Returns instance of The Strings class.
     */
    public function sponge(): self
    {
        $result = '';

        foreach (static::create($this->string)->chars() as $char) {
            if (mt_rand(0, 100) > 50) {
                $result .= static::create($char)->upper()->toString();
            } else {
                $result .= static::create($char)->lower()->toString();
            }
        }

        $this->string = $result;

        return $this;
    }

    /**
     * Transform the given string by swapping every character from upper to lower case, or lower to upper case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function swap(): self
    {
        $result = '';

        foreach (static::create($this->string)->chars() as $char) {
            if (static::create($char)->isUpper()) {
                $result .= static::create($char)->lower()->toString();
            } else {
                $result .= static::create($char)->upper()->toString();
            }
        }

        $this->string = $result;

        return $this;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function lower(): self
    {
        $this->string = mb_strtolower($this->string, $this->encoding);

        return $this;
    }

    /**
     * Convert the given string to upper-case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function upper(): self
    {
        $this->string = mb_strtoupper($this->string, $this->encoding);

        return $this;
    }

    /**
     * Convert a string to studly caps case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function studly(): self
    {
        $this->string = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $this->string)));

        return $this;
    }

    /**
     * Convert a string to snake case.
     *
     * @param  string $delimiter Delimeter
     *
     * @return self Returns instance of The Strings class.
     */
    public function snake(string $delimiter = '_'): self
    {
        $key = $this->string;

        if (! ctype_lower($this->string)) {
            $string = preg_replace('/\s+/u', '', ucwords($this->string));
            $string = static::create(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $string), $this->encoding)->lower();
        
            $this->string = $string->toString();
        }

        return $this;
    }

    /**
     * Convert a string to camel case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function camel(): self
    {
        $this->string = lcfirst((string) static::create($this->string, $this->encoding)->studly());

        return $this;
    }

    /**
     * Convert a string to kebab case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function kebab(): self
    {
        $this->string = static::create($this->string, $this->encoding)->snake('-')->toString();

        return $this;
    }

    /**
     * Limit the number of words in a string.
     *
     * @param  int    $words  Words limit
     * @param  string $append Text to append to the string IF it gets truncated
     *
     * @return self Returns instance of The Strings class.
     */
    public function wordsLimit(int $words = 100, string $append = '...'): self
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $this->string, $matches);

        if (! isset($matches[0]) || static::create($this->string, $this->encoding)->length() === static::create($matches[0], $this->encoding)->length()) {
            $this->string = $this->string;
        }

        $this->string = static::create($matches[0], $this->encoding)->trimRight() . $append;

        return $this;
    }

    /**
     * Get words from the string.
     *
     * @param string $ignore Ingnore symbols.
     *
     * @return array Returns words array.
     */
    public function words(string $ignore = '?!;:,.'): array
    {
        $words = preg_split('/[\s' . $ignore . ']+/', $this->string);

        empty(end($words)) and array_pop($words);

        return $words;
    }

    /**
     * Get array of individual lines in the string.
     *
     * @return array Returns array of lines.
     */
    public function lines(): array
    {
        $lines = preg_split('/\r\n|\n|\r/', $this->string);

        empty(end($lines)) and array_pop($lines);

        return $lines;
    }

    /**
     * Returns the length of the string, analog to length().
     *
     * @return int Returns string length.
     */
    public function count(): int
    {
        return $this->length();
    }

    /**
     * Returns the number of occurrences of $substring in the given string.
     * By default, the comparison is case-sensitive, but can be made insensitive
     * by setting $caseSensitive to false.
     *
     * @param  string $substring     The substring to search.
     * @param  bool   $caseSensitive Whether or not to enforce case-sensitivity. Default is true.
     *
     * @return int Returns the number of occurrences of $substring in the given string.
     */
    public function countSubString(string $substring, bool $caseSensitive = true): int
    {
        if ($caseSensitive) {
            return mb_substr_count($this->string, $substring);
        }

        return mb_substr_count(
            (string) static::create($this->string, $this->encoding)->lower(),
            (string) static::create($substring, $this->encoding)->lower()
        );
    }

    /**
     * Get words count from the string.
     *
     * @param string $ignore Ingnore symbols.
     *
     * @return int Returns words count.
     */
    public function wordsCount(string $ignore = '?!;:,.'): int
    {
        $words = preg_split('/[\s' . $ignore . ']+/', $this->string);

        empty(end($words)) and array_pop($words);

        return count($words);
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string|string[] $needles       The string to find in haystack.
     * @param  bool            $caseSensitive Whether or not to enforce case-sensitivity. Default is true.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function contains($needles, bool $caseSensitive = true): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && static::create($this->string, $this->encoding)->indexOf($needle, 0, $caseSensitive) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string contains all array values.
     *
     * @param  string[] $needles       The array of strings to find in haystack.
     * @param  bool     $caseSensitive Whether or not to enforce case-sensitivity. Default is true.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function containsAll(array $needles, bool $caseSensitive = true): bool
    {
        foreach ($needles as $needle) {
            if (! static::create($this->string, $this->encoding)->contains($needle, $caseSensitive)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if a given string contains any of array values.
     *
     * @param  string   $haystack      The string being checked.
     * @param  string[] $needles       The array of strings to find in haystack.
     * @param  bool     $caseSensitive Whether or not to enforce case-sensitivity. Default is true.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function containsAny(array $needles, bool $caseSensitive = true): bool
    {
        foreach ($needles as $needle) {
            if (static::create($this->string, $this->encoding)->contains($needle, $caseSensitive)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Converts the first character of a string to upper case
     * and leaves the other characters unchanged.
     *
     * @return self Returns instance of The Strings class.
     */
    public function ucfirst(): self
    {
        $this->string = static::create(static::create($this->string, $this->encoding)->substr(0, 1))->upper() . static::create($this->string, $this->encoding)->substr(1);

        return $this;
    }

    /**
     * Converts the first character of every word of string to upper case and the others to lower case.
     *
     * @return self Returns instance of The Strings class.
     */
    public function capitalize(): self
    {
        $this->string = mb_convert_case($this->string, MB_CASE_TITLE, $this->encoding);

        return $this;
    }

    /**
     * Return the length of the given string.
     *
     * @return int Returns the length of the given string.
     */
    public function length(): int
    {
        return mb_strlen($this->string, $this->encoding);
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param  int      $start  If start is non-negative, the returned string will
     *                          start at the start'th position in $string, counting from zero.
     *                          For instance, in the string 'abcdef', the character at position
     *                          0 is 'a', the character at position 2 is 'c', and so forth.
     * @param  int|null $length Maximum number of characters to use from string.
     *                          If omitted or NULL is passed, extract all characters to the end of the string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function substr(int $start, ?int $length = null): self
    {
        $this->string = mb_substr($this->string, $start, $length, $this->encoding);

        return $this;
    }

    /**
     * Returns the index of the first occurrence of $needle in the string,
     * and false if not found. Accepts an optional offset from which to begin
     * the search.
     *
     * @param int|string $needle        The string to find in haystack.
     * @param int        $offset        The search offset. If it is not specified, 0 is used.
     * @param bool       $caseSensitive Whether or not to enforce case-sensitivity. Default is true.
     *
     * @return mixed Returns the index of the first occurrence of $needle in the string,
     * and false if not found.
     */
    public function indexOf($needle, int $offset = 0, bool $caseSensitive = true)
    {
        if ($needle === '' || $this->string === '') {
            return false;
        }

        if ($caseSensitive) {
            return mb_strpos((string) $this->string, $needle, $offset, $this->encoding);
        }

        return mb_stripos((string) $this->string, $needle, $offset, $this->encoding);
    }

    /**
     * Returns the index of the last occurrence of $needle in the string, and false if not found.
     * Accepts an optional $offset from which to begin the search. Offsets may be negative to
     * count from the last character in the string.
     *
     * @param int|string $needle        The string to find in haystack.
     * @param int        $offset        The search offset. If it is not specified, 0 is used.
     * @param bool       $caseSensitive Whether or not to enforce case-sensitivity. Default is true.
     *
     * @return mixed Returns the index of the last occurrence of $needle in the string, and false if not found.
     */
    public function indexOfLast(string $needle, int $offset = 0, bool $caseSensitive = true)
    {
        if ($needle === '' || $this->string === '') {
            return false;
        }

        $max_length = static::create($this->string, $this->encoding)->length();

        if ($offset < 0) {
            $offset = $max_length - (int) abs($offset);
        }

        if ($offset > $max_length || $offset < 0) {
            return false;
        }

        if ($caseSensitive) {
            return mb_strrpos((string) $this->string, $needle, $offset, $this->encoding);
        }

        return mb_strripos((string) $this->string, $needle, $offset, $this->encoding);
    }

    /**
     * Strip whitespace (or other characters) from the beginning and end of a string.
     *
     * @param string $character_mask Stripped characters can also be specified using the character_mask parameter.
     *
     * @return self Returns instance of The Strings class.
     */
    public function trim(?string $character_mask = null): self
    {
        $this->string = trim(...array_merge([$this->string], func_get_args()));

        return $this;
    }

    /**
     * Strip whitespace (or other characters) from the beginning of a string.
     *
     * @param string $character_mask Stripped characters can also be specified using the character_mask parameter.
     *
     * @return self Returns instance of The Strings class.
     */
    public function trimLeft(?string $character_mask = null): self
    {
        $this->string = ltrim(...array_merge([$this->string], func_get_args()));

        return $this;
    }

    /**
     * Strip whitespace (or other characters) from the end of a string.
     *
     * @param string $character_mask Stripped characters can also be specified using the character_mask parameter.
     *
     * @return self Returns instance of The Strings class.
     */
    public function trimRight(?string $character_mask = null): self
    {
        $this->string = rtrim(...array_merge([$this->string], func_get_args()));

        return $this;
    }

    /**
     * Reverses string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function reverse(): self
    {
        $result = '';

        for ($i = static::create($this->string, $this->encoding)->length(); $i >= 0; $i--) {
            $result .= (string) static::create($this->string, $this->encoding)->substr($i, 1);
        }

        $this->string = $result;

        return $this;
    }

    /**
     * Get array of segments from a string based on a delimiter.
     *
     * @param string $delimiter Delimeter
     *
     * @return array Returns array of segments.
     */
    public function segments(string $delimiter = ' '): array
    {
        return explode($delimiter, $this->string);
    }

    /**
     * Get a segment from a string based on a delimiter.
     * Returns an empty string when the offset doesn't exist.
     * Use a negative index to start counting from the last element.
     *
     * @param int    $index     Index
     * @param string $delimiter Delimeter
     *
     * @return self Returns instance of The Strings class.
     */
    public function segment(int $index, string $delimiter = ' '): self
    {
        $segments = explode($delimiter, $this->string);

        if ($index < 0) {
            $segments = array_reverse($segments);
            $index    = abs($index) - 1;
        }

        $this->string = $segments[$index] ?? '';

        return $this;
    }

    /**
     * Get the first segment from a string based on a delimiter.
     *
     * @param string $delimiter Delimeter
     *
     * @return self Returns instance of The Strings class.
     */
    public function firstSegment(string $delimiter = ' '): self
    {
        $this->string = (string) $this->segment(0, $delimiter);

        return $this;
    }

    /**
     * Get the last segment from a string based on a delimiter.
     *
     * @param string $string    String
     * @param string $delimiter Delimeter
     *
     * @return self Returns instance of The Strings class.
     */
    public function lastSegment(string $delimiter = ' '): self
    {
        $this->string = (string) $this->segment(-1, $delimiter);

        return $this;
    }

    /**
     * Get the portion of a string between two given values.
     *
     * @param  string $from From
     * @param  string $to   To
     *
     * @return self Returns instance of The Strings class.
     */
    public function between(string $from, string $to): self
    {
        if ($from === '' || $to === '') {
            $this->string = $this->string;
        } else {
            $this->string = static::create((string) static::create($this->string, $this->encoding)->after($from), $this->encoding)->beforeLast($to)->toString();
        }

        return $this;
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string $search Search
     *
     * @return self Returns instance of The Strings class.
     */
    public function before(string $search): self
    {
        $search === '' and $this->string = $search;

        $result = strstr($this->string, (string) $search, true);

        $this->string = $result === false ? $search : $result;

        return $this;
    }

    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string $search Search
     *
     * @return self Returns instance of The Strings class.
     */
    public function beforeLast(string $search): self
    {
        $position = mb_strrpos($this->string, $search);

        if ($position === false) {
            $this->string = $this->string;
        } else {
            $this->string = (string) static::create($this->string, $this->encoding)->substr(0, $position);
        }

        return $this;
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string $search Search
     *
     * @return self Returns instance of The Strings class.
     */
    public function after(string $search): self
    {
        $this->string = $search === '' ? $this->string : array_reverse(explode($search, $this->string, 2))[0];

        return $this;
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string $search Search
     *
     * @return self Returns instance of The Strings class.
     */
    public function afterLast(string $search): self
    {
        $position = mb_strrpos($this->string, (string) $search);

        if ($position === false) {
            $this->string = $this->string;
        } else {
            $this->string = (string) $this->substr($position + static::create($search, $this->encoding)->length());
        }

        return $this;
    }

    /**
     * Pad both sides of a string with another.
     *
     * @param  int    $length If the value of pad_length is negative, less than, or equal to the length of the input string, no padding takes place, and input will be returned.
     * @param  string $pad    The pad string may be truncated if the required number of padding characters can't be evenly divided by the pad_string's length.
     *
     * @return self Returns instance of The Strings class.
     */
    public function padBoth(int $length, string $pad = ' '): self
    {
        $this->string = str_pad($this->string, $length, $pad, STR_PAD_BOTH);

        return $this;
    }

    /**
     * Pad the left side of a string with another.
     *
     * @param  int    $length If the value of pad_length is negative, less than, or equal to the length of the input string, no padding takes place, and input will be returned.
     * @param  string $pad    The pad string may be truncated if the required number of padding characters can't be evenly divided by the pad_string's length.
     *
     * @return self Returns instance of The Strings class.
     */
    public function padLeft(int $length, string $pad = ' '): self
    {
        $this->string = str_pad($this->string, $length, $pad, STR_PAD_LEFT);

        return $this;
    }

    /**
     * Pad the right side of a string with another.
     *
     * @param  int    $length If the value of pad_length is negative, less than, or equal to the length of the input string, no padding takes place, and input will be returned.
     * @param  string $pad    The pad string may be truncated if the required number of padding characters can't be evenly divided by the pad_string's length.
     *
     * @return self Returns instance of The Strings class.
     */
    public function padRight(int $length, string $pad = ' '): self
    {
        $this->string = str_pad($this->string, $length, $pad, STR_PAD_RIGHT);

        return $this;
    }

    /**
     * Strip all whitespaces from the given string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function stripSpaces(): self
    {
        $this->string = preg_replace('/\s+/u', '', $this->string);

        return $this;
    }

    /**
     * Replace all dashes characters in the string with the given value.
     *
     * @param string $replacement Value to replace dashes characters with replacement. Default is ''
     * @param bool   $strict      Should spaces be preserved or not. Default is false.
     *
     * @return self Returns instance of The Strings class.
     */
    public function replaceDashes(string $replacement = '', bool $strict = false): self
    {
        $this->string = preg_replace(
            '/\p{Pd}/u',
            $replacement,
            static::create($this->string, $this->encoding)->trim()->toString()
        );

        if ($strict) {
            $this->string = static::create($this->string, $this->encoding)
                                ->stripSpaces()
                                ->toString();
        }

        return $this;
    }

    /**
     * Replace all punctuations characters in the string with the given value.
     *
     * @param string $replacement Value to replace punctuations characters with replacement. Default is ''
     * @param bool   $strict      Should spaces be preserved or not. Default is false.
     *
     * @return self Returns instance of The Strings class.
     */
    public function replacePunctuations(string $replacement = '', bool $strict = false): self
    {
        $this->string = preg_replace(
            '/\p{P}/u',
            $replacement,
            static::create($this->string, $this->encoding)->trim()->toString()
        );

        if ($strict) {
            $this->string = static::create($this->string, $this->encoding)
                                ->stripSpaces()
                                ->toString();
        }

        return $this;
    }

    /**
     * Replace none alphanumeric characters in the string with the given value.
     *
     * @param string $replacement Value to replace none alphanumeric characters with. Default is ''
     * @param bool   $strict      Should spaces be preserved or not. Default is false.
     *
     * @return self Returns instance of The Strings class.
     */
    public function replaceNonAlphanumeric(string $replacement = '', bool $strict = false): self
    {
        $this->string = preg_replace(
            '/[^\p{L}0-9\s]+/u',
            $replacement,
            static::create($this->string, $this->encoding)->trim()->toString()
        );

        if ($strict) {
            $this->string = static::create($this->string, $this->encoding)
                                ->stripSpaces()
                                ->toString();
        }

        return $this;
    }

    /**
     * Replace none alpha characters in the string with the given value.
     *
     * @param string $replacement Value to replace none alpha characters with
     * @param bool   $strict      Should spaces be preserved or not. Default is false.
     *
     * @return self Returns instance of The Strings class.
     */
    public function replaceNonAlpha(string $replacement = '', bool $strict = false): self
    {
        $this->string = preg_replace(
            '/[^\p{L}\s]+/u',
            $replacement,
            static::create($this->string, $this->encoding)
                ->trim()
                ->toString()
        );

        if ($strict) {
            $this->string = static::create($this->string, $this->encoding)
                                ->stripSpaces()
                                ->toString();
        }

        return $this;
    }

    /**
     * Replace the given value within a portion of a string.
     *
     * @param  string|array   $replace The replacement string.
     * @param  array|int      $offset  Offset.
     * @param  array|int|null $length  Length.
     * 
     * @return self Returns instance of The Strings class.
     */
    public function replaceSubstr($replace, $offset = 0, $length = null): self
    {
        if ($length === null) {
            $length = mb_strlen($this->string);
        }

        $this->string = mb_substr($this->string, 0, $offset, $this->encoding) . 
                        $replace . 
                        mb_substr($this->string, $offset + $length, mb_strlen($this->string, $this->encoding), $this->encoding);
      
        return $this;
    }

    /**
     * Replace the given value in the given string.
     *
     * @param  string $search  Search
     * @param  mixed  $replace Replace
     *
     * @return self Returns instance of The Strings class.
     */
    public function replace(string $search, $replace): self
    {
        $this->string = str_replace($search, $replace, $this->string);

        return $this;
    }

    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string $search  Search
     * @param  array  $replace Replace
     *
     * @return self Returns instance of The Strings class.
     */
    public function replaceArray(string $search, array $replace): self
    {
        $segments = explode($search, $this->string);

        $result = array_shift($segments);

        foreach ($segments as $segment) {
            $result .= (array_shift($replace) ?? $search) . $segment;
        }

        $this->string = $result;

        return $this;
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param  string $search  Search
     * @param  string $replace Replace
     *
     * @return self Returns instance of The Strings class.
     */
    public function replaceFirst(string $search, string $replace): self
    {
        $position = strpos($this->string, $search);

        if ($position !== false) {
            $this->string = substr_replace($this->string, $replace, $position, static::create($search, $this->encoding)->length());
        } else {
            $this->string = $search;
        }

        return $this;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param  string $search  Search
     * @param  string $replace Replace
     *
     * @return self Returns instance of The Strings class.
     */
    public function replaceLast(string $search, string $replace): self
    {
        $position = strrpos($this->string, $search);

        if ($position !== false) {
            $this->string = substr_replace($this->string, $replace, $position, static::create($search, $this->encoding)->length());
        } else {
            $this->string = $search;
        }

        return $this;
    }

    /**
     * Begin a string with a single instance of a given value.
     *
     * @param  string $prefix Prefix
     *
     * @return self Returns instance of The Strings class.
     */
    public function start(string $prefix): self
    {
        $quoted = preg_quote($prefix, '/');

        $this->string = $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $this->string);

        return $this;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string|string[] $needles Needles
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function startsWith($needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($this->string, (string) $needle, static::create($needle, $this->encoding)->length()) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string|string[] $needles needles
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function endsWith($needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && (string) static::create($this->string, $this->encoding)->substr(-static::create($needle, $this->encoding)->length()) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param  string $cap Cap
     *
     * @return self Returns instance of The Strings class.
     */
    public function finish(string $cap): self
    {
        $quoted = preg_quote($cap, '/');

        $this->string = preg_replace('/(?:' . $quoted . ')+$/u', '', $this->string) . $cap;

        return $this;
    }

    /**
     * Prepend the given values to the string.
     *
     * @param  string[] $values Values
     *
     * @return self Returns instance of The Strings class.
     */
    public function prepend(string ...$values): self
    {
        $this->string = implode('', $values) . $this->string;

        return $this;
    }

    /**
     * Append the given values to the string.
     *
     * @param  string[] $values Values
     *
     * @return self Returns instance of The Strings class.
     */
    public function append(string ...$values): self
    {
        $this->string .= implode('', (array) $values);

        return $this;
    }

    /**
     * Generate a hash string from the input string.
     *
     * @param  string $algorithm  Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..).
     *                            For a list of supported algorithms see hash_algos(). Default is md5.
     * @param  string $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits. Default is FALSE
     *
     * @return self Returns instance of The Strings class.
     */
    public function hash(string $algorithm = 'md5', bool $raw_output = false): self
    {
        if (in_array($algorithm, hash_algos())) {
            $this->string = hash($algorithm, $this->string, $raw_output);
        } else {
            $this->string = $this->string;
        }

        return $this;
    }

    /**
     * Generate the crc32 polynomial from the input string.
     *
     * @return int Returns crc32 polynomial from the input string.
     */
    public function crc32(): int
    {
        return crc32($this->string);
    }

    /**
     * Generate a md5 hash string from the input string.
     *
     * @param  string $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits. Default is FALSE
     *
     * @return self Returns instance of The Strings class.
     */
    public function md5(bool $raw_output = false): self
    {
        $this->string = hash('md5', $this->string, $raw_output);

        return $this;
    }

    /**
     * Generate a sha1 hash string from the input string.
     *
     * @param  string $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits. Default is FALSE
     *
     * @return self Returns instance of The Strings class.
     */
    public function sha1(bool $raw_output = false): self
    {
        $this->string = hash('sha1', $this->string, $raw_output);

        return $this;
    }

    /**
     * Generate a sha256 hash string from the input string.
     *
     * @param  string $raw_output When set to TRUE, outputs raw binary data. FALSE outputs lowercase hexits. Default is FALSE
     *
     * @return self Returns instance of The Strings class.
     */
    public function sha256(bool $raw_output = false): self
    {
        $this->string = hash('sha256', $this->string, $raw_output);

        return $this;
    }

    /**
     * Encodes data with MIME base64.
     *
     * @return self Returns instance of The Strings class.
     */
    public function base64Encode(): self
    {
        $this->string = base64_encode($this->string);

        return $this;
    }

    /**
     * Decodes data encoded with MIME base64
     *
     * @return self Returns instance of The Strings class.
     */
    public function base64Decode(): self
    {
        $this->string = base64_decode($this->string);

        return $this;
    }

    /**
     * Randomly shuffles a string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function shuffle(): self
    {
        $indexes = range(0, static::create($this->string, $this->encoding)->length()  - 1);

        shuffle($indexes);

        $shuffled_string = '';

        foreach ($indexes as $i) {
            $shuffled_string .= static::create($this->string, $this->encoding)->substr($i, 1);
        }

        $this->string = $shuffled_string;

        return $this;
    }

    /**
     * Calculate the similarity between two strings.
     *
     * @param string $string The delimiting string.
     *
     * @return float Returns similarity percent.
     */
    public function similarity(string $string): float
    {
        similar_text($this->string, $string, $percent);

        return $percent;
    }

    /**
     * Returns the character at $index, with indexes starting at 0.
     *
     * @param int $index Position of the character.
     */
    public function at(int $index): self
    {
        $this->string = (string) $this->substr($index, 1);

        return $this;
    }

    /**
     * Sort words in string descending.
     *
     * @return self Returns instance of The Strings class.
     */
    public function wordsSortDesc(): self
    {
        $words = mb_split('\s', $this->string);

        rsort($words);

        $this->string = implode(' ', $words);

        return $this;
    }

    /**
     * Sort words in string ascending.
     *
     * @return self Returns instance of The Strings class.
     */
    public function wordsSortAsc(): self
    {
        $words = mb_split('\s', $this->string);

        sort($words);

        $this->string = implode(' ', $words);

        return $this;
    }

    /**
     * Returns an array consisting of the characters in the string.
     *
     * @return array Returns an array of string chars.
     */
    public function chars(): array
    {
        $chars = [];

        for ($i = 0, $length = $this->length(); $i < $length; $i++) {
            $chars[] = static::create($this->toString())->at($i)->toString();
        }

        return $chars;
    }

    /**
     * Get chars usage frequency array.
     *
     * @param int    $decimals     Number of decimal points. Default is 2.
     * @param string $decPoint     Separator for the decimal point. Default is ".".
     * @param string $thousandsSep Thousands separator. Default is ",".
     *
     * @return array Returns an chars usage frequency array.
     */
    public function charsFrequency(int $decimals = 2, string $decPoint = '.', string $thousandsSep = ','): array
    {
        $this->stripSpaces();
        $chars              = preg_split('//u', $this->string, -1, PREG_SPLIT_NO_EMPTY);
        $totalAllCharsArray = count($chars);
        $charsCount         = array_count_values($chars);

        arsort($charsCount);

        $percentageCount = [];

        foreach ($charsCount as $chars => $char) {
            $percentageCount[$chars] = number_format($char / $totalAllCharsArray * 100, $decimals, $decPoint, $thousandsSep);
        }

        return $percentageCount;
    }

    /**
     * Get words usage frequency array.
     *
     * @param int    $decimals     Number of decimal points. Default is 2.
     * @param string $decPoint     Separator for the decimal point. Default is ".".
     * @param string $thousandsSep Thousands separator. Default is ",".
     *
     * @return array Returns an words usage frequency array.
     */
    public function wordsFrequency(int $decimals = 2, string $decPoint = '.', string $thousandsSep = ','): array
    {
        $this->replacePunctuations();
        $words              = mb_split('\s', $this->string);
        $totalAllWordsArray = count($words);
        $wordsCount         = array_count_values($words);

        arsort($wordsCount);

        $percentageCount = [];

        foreach ($wordsCount as $words => $word) {
            $percentageCount[$words] = number_format($word / $totalAllWordsArray * 100, $decimals, $decPoint, $thousandsSep);
        }

        return $percentageCount;
    }

    /**
     * Move substring of desired $length to $destination index of the original string.
     * In case $destination is less than $length returns the string untouched.
     *
     * @param int $start       Start
     * @param int $length      Length
     * @param int $destination Destination
     *
     * @return self Returns instance of The Strings class.
     */
    public function move(int $start, int $length, int $destination): self
    {
        if ($destination <= $length) {
            return $this;
        }

        $substr       = mb_substr($this->string, $start, $length);
        $this->string = mb_substr($this->string, 0, $destination) . $substr . mb_substr($this->string, $destination);

        $pos          = mb_strpos($this->string, $substr, 0);
        $this->string = mb_substr($this->string, 0, $pos) . mb_substr($this->string, $pos + mb_strlen($substr));

        return $this;
    }

    /**
     * Inserts $substring into the string at the $index provided.
     *
     * @param string $substring Substring
     * @param int    $index     Index
     *
     * @return self Returns instance of The Strings class.
     */
    public function insert(string $substring, int $index): self
    {
        $this->string = static::create($this->string)->substr(0, $index)->toString() .
                        $substring .
                        static::create($this->string)->substr($index)->toString();

        return $this;
    }

    /**
     * Passes the strings to the given callback and return the result.
     *
     * @param Closure $callback Function with strings as parameter which returns arbitrary result.
     *
     * @return mixed Result returned by the callback.
     */
    public function pipe(Closure $callback)
    {
        return $callback($this);
    }

    /**
     * Creates a new Strings object with the same string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function copy(): self
    {
        return clone $this;
    }

    /**
     * Echo the string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function echo(): self
    {
        echo $this->toString();

        return $this;
    }

    /**
     * Return the formatted string.
     *
     * @param mixed ...$args Any number of elements to fill the string.
     *
     * @return self Returns instance of The Strings class.
     */
    public function format(...$args): self
    {
        $this->string = sprintf($this->string, ...$args);

        return $this;
    }

    /**
     * Returns true if the string is hex color, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isHexColor(): bool
    {
        return (bool) mb_ereg_match('^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$', $this->string);
    }

    /**
     * Returns true if the string is affirmative, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isAffirmative(): bool
    {
        return (bool) mb_ereg_match('^(?:1|t(?:rue)?|y(?:es)?|ok(?:ay)?)$', $this->string);
    }

    /**
     * Returns true if the string is date and it is valid, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isDate(): bool
    {
        return (bool) strtotime($this->string);
    }

    /**
     * Returns true if the string is email and it is valid, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isEmail(): bool
    {
        return (bool) filter_var($this->string, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Returns true if the string is url and it is valid, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isUrl(): bool
    {
        return (bool) filter_var($this->string, FILTER_VALIDATE_URL);
    }

    /**
     * Returns true if the string is not empty, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->string);
    }

    /**
     * Returns true if the string contains ASCII, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isAscii(): bool
    {
        return mb_ereg_match('^[[:ascii:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only alphabetic and numeric chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isAlphanumeric(): bool
    {
        return mb_ereg_match('^[[:alnum:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only alphabetic chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isAlpha(): bool
    {
        return mb_ereg_match('^[[:alpha:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only whitespace chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isBlank(): bool
    {
        return mb_ereg_match('^[[:space:]]*$', $this->string);
    }

    /**
     * Returns true if the string is a number or a numeric strings, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isNumeric(): bool
    {
        return is_numeric($this->string);
    }

    /**
     * Returns true if the string contains only digit chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isDigit(): bool
    {
        return mb_ereg_match('^[[:digit:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only lower case chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isLower(): bool
    {
        return mb_ereg_match('^[[:lower:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only upper case chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isUpper(): bool
    {
        return mb_ereg_match('^[[:upper:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only hexadecimal chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isHexadecimal(): bool
    {
        return mb_ereg_match('^[[:xdigit:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only printable (non-invisible) chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isPrintable(): bool
    {
        return mb_ereg_match('^[[:print:]]*$', $this->string);
    }

    /**
     * Returns true if the string contains only punctuation chars, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isPunctuation(): bool
    {
        return mb_ereg_match('^[[:punct:]]*$', $this->string);
    }

    /**
     * Returns true if the string is serialized, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isSerialized(): bool
    {
        if ($this->string === '') {
            return false;
        }

        return $this->string === 'b:0;' || @unserialize($this->string) !== false;
    }

    /**
     * Returns true if the string is JSON, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isJson(): bool
    {
        json_decode($this->string);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Returns true if the string is base64 encoded, false otherwise.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isBase64(): bool
    {
        if ($this->length() === 0) {
            return false;
        }

        $decoded = base64_decode($this->string, true);

        if ($decoded === false) {
            return false;
        }

        return base64_encode($decoded) === $this->string;
    }

    /**
     * Check if two strings are similar.
     *
     * @param string $string                  The string to compare against.
     * @param float  $minPercentForSimilarity The percentage of needed similarity. Default is 80%
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isSimilar(string $string, float $minPercentForSimilarity = 80.0): bool
    {
        return $this->similarity($string) >= $minPercentForSimilarity;
    }

    /**
     * Determine whether the string is equals to $string.
     *
     * @param $string String to compare.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isEqual(string $string): bool
    {
        return $string === $this->toString();
    }

    /**
     * Determine whether the string is IP and it is a valid IP address.
     *
     * @param $flags Flags:
     *                  FILTER_FLAG_IPV4
     *                  FILTER_FLAG_IPV6
     *                  FILTER_FLAG_NO_PRIV_RANGE
     *                  FILTER_FLAG_NO_RES_RANGE
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isIP(int $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6): bool
    {
        return (bool) filter_var($this->toString(), FILTER_VALIDATE_IP, $flags);
    }

    /**
     * Determine whether the string is MAC address and it is a valid MAC address.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isMAC(): bool
    {
        return (bool) filter_var($this->toString(), FILTER_VALIDATE_MAC);
    }

    /**
     * Determine whether the string is HTML.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isHTML(): bool
    {
        return $this->toString() !== strip_tags($this->toString());
    }

    /**
     * Determine whether the string is Boolean.
     *
     * Boolean representation for logical strings:
     * 'true', '1', 'on' and 'yes' will return true.
     * 'false', '0', 'off', and 'no' will return false.
     *
     * In all instances, case is ignored.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isBoolean(): bool
    {
        return in_array(mb_strtolower($this->toString()), ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'], true);
    }

    /**
     * Determine whether the string is Boolean and it is TRUE.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isTrue(): bool
    {
        return $this->toBoolean() === true;
    }

    /**
     * Determine whether the string is Boolean and it is FALSE.
     *
     * @return bool Returns TRUE on success or FALSE otherwise.
     */
    public function isFalse(): bool
    {
        return $this->toBoolean() === false;
    }

    /**
     * Return Strings object as string.
     *
     * @return string Returns strings object as string.
     */
    public function toString(): string
    {
        return strval($this);
    }

    /**
     * Return Strings object as integer.
     *
     * @return int Return Strings object as integer.
     */
    public function toInteger(): int
    {
        return intval($this->string);
    }

    /**
     * Return Strings object as float.
     *
     * @return float Return Strings object as float.
     */
    public function toFloat(): float
    {
        return floatval($this->string);
    }

    /**
     * Returns a boolean representation of the given logical string value.
     *
     * For example:
     * 'true', '1', 'on' and 'yes' will return true.
     * 'false', '0', 'off', and 'no' will return false.
     *
     * In all instances, case is ignored.
     *
     * For other numeric strings, their sign will determine the return value.
     * In addition, blank strings consisting of only whitespace will return
     * false. For all other strings, the return value is a result of a
     * boolean cast.
     *
     * @return bool Returns a boolean representation of the given logical string value.
     */
    public function toBoolean(): bool
    {
        $result = filter_var($this->string, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $result ?? true;
    }

    /**
     * Return Strings object as array based on a delimiter.
     *
     * @param string $delimiter Delimeter. Default is null.
     *
     * @return array Return Strings object as array based on a delimiter.
     */
    public function toArray(?string $delimiter = null): array
    {
        $encoding = $this->encoding;
        $string   = static::create($this->string, $encoding)->trim()->toString();

        if ($delimiter !== null) {
            $array = explode($delimiter, $string);
        } else {
            $array = [$string];
        }

        array_walk(
            $array,
            static function (&$value) use ($encoding): void {
                if ((string) $value !== $value) {
                    return;
                }

                $value = static::create($value, $encoding)->trim()->toString();
            }
        );

        return $array;
    }

    /**
     * Returns a new ArrayIterator, thus implementing the IteratorAggregate
     * interface. The ArrayIterator's constructor is passed an array of chars
     * in the multibyte string. This enables the use of foreach with instances
     * of Strings\Strings.
     *
     * @return ArrayIterator An iterator for the characters in the string
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->chars());
    }

    /**
     * Returns whether or not a character exists at an index. Offsets may be
     * negative to count from the last character in the string. Implements
     * part of the ArrayAccess interface.
     *
     * @param  mixed $offset The index to check
     *
     * @return bool Return TRUE key exists in the array, FALSE otherwise.
     */
    public function offsetExists($offset): bool
    {
        $length = $this->length();
        $offset = (int) $offset;

        if ($offset >= 0) {
            return $length > $offset;
        }

        return $length >= abs($offset);
    }

    /**
     * Returns the character at the given index. Offsets may be negative to
     * count from the last character in the string. Implements part of the
     * ArrayAccess interface, and throws an OutOfBoundsException if the index
     * does not exist.
     *
     * @param  mixed $offset The index from which to retrieve the char
     *
     * @return mixed                 The character at the specified index
     * @return bool Return TRUE key exists in the array, FALSE otherwise.
     *
     * @throws OutOfBoundsException  If the positive or negative offset does
     *                               not exist
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        $length = $this->length();

        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException('No character exists at the index');
        }

        return mb_substr($this->toString(), $offset, 1, $this->encoding);
    }

    /**
     * Implements part of the ArrayAccess interface, but throws an exception
     * when called. This maintains the immutability of Strings objects.
     *
     * @param  mixed $offset The index of the character
     * @param  mixed $value  Value to set
     *
     * @throws Exception When called
     */
    public function offsetSet($offset, $value): void
    {
        throw new Exception('Strings object is immutable, cannot modify char');
    }

    /**
     * Implements part of the ArrayAccess interface, but throws an exception
     * when called. This maintains the immutability of Strings objects.
     *
     * @param  mixed $offset The index of the character
     *
     * @throws Exception When called
     */
    public function offsetUnset($offset): void
    {
        throw new Exception('Strings object is immutable, cannot unset char');
    }
}
