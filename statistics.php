<?php
include("config/config.php");
include("functions.php");
$mysqli = new mysqli(SERVER, DB_USER, DB_PW, DATABASE);

define("CHAMPS_PER_PAGE", 10);

$numberOfChampions = $mysqli->query("SELECT * FROM " . CHAMP_TABLE)->num_rows;
$pageCount = ceil($numberOfChampions / CHAMPS_PER_PAGE);
$servers = array("br", "eune", "euw", "lan", "las", "kr", "tr", "ru", "na", "oce");

if (isset($_POST["server"]) || isset($_GET["server"])) {
    $server = isset($_POST["server"]) ? $_POST["server"] : $_GET["server"];

    if (!in_array($server, $servers)) {
        $server = "euw";
    }
} else {
    $server = "euw";
}

if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}

$lowerBound = ($page - 1) * CHAMPS_PER_PAGE;
// Get champiosn & data
$winRates = $mysqli->query("SELECT (blueSideWins / (blueSideWins + redSideWins)) as blueSideRate, (redSideWins / (blueSideWins + redSideWins)) as redSideRate, (blueSideWins + redSideWins) as numberOfGames FROM " . WINRATE_TABLE . " WHERE region ='" . $server . "'")->fetch_assoc();
$numberOfGames = $mysqli->query("SELECT SUM(blueSideWins + redSideWins) as numberOfGames FROM " . WINRATE_TABLE)->fetch_assoc();
// TODO order by name
$champs = $mysqli->query("SELECT * FROM " . CHAMP_TABLE . " ORDER BY name LIMIT " . $lowerBound . ", " . CHAMPS_PER_PAGE);

$serverStats = array();
$allServerStats = array();

