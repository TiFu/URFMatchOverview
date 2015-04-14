<?php
include("config/config.php");
include("functions.php");
$mysqli = new mysqli(SERVER, DB_USER, DB_PW, DATABASE);

define("CHAMPS_PER_PAGE", 10);

$numberOfChampions = $mysqli->query("SELECT * FROM " .CHAMP_TABLE)->num_rows;
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

$lowerBound = ($page-1) * CHAMPS_PER_PAGE;	
// Get champiosn & data
$winRates = $mysqli->query("SELECT (blueSideWins / (blueSideWins + redSideWins)) as blueSideRate, (redSideWins / (blueSideWins + redSideWins)) as redSideRate FROM " .WINRATE_TABLE ." WHERE region ='" .$server ."'")->fetch_assoc();
$numberOfGames = $mysqli->query("SELECT (blueSideWins + redSideWins) as numberOfGames FROM " .WINRATE_TABLE)->fetch_assoc();
// TODO order by name
$champs = $mysqli->query("SELECT * FROM " .CHAMP_TABLE ." ORDER BY name LIMIT " .$lowerBound .", " .CHAMPS_PER_PAGE);

$serverStats = array();
$allServerStats = array();

while ($champ = $champs->fetch_assoc()) {
	$stat = $mysqli->query("SELECT * FROM " .AVERAGE_TABLE ." WHERE region = '" .$server ."' AND championId = " .$champ["id"]);
	$columns = $stat->fetch_fields();

	$allServerQuery = "SELECT ";
	foreach ($stat->fetch_assoc() as $key => $value) {
		if (!isset($serverStats[$key])) {
			$serverStats[$key] = array();
		}
		if ($key == "championId" || $key == "region") {
			$allServerQuery .= $key .", ";
		} else if ($key == "numberOfGames") {
			$allServerQuery .= " SUM(" .$key .") AS " .$key .", ";			
		} else {
			$allServerQuery .= " SUM(" .$key ." * numberOfGames) AS " .$key .", ";
		}
		if ($key == "championId") {
			$key = "champion";
			$value = $champ["name"];
		}
		$serverStats[$key][$champ["id"]] = $value;
	}
	$allServerQuery = rtrim($allServerQuery, ", ");
	$allServerQuery .= " FROM " .AVERAGE_TABLE ." WHERE championId = " .$champ["id"];
	$all = $mysqli->query($allServerQuery)->fetch_assoc();
	foreach ($all as $key => $value) {
		if (!isset($allServerStats[$key])) {
			$allServerStats[$key] = array();
		}
		if ($key == "championId") {
			$key = "champion";
			$value = $champ["name"];
		}
		$allServerStats[$key][$champ["id"]] = $value;		
	}
}
?>
   <head>
       <title>URF Champions Statistics</title> 
          <link href="css/style.css" rel="stylesheet" type="text/css" />
    <body>
        <div class="main_info" style="width: 1280px;">
            <div class="summary">
                <div id="blueVictory" class="tblue" data-uk-tooltip title="Blue Team Win Rate On <?php echo $server; ?> Server"><?php echo number_format((float) $winRates["blueSideRate"], 2, '.', ''); ?>%</div>
				<div class="sele">
                    <form action="" method="post">
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
						<span style="margin-left:-100px">
                        Current Server: <?php echo regionToServer(strtoupper($server)); ?>
                        <br />
						</span>
						<span style="margin-left:-100px">
                        Number Of Matches: <?php echo $numberOfGames["numberOfGames"]; ?>
						</span>
						<br>
						<span style="margin-left:-100px">
						<?php if ($page > 1) { ?><a href="champion.php?page=<?php echo ($page-1) ?>&server=<?php echo $server?>">previous</a> <?php } echo $page ?> of <?php echo $pageCount ?><?php if ($page < 13) { ?><a href="champion.php?page=<?php echo ($page+1) ?>&server=<?php echo $server?>">next</a> <?php } ?>
						</span>
                </div>
                <div id="redVictory" style="padding-right: 170px;" class="tred" data-uk-tooltip title="Red Team Win Rate On <?php echo $server; ?> Server"><?php echo number_format((float) $winRates["redSideRate"], 2, '.', ''); ?>%</div>
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
										<td width="4.34%" class="border">Champion</td>
										<?php
										foreach ($serverStats[$key] as $champId => $value) {
												echo "<th  width=\"4.34%\" class=\"border\" colspan=\"2\">";
												echo tableCell($key, $value);
												echo "</th>";
	
										}
										?>
									</tr>
									<tr>
										<td class="yellow border"></td>
										<?php
											for ($i = 0; $i < count($serverStats["region"]); $i++) {
												echo "<td class=\"yellow\">" .regionToServer(strtoupper($server)) ."</td>";
												echo "<td class=\"border yellow\">All Servers</td>";
											}
										?>
									</tr>
								</thead>
								<?php
							} else {
								echo "<tr>";
								echo "<td class=\"yellow border\">" .($key != "firstBloodKill" ? transformColumnNameToText($key) : "First Blood") ."</td>";
								foreach ($serverStats[$key] as $champId => $value) {
									?>
										<td class="yellow"><?php echo tableCell($key, $value); ?></td>
										<td class="yellow border"><?php echo tableCell($key, $allServerStats[$key][$champId]) ?> </td>
									<?php
								}
								echo "</tr>";
							}
						}
				?>
				</table>
			</div>
</body>