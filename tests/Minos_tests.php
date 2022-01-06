<?php

use Tetris\Mino;
use Tetris\Minos;
use Tetris\Vector;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectFalse;

it('can compare for equality', function () {

    expectTrue(
        Minos::fromList(
            Mino::at(Vector::fromInt(0, 0))
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(0, 0))
            )
        )
    );
    
    expectFalse(
        Minos::fromList(
            Mino::at(Vector::fromInt(0, 0))
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(1, 1))
            )
        )
    );
    
});