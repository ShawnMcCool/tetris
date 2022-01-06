<?php

use Tetris\Vector;
use Tetris\Tetrimino;
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
//
//it('can return a list of all mino positions', function () {
//
//    $matrix = Matrix::fromArray(
//        [
//            [0, 0, 0, 0],
//            [1, 1, 1, 1],
//            [0, 0, 0, 0],
//            [0, 0, 0, 0],
//        ]
//    );
//
//    /**
//     * @var array<Vector> $positions
//     */
//    $positions = $matrix->minoPositions();
//
//    expectTrue(
//        $positions[0]->equals(Vector::fromInt(0, 1)),
//    );
//
//    expectTrue(
//        $positions[1]->equals(Vector::fromInt(1, 1)),
//    );
//
//    expectTrue(
//        $positions[2]->equals(Vector::fromInt(2, 1)),
//    );
//
//    expectTrue(
//        $positions[3]->equals(Vector::fromInt(3, 1)),
//    );
//});
//
//it('can detect mino collisions', function () {
//    $largeMatrix = Matrix::fromArray(
//        [
//            [0, 0, 0,],
//            [0, 1, 0,],
//            [0, 0, 0,],
//        ]
//    );
//
//    $smallMatrix = Matrix::fromArray(
//        [
//            [1],
//        ]
//    );
//
//    expectTrue(
//        $largeMatrix->collidesWith(
//            $smallMatrix,
//            Vector::fromInt(1, 1)
//        )
//    );
//        
//    expectFalse(
//        $largeMatrix->collidesWith(
//            $smallMatrix,
//            Vector::fromInt(0, 0)
//        )
//    );
//
//    expectFalse(
//        $largeMatrix->collidesWith(
//            $smallMatrix,
//            Vector::fromInt(2, 0)
//        )
//    );
//    expectFalse(
//        $largeMatrix->collidesWith(
//            $smallMatrix,
//            Vector::fromInt(0, 2)
//        )
//    );
//});

//it('can lock a tetrimino', function () {
//    $matrix = \Tetris\Matrix::withDimensions(10, 10, Vector::zero());
//
//    $tetrimino = Tetrimino::t(Vector::zero());
//    $matrix = $matrix->lock($tetrimino);
//
//    foreach ($tetrimino as $mino) {
//        expectTrue(
//            $matrix->minos()->hasMino($mino)
//        );
//    }
//
//    //--------------
//    $matrix = \Tetris\Matrix::withDimensions(10, 10, Vector::zero());
//
//    $tetrimino = Tetrimino::t(Vector::zero());
//
//    foreach ($tetrimino as $mino) {
//        expectFalse(
//            $matrix->minos()->hasMino($mino)
//        );
//    }
//});

it('can determine if a mino collides with a wall', function () {
    $matrix = \Tetris\Matrix::withDimensions(10, 10, Vector::zero());

    expectTrue(
        $matrix->canFit(
            Tetrimino::t(Vector::fromInt(0, 0))
        )
    );
    
    expectFalse(
        $matrix->canFit(
            Tetrimino::t(Vector::fromInt(-1, 0))
        )
    );
    
    expectTrue(
        $matrix->canFit(
            Tetrimino::t(Vector::fromInt(7, 0))
        )
    );
    
    expectFalse(
        $matrix->canFit(
            Tetrimino::t(Vector::fromInt(8, 0))
        )
    );
});

//it('can determine if a mino collides with other minos', function () {
//
//    $matrix = \Tetris\Matrix::withDimensions(10, 10, Vector::zero());
//    $matrix = $matrix->lock(
//        Tetrimino::t(Vector::fromInt(0, 0))
//    );
//
//    expectFalse(
//        $matrix->canFit(
//            Tetrimino::t(Vector::fromInt(0, 0))
//        )
//    );
//
//    /*
//     * does it fit below?
//     */
//    expectTrue(
//        $matrix->canFit(
//            Tetrimino::t(Vector::fromInt(0, 2))
//        )
//    );
//
//    /*
//     * does it fit to the right?
//     */
//    expectTrue(
//        $matrix->canFit(
//            Tetrimino::t(Vector::fromInt(3, 0))
//        )
//    );
//});