while ($champ = $champs->fetch_assoc()) {
    $stat = $mysqli->query("SELECT * FROM " . AVERAGE_TABLE . " WHERE region = '" . $server . "' AND championId = " . $champ["id"]);
    $columns = $stat->fetch_fields();

    $allServerQuery = "SELECT ";
	$curr = $stat->fetch_assoc();
    foreach ($curr as $key => $value) {
        if (!isset($serverStats[$key])) {
            $serverStats[$key] = array();
        }
        if ($key == "championId" || $key == "region") {
            $allServerQuery .= $key . ", ";
        } else if ($key == "numberOfGames" || $key == "banRate") {
            $allServerQuery .= " SUM(" . $key . ") AS " . $key . ", ";
        } else {
            $allServerQuery .= " SUM(" . $key . " * numberOfGames) AS " . $key . ", ";
        }
        if ($key == "championId") {
            $key = "champion";
            $value = $champ["name"];
        }

		if ($key == "banRate") {
			$value = $value / max(1, $winRates["numberOfGames"]);
		}

		if ($key == "pickRate") {
			$value = $curr["numberOfGames"] / max(1, $winRates["numberOfGames"]);
		}
		$serverStats[$key][$champ["id"]] = $value;
    }
    $allServerQuery = rtrim($allServerQuery, ", ");
    $allServerQuery .= " FROM " . AVERAGE_TABLE . " WHERE championId = " . $champ["id"];
    $all = $mysqli->query($allServerQuery)->fetch_assoc();
    foreach ($all as $key => $value) {
        if (!isset($allServerStats[$key])) {
            $allServerStats[$key] = array();
        }
        if ($key == "championId") {
            $key = "champion";
            $value = $champ["name"];
        }

		if ($key != "banRate" && $key != "pickRate") {
			$allServerStats[$key][$champ["id"]] = $value / $all["numberOfGames"];
		} else {
			$allServerStats[$key][$champ["id"]] = $value / $numberOfGames["numberOfGames"];			
		}

		}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>URF Champions Statistics</title> 
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <meta charset="UTF-8" />
    <script src="script/d3.v3.min.js"></script>
    <script type="text/javascript" src="script/jquery-1.10.2.min.js"></script>
    <!-- timeline -->
    <link id="data-uikit-theme" rel="stylesheet" href="css/uikit.docs.min.css">
    <script src="script/uikit.min.js"></script>
    <script src="script/tooltip.js"></script>
    <script src="script/jquery.pause.min.js"></script>
    <script src="script/jquery.timer.js"></script>
    <script src="script/timeline.js"></script>
    <script src="script/functions.js"></script>
    <script src="script/templates.js"></script>
    <script src="script/prototypes.js"></script>
    <script src="script/update.js"></script>
    <script src="script/timelineCallbacks.js"></script>
    <script src="script/pablo.min.js"></script>
    <script src="script/jquery.tipsy.js"></script>
    <link rel="stylesheet" href="css/tipsy.css">
    <link rel="stylesheet" href="css/timeline.css">
    <link rel="stylesheet" href="css/update.css">
</head>
<body>
    <div class="main_info" style="width: 90%;">
	<div class="header">
		<div id="blueVictory" class="tblue" data-uk-tooltip title="Blue Team Win Rate in <?php echo regionToServer(strtoupper($server)); ?> Server"><div style="margin:0 auto;margin-left:15px;float:left;"><img src="images/blueteam.png" alt="blueteam"></div><div style="vertical-align:middle; margin:0 auto; height:100%;float:left; margin-left:10px;margin-top:2px;"><?php echo number_format((float) $winRates["blueSideRate"]*100, 2, '.', ''); ?>%</div></div>
        <div class="summary">
            <div class="sele">
                <form action="?page=1" method="post">
                    <select name="server">
                        <option value="na" 
                        <?php
                        if ($server == "na") {
                            echo "selected=\"\"";
                        }
                        ?> 
                                data-tselect-selected-label="NA">North America</option>
                        <option value="euw" 
                        <?php
                        if ($server == "euw") {
                            echo "selected=\"\"";
                        }
                        ?> 

                                data-tselect-selected-label="EUW">Europe West</option>
                        <option value="eune" 
                        <?php
                        if ($server == "eune") {
                            echo "selected=\"\"";
                        }
                        ?> 
                                data-tselect-selected-label="EUNE">Europe Nordic &amp; East</option>
                        <option value="br"
                        <?php
                        if ($server == "br") {
                            echo "selected=\"\"";
                        }
                        ?> 
                                data-tselect-selected-label="BR">Brazil</option>
                        <option value="tr"
                        <?php
                        if ($server == "tr") {
                            echo "selected=\"\"";
                        }
                        ?> 
                                data-tselect-selected-label="TR">Turkey</option>
                        <option value="kr" 
                        <?php
                        if ($server == "kr") {
                            echo "selected=\"\"";
                        }
                        ?> 
                                data-tselect-selected-label="TR">Korea</option>
                        <option value="ru" 
                        <?php
                        if ($server == "ru") {
                            echo "selected=\"\"";
                        }
                        ?> 
                                data-tselect-selected-label="RU">Russia</option>
                        <option value="lan" 
                        <?php
                        if ($server == "lan") {
                            echo "selected=\"\"";
                        }
                        ?> 

                                data-tselect-selected-label="LAN">Latin America North</option>
                        <option value="las"
                        <?php
                        if ($server == "las") {
                            echo "selected=\"\"";
                        }
                        ?> 

                                data-tselect-selected-label="LAS">Latin America South</option>
                        <option value="oce" 
                        <?php
                        if ($server == "oce") {
                            echo "selected=\"\"";
                        }
                        ?> 

                                data-tselect-selected-label="OCE">Oceania</option>
                    </select>
                    <input class="inputte" name="go" value="Filter" type="submit">

                </form>
                <span>
                    Current Server: <?php echo regionToServer(strtoupper($server)); ?> / <?php echo number_format($winRates["numberOfGames"], 0); ?> Matches
                    <br />
                </span>
                <span>
                    Total Number Of Matches: <?php echo number_format($numberOfGames["numberOfGames"], 0); ?>
                </span>
                <br>
                <span class="nextprev">
                    <?php if ($page > 1) { ?><a href="?page=<?php echo ($page - 1) ?>&amp;server=<?php echo $server ?>">Previous </a> <?php } echo $page ?> of <?php echo $pageCount ?><?php if ($page < 13) { ?><a href="?page=<?php echo ($page + 1) ?>&amp;server=<?php echo $server ?>"> Next</a> <?php } ?>
                </span>
            </div>
		</div>
        <div id="redVictory" class="tred" data-uk-tooltip title="Red Team Win Rate in <?php echo regionToServer(strtoupper($server)); ?> Server"><div style="margin:0 auto;margin-right:15px;float:right;"><img src="images/redteam.png" alt="redteam"></div><div style="vertical-align:middle; margin:0 auto; height:100%;float:right; margin-right:10px;margin-top:2px;"><?php echo number_format((float) $winRates["redSideRate"]*100, 2, '.', ''); ?>%</div></div>
        </div>
        <div class="backurf"> 
            <table id="keywords">
                <?php
                foreach ($serverStats as $key => $value) {
                    if ($key == "numberOfGames" || $key == "championId" || $key == "region") {
                        continue;
                    }
                    if ($key == "champion") {
                        ?>
                        <thead>
                            <tr>
                                <td style="width:4.34%" class="border">Champion</td>
                                <?php
                                foreach ($serverStats[$key] as $champId => $value) {
                                    echo "<th  style=\"width:4.34%\" class=\"border\" colspan=\"2\">";
                                    echo tableCell($key, $value);
                                    echo "</th>";
                                }
                                ?>
                            </tr>
                            <tr>
                                <td class="yellow border"></td>
                                <?php
                                for ($i = 0; $i < count($serverStats["region"]); $i++) {
                                    echo "<td class=\"yellow\" style=\"font-family: 'gulim';text-shadow: 0px 1px rgba(255, 255, 255, 0.9);font-weight: bold;color: black;\">" . regionToServer(strtoupper($server)) . "</td>";
                                    echo "<td class=\"border yellow\" style=\"font-family: 'gulim';text-shadow: 0px 1px rgba(255, 255, 255, 0.9);font-weight: bold;color: black;\">All Servers</td>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <?php
                    } else {
                        echo "<tr>";
                        echo "<td class=\"yellow border\" style=\"font-family: 'gulim';text-shadow: 0px 1px rgba(255, 255, 255, 0.9);font-weight: bold;color:black;\">" . ($key != "firstBloodKill" ? transformColumnNameToText($key) : "First Blood") . "</td>";
                        foreach ($serverStats[$key] as $champId => $value) {
                            ?>
                            <td class="yellow" style="font-family: 'gulim';text-shadow: 0px 1px rgba(255, 255, 255, 0.9);color: #C0392B;"><?php 
							echo tableCell($key, $value); ?></td>
                            <td class="yellow border" style="font-family: 'gulim';text-shadow: 0px 1px rgba(255, 255, 255, 0.9);color: #2980B9;"><?php echo tableCell($key, $allServerStats[$key][$champId]) ?> </td>
                            <?php
                        }
                        echo "</tr>";
                    }
                }
                ?>
            </table>
        </div>
		</div>
</body>
</html>