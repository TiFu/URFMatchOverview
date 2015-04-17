# Riot Games API Challenge 2015
## Install
1. Open: config/config.php
2. Enter your mysql configuration
  - SERVER
  - DATABASE
  - DB\_USER
  - DB\_PW 
3. Start your server and go to install/install.php

## How To: add your own matches
1. Download your match file from the Riot Games API and place it in data/matches/{yourMatchIdHere}.json
2. By opening result.php?matchId={yourMatchIdHere} you can view your match

## How To: Add more games to the statistics
1. Paste all games you want to add into one folder (path may not contain spaces)
2. Call ratescripts/analyzeGames.php?matchesFolder={folder}
3. Call ratescripts/bans.php?file={file} for each match you want to add

## Features
 - Match overview containing
   - a timeline
   -  champions overview with items, minions, gold, kda, ...
   -  Team stats
   -  Textbox and map showing events
   -  and all that synchronized with the timeline!
 - View general statistics of the match, the server it was played on and all servers (09.04 to 13.04. data is included in the installation)
 
