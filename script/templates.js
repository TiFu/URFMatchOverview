/**
 * The following variables are used:
 * {killer}/{victim} - in generell a picture of a prototype!
 * {monster} - Baron Nashor or Dragon
 */
$templates = new Array();
$templates["highlights"] = new Array();
$templates["highlights"]["ELITE_MONSTER_KILL"] = "<span> has been slain by </span>";
$templates["highlights"]["CHAMPION_KILL"] = "<span> killed </span>";
$templates["highlights"]["BUILDING_KILL"] = "<span> was destroyed by </span>";
$templates["map"] = new Array();
$templates["map"]["KILL"] = '{killer}<img src="images/kill_icon.png" class="killIcon" alt="killed">{victim}';
