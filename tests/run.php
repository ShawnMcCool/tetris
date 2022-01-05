<?php

require 'test_bootstrap.php';

$searchDepth = '/,/*/,/*/*/,/*/*/*/,/*/*/*/*/,/*/*/*/*/*/,/*/*/*/*/*/*/,/*/*/*/*/*/*/*/,/*/*/*/*/*/*/*/*/,/*/*/*/*/*/*/*/*/*/';
$testFiles = glob('tests{'.$searchDepth.'}*_tests.php', GLOB_BRACE);

foreach ($testFiles as $testFile) {
    require($testFile);
}