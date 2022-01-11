<?php

use Tetris\Mino;
use Tetris\Vector;
use Tetris\ShapeName;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectEqual;
use function Tests\expectFalse;

it('has a known position and shape name', function () {

    $mino = Mino::at(
        Vector::one(),
        ShapeName::i()
    );

    expectTrue(
        $mino->position()->equals(Vector::one())
    );

    expectEqual(
        'i',
        $mino->shapeName()->toString()
    );

});

it('knows if it shares a position with another mino', function () {

    expectTrue(
        Mino::at(
            Vector::one(), ShapeName::i()
        )->sharesAPositionWith(
            Mino::at(Vector::one(), ShapeName::t())
        )
    );

    expectFalse(
        Mino::at(
            Vector::one(), ShapeName::i()
        )->sharesAPositionWith(
            Mino::at(Vector::zero(), ShapeName::t())
        )
    );
});

it('can can translate its position by a vector', function () {

    $mino = Mino::at(
        Vector::one(), ShapeName::i()
    );

    expectTrue(
        $mino
            ->translate(Vector::one())
            ->position()
            ->equals(
                Vector::fromInt(2, 2)
            )
    );

});