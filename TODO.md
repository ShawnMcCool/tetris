 - add a line clear
  - does any row have x minos (x = full width)
  - remove all minos in that row
  - one by one drop rows

- make player input more event driven, so that we can add more controls
    - run an input check on every frame
    - if we get input, we raise an event that we received input
    - listen to that event, translate key to game behavior

- add soft drop to speed up future gameplay testing
    - update the gameplay timer
        - $game->updateGameplaySpeed()