<?php

use Tetris\Direction;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectEqual;

it('can represent left', function () {
    expectTrue(
        Direction::left()->isLeft()
    );
});

it('can represent right', function () {
    expectTrue(
        Direction::right()->isRight()
    );
});

it('can return a string description of the direction', function () {
    expectEqual(
        'left',
        Direction::left()->toString()
    );

    expectEqual(
        'right',
        Direction::right()->toString()
    );
});