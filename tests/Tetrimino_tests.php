<?php

use Tetris\Shape;
use Tetris\Vector;
use Tetris\Tetrimino;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectFalse;

it('can return minos translated to matrix space', function () {

    $tetrimino = Tetrimino::withShape(Shape::t());

    $minos = $tetrimino->minos();

    expectTrue(
        $minos->equals($minos)
    );

    // matrix space = tetrimino position + mino positions
    // if tetrimino is at 1,1 and one of the four minos is at 3,3
    // then the mino's matrix space is 4,4

    $movedTetrimino = $tetrimino->translate(Vector::fromInt(1, 1));
    $movedMinos = $movedTetrimino->minosInMatrixSpace();

    expectFalse(
        $minos->equals($movedMinos)
    );
});