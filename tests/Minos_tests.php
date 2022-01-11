<?php

use Tetris\Mino;
use Tetris\Minos;
use Tetris\Vector;
use Tetris\ShapeName;
use function Tests\it;
use function Tests\expectTrue;
use function Tests\expectFalse;
use function Tests\expectEqual;

it('can create an empty set of minos', function () {

    expectTrue(
        Minos::empty() instanceof Minos
    );

    expectEqual(
        0,
        Minos::empty()->count()
    );

});

it('can be constructed from a list', function () {

    $minos = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
    );

    expectTrue(
        $minos->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(3, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
                Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
            )
        )
    );

});

it('can be combined with other mino sets', function () {

    $minos = Minos::empty();

    $sum = $minos->add(
        Minos::fromList(
            Mino::at(Vector::one(), ShapeName::i())
        )
    );

    expectEqual(1, $sum->count());
    expectTrue(
        $sum->toArray()[0]->position()->equals(Vector::one())
    );

});

it('can return an array representation of itself', function () {

    expectEqual(
        5,
        count(
            Minos::fromList(
                Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(3, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
                Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
            )->toArray()
        )
    );

});

it('can compare for equality', function () {

    // it identifies that the minos are same
    expectTrue(
        Minos::fromList(
            Mino::at(Vector::fromInt(0, 0), ShapeName::i())
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(0, 0), ShapeName::i())
            )
        )
    );

    // it identifies that the minos are different
    expectFalse(
        Minos::fromList(
            Mino::at(Vector::fromInt(0, 0), ShapeName::i())
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(1, 1), ShapeName::i())
            )
        )
    );

    // it compares equality without considering sequence
    expectTrue(
        Minos::fromList(
            Mino::at(Vector::fromInt(1, 0), ShapeName::i()),
            Mino::at(Vector::fromInt(0, 1), ShapeName::i())
        )->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(0, 1), ShapeName::i()),
                Mino::at(Vector::fromInt(1, 0), ShapeName::i())
            )
        )
    );
});

it('can filter the set of minos using a predicate', function () {

    $filteredMinos = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
    )->filter(
        fn(Mino $mino) => $mino->position()->y() == 2
    );

    expectTrue(
        $filteredMinos->count() == 2
    );

    expectTrue(
        $filteredMinos->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
                Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
            )
        )
    );

});

it('can map the set of minos using a transformation function', function () {

    $filteredMinos = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
    )->map(
        fn(Mino $mino) => $mino->translate(Vector::one())
    );

    expectTrue(
        $filteredMinos->equals(
            Minos::fromList(
                Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
                Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
                Mino::at(Vector::fromInt(4, 2), ShapeName::i()),
                Mino::at(Vector::fromInt(3, 3), ShapeName::i()),
                Mino::at(Vector::fromInt(4, 3), ShapeName::i()),
            )
        )
    );

});

it('it can translate all mino positions by a vector', function () {

    $translated = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 1), ShapeName::i())
    )->translate(
        Vector::one()
    )->toArray();

    expectTrue(
        $translated[0]
            ->position()
            ->equals(Vector::fromInt(2, 2))
    );

    expectTrue(
        $translated[1]
            ->position()
            ->equals(Vector::fromInt(3, 2))
    );

});

it('can return a count of contained minos', function () {

    expectEqual(
        2,
        Minos::fromList(
            Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
            Mino::at(Vector::fromInt(2, 1), ShapeName::i())
        )->count()
    );

});

it('knows if it contains a specified mino', function () {

    $minos = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 1), ShapeName::i())
    );

    expectTrue(
        $minos
            ->hasMinoSharingAPositionWith(
                Mino::at(Vector::one(), ShapeName::i())
            )
    );

    expectFalse(
        $minos
            ->hasMinoSharingAPositionWith(
                Mino::at(Vector::zero(), ShapeName::i())
            )
    );

});

it('can create an equivalent clone of the mino set', function () {

    $minos = Minos::fromList(
        Mino::at(Vector::fromInt(1, 1), ShapeName::i()),
        Mino::at(Vector::fromInt(2, 1), ShapeName::i())
    );

    $sameMinos = $minos;

    expectTrue(
        $minos === $sameMinos
    );

    expectFalse(
        $minos === $minos->clone()
    );

    expectTrue(
        $minos->equals($minos->clone())
    );

});

it('can count the number of minos in a row', function () {

    Minos::fromList(
        Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(4, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 3), ShapeName::i()),
        Mino::at(Vector::fromInt(4, 3), ShapeName::i()),
    );

});

it('can find the nearest row above containing minos', function () {

    expectEqual(
        1,
        Minos::fromList(
            Mino::at(Vector::fromInt(2, 1), ShapeName::i()),
            Mino::at(Vector::fromInt(3, 3), ShapeName::i()),
            Mino::at(Vector::fromInt(4, 3), ShapeName::i()),
        )->nearestRowAboveContainingMinos(3)
    );

    expectEqual(
        null,
        Minos::empty()
             ->nearestRowAboveContainingMinos(3)
    );

});

it('can count the number of minos in a row', function () {

    $minos = Minos::fromList(
        Mino::at(Vector::fromInt(2, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(4, 2), ShapeName::i()),
        Mino::at(Vector::fromInt(3, 3), ShapeName::i()),
        Mino::at(Vector::fromInt(4, 3), ShapeName::i()),
    );

    expectEqual(
        3, $minos->countOfMinosInRow(2)
    );

});