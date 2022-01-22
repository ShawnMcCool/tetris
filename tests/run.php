<?php

require 'test_bootstrap.php';

/*
 * get all test files in /tests/
 */
$searchDepth = '/,/*/,/*/*/,/*/*/*/,/*/*/*/*/,/*/*/*/*/*/,/*/*/*/*/*/*/,/*/*/*/*/*/*/*/,/*/*/*/*/*/*/*/*/,/*/*/*/*/*/*/*/*/*/';
$testFiles = glob('tests{' . $searchDepth . '}*_tests.php', GLOB_BRACE);

/*
 * if command-line arguments are present, filter the files
 */
$searchQuery = implode(' ', array_slice($argv, 1, count($argv) - 1));

$filesToRun = array_filter(
    $testFiles,
    fn($testFile) => str_contains($testFile, $searchQuery)
);

/*
 * run the tests
 */
foreach ($filesToRun as $testFile) {
    require($testFile);
}