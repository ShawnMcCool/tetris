<?php

function dd(...$vars) {
    array_walk($vars, fn($var) => var_dump($var));
    die();
}