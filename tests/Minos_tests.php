<?php

use Tetris\Mino;
use Tetris\Minos;
use Tetris\Vector;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectFalse;

it('can compare for equality', function () {

    // it identifies that the minos are same
    expectTrue(
        Minos::fromList(
            Mino::at(Vector::fromInt(0, 0))
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(0, 0))
            )
        )
    );

    // it identifies that the minos are different
    expectFalse(
        Minos::fromList(
            Mino::at(Vector::fromInt(0, 0))
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(1, 1))
            )
        )
    );

    // it compares equality without considering sequence
    expectTrue(
        Minos::fromList(
            Mino::at(Vector::fromInt(1, 0)),
            Mino::at(Vector::fromInt(0, 1))
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(0, 1)),
                Mino::at(Vector::fromInt(1, 0))
            )
        )
    );
});

it('can filter the set of minos using a predicate', function() {

    $filteredMinos = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1)),
        Mino::at(Vector::fromInt(2, 1)),
        Mino::at(Vector::fromInt(3, 1)),
        Mino::at(Vector::fromInt(2, 2)),
        Mino::at(Vector::fromInt(3, 2)),
    )->filter(
        fn(Mino $mino) => $mino->position()->y() == 2
    );

    expectTrue(
        $filteredMinos->count() == 2
    );

    expectTrue(
        $filteredMinos->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(3, 2)),
                Mino::at(Vector::fromInt(2, 2)),
            )
        )
    );

    // ---
});