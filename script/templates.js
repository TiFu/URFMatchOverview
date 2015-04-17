/**
 * The following variables are used:
 * {killer}/{victim} - in generell a picture of a prototype!
 * {monster} - Baron Nashor or Dragon
 */
$templates = new Array();
$templates["highlights"] = new Array();
$templates["highlights"]["ELITE_MONSTER_KILL"] = "<span class=\"moreinfp\"> has been slain by </span>";
$templates["highlights"]["CHAMPION_KILL"] = "<span class=\"moreinfp\"> has killed </span>";
$templates["highlights"]["BUILDING_KILL"] = "<span class=\"moreinfp\"> has been destroyed by </span>";
$templates["map"] = new Array();
$templates["map"]["KILL"] = '{killer}<img src="images/kill_icon.png" class="killIcon" alt="killed">{victim}';
