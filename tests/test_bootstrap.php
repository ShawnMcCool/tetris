<?php namespace Tests;

use Throwable;

require 'vendor/autoload.php';

# The most incredibly naive and hardcoded testing framework ever made.

class InvalidAssertion extends \Exception {}

function it(string $description, callable $test)
{
    $exceptions = [];

    try {
        $test();
    } catch (Throwable $exception) {
        $exceptions[] = $exception;
    }

    if (empty($exceptions)) {
        $filename = basename(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0]['file']);
        echo "{$filename} :: it {$description}\n";
        return;
    }

    if ($exceptions) {
        echo "\n";

        foreach ($exceptions as $exception) {

            // naive method to find the most relevant trace frame
            $traceFrames = array_filter(
                $exception->getTrace(),
                fn($frame) => $frame['file'] != __FILE__ && basename($frame['file']) !== 'run.php'
            );
            $frame = reset($traceFrames);

            // which file, which test
            $testFile = basename(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0]['file']);
            echo "{$testFile} ;; it {$description}\n";

            // display the exception
            $frameFile = basename($frame['file']);
            echo str_pad('', strlen($testFile) + 4, ' ', STR_PAD_LEFT) . $frameFile . ':' . $frame['line'] . ' - ' . get_class($exception) . ' - ' . ($exception->getMessage() ? '"' . $exception->getMessage() . '"' : '') . "\n";
        }

        echo "\n";
    }
}

function expectException(string $exceptionClass, callable $test)
{
    try {
        $test();
    } catch (\Exception $exception) {
        // the victory condition
        if (get_class($exception) === $exceptionClass) {
            return;
        }

        throw new InvalidAssertion("Expected exception '{$exceptionClass}' but received '" . get_class($exception) . "'.");
    }

    throw new InvalidAssertion("Expected exception '{$exceptionClass}' but received no exception.");
}

function expectEqual(mixed $expected, mixed $actual)
{
    if ($expected === $actual) {
        return;
    }

    throw new InvalidAssertion("Expected " . var_export($expected, true) . " but got " . var_export($actual, true) . ".");
}

function expectTrue(bool $expected) {

    if ($expected === true) {
        return;
    }

    throw new InvalidAssertion("Expected value to be 'true' but got 'false'.");
}

function expectFalse(bool $expected) {

    if ($expected === false) {
        return;
    }

    throw new InvalidAssertion("Expected value to be 'false' but got 'true'.");
}

function expectFloat(float $expected, float $actual) {
    
    if (abs($expected - $actual) < PHP_FLOAT_EPSILON) {
        return;
    }
    
    throw new InvalidAssertion("Expected float '{$actual}' was not within epsilon of float '{$expected}'.");
}

function expectOutputStartsWith(string $expected, callable $expression) {
    
    ob_start();
    $expression();
    $actual = ob_get_clean();
    
    if (str_starts_with($actual, $expected)) {
        return;
    }
    
    throw new InvalidAssertion("Expected string to start with '$expected' but received '$actual'.");
}