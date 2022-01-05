# Tetris

The main branch is the most up-to-date. There are branches marking the endpoints of each stream.

- [End of Stream 1](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-1)
- [End of Stream 2](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-2)

## How to Run

This project requires php 8.0 and the readline extension (should come with your PHP). You need to run the game from a terminal that supports ANSI character codes.

To play Tetris:

```shell
$ php ./play.php
```

To run the tests:

```shell
$ php ./tests/run.php
```

## Controls

QWERTY controls:  
A / D = move left / right  
; / ' = rotate left / right

DVORAK controls:  
A / O = move left / right  
s / - = rotate left / right

## Requirements

### Must Haves

- 7 tetriminos: O, I, T, L, J, S, and Z, each made up of 4 minos
- the matrix: the play-field may be of any size, however standard is 10 minos wide and 40 minos tall
- line clear: when a line is completely cleared, remove it from the board and drop 
  lines until all empty lines are above the top-most mino in the matrix
- lock down: the moment a tetrimino locks into place and can no longer be moved
- tetris notification: reward the player with a notification when they clear a tetris (4 lines at once)
- keyboard controls: left / right shifts the tetrimino
- basic display: text-based terminal display

### Nice to Haves

- lock down delay: extra time given to the player to rotate the piece before it's permanently snapped into place
- hard drop: drop a tetrimino from the current position directly into lock down by tapping 'up'
- soft drop: increase the speed at which a tetrimino falls by holding 'down'
- scoring
- different randomization algorithms for drawing pieces
- wall kick

## References

- https://tetris.com/article/35/tetris-lingo-every-player-should-know