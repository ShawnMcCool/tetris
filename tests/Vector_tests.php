<?php

use Tetris\Vector;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectEqual;
use function Tests\expectFalse;

it('can be constructed from x,y integers', function () {
    $vector = Vector::fromInt(1, 2);

    expectEqual(1, $vector->x());
    expectEqual(2, $vector->y());
});

it('can compare two vectors for value equality', function () {
    expectTrue(
        Vector::fromInt(1, 2)->equals(
            Vector::fromInt(1, 2)
        )
    );
    expectFalse(
        Vector::fromInt(2, 2)->equals(
            Vector::fromInt(3, 3)
        )
    );
});

it('can sum two vectors', function () {
    expectTrue(
        Vector::fromInt(
            1, 2
        )->translate(
            Vector::fromInt(2, 1)
        )->equals(
            Vector::fromInt(3, 3)
        )
    );
});

it('can construct a zero vector', function() {
    expectTrue(
        Vector::zero()->equals(
            Vector::fromInt(0, 0)
        )
    );
});