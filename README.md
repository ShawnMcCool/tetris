# Tetris

This is an experimental project in which I build Tetris on a stream and talk through the process. The goal is to build a system that not only works well, but is very easy to read and extend. So, over time we don't only add features, but iteratively refactor toward a beautiful machine. 

The main branch is the most up-to-date. There are branches marking the endpoints of each stream.

- [End of Stream 1](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-1)
- [End of Stream 2](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-2)
- [End of Stream 3](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-3)
- [End of Stream 4](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-4)
- [End of Stream 5](https://github.com/ShawnMcCool/tetris/tree/end-of-stream-5)

## How to Run

This project requires php 8.0 and the readline extension (should come with your PHP). You need to run the game from a terminal that supports ANSI character codes.

Clone this branch locally with `git clone git@github.com:ShawnMcCool/tetris.git`.

All dependencies are inside the repo, so no need to install packages.

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

## Development Notes

Everything in the project is written by Shawn McCool except the Thermage/Thermage package which is used to render the nice blocks in the terminal. There's another render option, it uses ascii characters like O and 0, and while it's fun, it's not as fancy.

### Completed

- 7 tetriminos: O, I, T, L, J, S, and Z, each made up of 4 minos
- the matrix: the play-field may be of any size, however standard is 10 minos wide and 40 minos tall (with 20 visible to the player). 
- line clear: when a line is completely cleared, remove it from the board and drop 
  lines until all empty lines are above the top-most mino in the matrix
- lock down: the moment a tetrimino locks into place and can no longer be moved
- keyboard controls
- basic display: text-based terminal display
- block display: nice graphics built with Thermage/Thermage
- 7-bag tetrimino spawn randomizer

### Todo

- tetris notification: reward the player with a notification when they clear a tetris (4 lines at once)
- hard drop: drop a tetrimino from the current position directly into lock down by tapping 'up'
- scoring
- soft drop: increase the speed at which a tetrimino falls by holding 'down'
- lock down delay: extra time given to the player to rotate the piece before it's permanently snapped into place
- wall kick