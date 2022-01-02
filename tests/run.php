<?php

require 'test_bootstrap.php';

$testFiles = glob('tests/*_tests.php');

foreach ($testFiles as $testFile) {
    require($testFile);
}