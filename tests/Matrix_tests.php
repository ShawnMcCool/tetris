<?php

use Tetris\Matrix;
use Tetris\Vector;
use Tetris\Position;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectFalse;

//it('can return a subsection of the matrix', function () {
//    $matrix = Playfield::standard();
//    
//    $matrix->lockTetrimino(
//        new ActiveTetrimino(
//            Tetrimino::I(),
//            Vector::fromInt(0, 0)
//        )
//    );
//    
//    $subsection = $matrix->minoMatrixFromSubsection(
//        Vector::fromInt(0, 0),
//        Vector::fromInt(5, 5)
//    );
//    
//    var_dump($subsection);
//});

it('can return a list of all mino positions', function () {

    $matrix = Matrix::fromArray(
        [
            [0, 0, 0, 0],
            [1, 1, 1, 1],
            [0, 0, 0, 0],
            [0, 0, 0, 0],
        ]
    );

    /**
     * @var array<Vector> $positions
     */
    $positions = $matrix->minoPositions();

    expectTrue(
        $positions[0]->equals(Vector::fromInt(0, 1)),
    );

    expectTrue(
        $positions[1]->equals(Vector::fromInt(1, 1)),
    );

    expectTrue(
        $positions[2]->equals(Vector::fromInt(2, 1)),
    );

    expectTrue(
        $positions[3]->equals(Vector::fromInt(3, 1)),
    );
});

it('can detect mino collisions', function () {
    $largeMatrix = Matrix::fromArray(
        [
            [0, 0, 0,],
            [0, 1, 0,],
            [0, 0, 0,],
        ]
    );

    $smallMatrix = Matrix::fromArray(
        [
            [1],
        ]
    );

    expectTrue(
        $largeMatrix->collidesWith(
            $smallMatrix,
            Vector::fromInt(1, 1)
        )
    );
        
    expectFalse(
        $largeMatrix->collidesWith(
            $smallMatrix,
            Vector::fromInt(0, 0)
        )
    );

    expectFalse(
        $largeMatrix->collidesWith(
            $smallMatrix,
            Vector::fromInt(2, 0)
        )
    );
    expectFalse(
        $largeMatrix->collidesWith(
            $smallMatrix,
            Vector::fromInt(0, 2)
        )
    );
});