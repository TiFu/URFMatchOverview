# Riot Games API Challenge 2015
## Install
1. Open: config/config.php
2. Enter your mysql configuration
  - SERVER
  - DATABASE
  - DB\_USER
  - DB\_PW 
3. Start your server and go to install/install.php

## How to add matches
1. Go to ids/ and add a text file with one id per row.
2. Go to matches/ and add for each added match id one file named %matchId%.json

## Features
 - Match overview containing
   - a timeline
   -  champions overview with items, minions, gold, kda, ...
   -  Team stats
   -  Textbox and map showing events
   -  and all that synchronized with the timeline!
 - View general statistics of the match, the server it was played on and all servers