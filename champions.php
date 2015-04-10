<!DOCTYPE html>
<?php
$db = new mysqli('localhost', 'root', '', 'challenge');
if (isset($_POST['go'])) {
    $serv = $_POST['server'];
} else {
    $serv = "na";
}

if (!$result4 = $db->query("SELECT * FROM `severrate` WHERE `name` =  '$serv'")) {
    die('There was an error running the query [' . $db->error . ']');
}
$rowx4 = $result4->fetch_assoc();

$red = $rowx4['winratered'] / $rowx4['gamenum'] * 100;
$blue = $rowx4['winrateblue'] / $rowx4['gamenum'] * 100;
?>
<html>
    <head>
        <title>URF Champions Statistical</title> 
        <meta charset="UTF-8" />
        <script src="script/d3.v3.min.js"></script>
        <script type="text/javascript" src="script/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="script/jquery.tablesorter.min.js"></script> 
        <!-- timeline -->
        <link id="data-uikit-theme" rel="stylesheet" href="css/uikit.docs.min.css">
        <script src="script/uikit.min.js"></script>
        <script src="script/tooltip.js"></script>
        <script src="script/jquery.pause.min.js"></script>
        <script src="script/jquery.timer.js"></script>
        <script src="script/timeline.js"></script>
        <script src="script/functions.js"></script>
        <script src="script/update.js"></script>
        <script src="script/timelineCallbacks.js"></script>
        <link rel="stylesheet" href="css/timeline.css">
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <div class="main_info" style="width: 1280px;">
            <div class="summary">
                <div id="blueVictory" class="tblue" data-uk-tooltip title="Blue Team Win Rate On <?php echo $rowx4['name']; ?> Server"><?php echo number_format((float)$blue, 2, '.', ''); ?>%</div>
                <div class="sele">
                    <form action="" method="post">
                        <select data-cached-title="قم بإختيار السيرفر" name="server">
                            <option value="na" data-tselect-selected-label="NA">North America</option>
                            <option value="euw" data-tselect-selected-label="EUW">Europe West</option>
                            <option value="eune" data-tselect-selected-label="EUNE">Europe Nordic &amp; East</option>
                            <option value="br" data-tselect-selected-label="BR">Brazil</option>
                            <option value="tr" data-tselect-selected-label="TR">Turkey</option>
                            <option value="kr" data-tselect-selected-label="TR">Korea</option>
                            <option value="ru" data-tselect-selected-label="RU">Russia</option>
                            <option value="lan" data-tselect-selected-label="LAN">Latin America North</option>
                            <option value="las" data-tselect-selected-label="LAS">Latin America South</option>
                            <option value="oce" data-tselect-selected-label="OCE">Oceania</option>
                        </select>
                        <input class="inputte" name="go" value="Filter" type="submit">

                    </form>
                    <div class="stats">
                        Server Selected : <?php echo $rowx4['name']; ?>
                        <br />
                        Number Of Matches : <?php echo $rowx4['gamenum']; ?>
                    </div>
                </div>
                <div id="redVictory" style="padding-right: 170px;" class="tred" data-uk-tooltip title="Red Team Win Rate On <?php echo $rowx4['name']; ?> Server"><?php echo number_format((float)$red, 2, '.', ''); ?>%</div>
            </div>

            <div class="backurf"> 
                <table id="keywords" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th colspan="2"><span data-uk-tooltip title="Champion Rank">Rank</span></th>
                            <th><span data-uk-tooltip title="Champion Pick Rate">Pick</span></th>
                            <th><span data-uk-tooltip title="Win Rate">Win</span></th>                                                        
                            <th><span data-uk-tooltip title="Kill/Death/Assist Rate">KDA</span></th>
                            <th><span data-uk-tooltip title="Champion Ban Rate">Ban</span></th>
                            <th><span data-uk-tooltip title="Champion Kills Rate">Kills</span></th>
                            <th><span data-uk-tooltip title="Champion Death Rate">Death</span></th>
                            <th><span data-uk-tooltip title="Champion Assist Rate">Assist</span></th>
                            <th><span data-uk-tooltip title="First Blood Rate">F B</span></th>
                            <th><span data-uk-tooltip title="Double Kill Rate">D K</span></th>
                            <th><span data-uk-tooltip title="Triple Kill Rate">T K</span></th>
                            <th><span data-uk-tooltip title="Quadra Kill Rate">Q K</span></th>
                            <th><span data-uk-tooltip title="Penta Kill Rate">P K</span></th>
                            <th><span data-uk-tooltip title="Killing Spree Rate">K S</span></th>
                            <th><span data-uk-tooltip title="Creeps Slain Rate (Minions & Jungle Monsters)">C S</span></th>
                            <th><span data-uk-tooltip title="Tower Destroy Rate">Tower Destroy</span></th>
                            <th><span data-uk-tooltip title="Wards Place Rate">Wards Place</span></th>
                            <th><span data-uk-tooltip title="True Damage Rate (For Champions)">True Dmg</span></th>
                            <th><span data-uk-tooltip title="Physical Damage Rate (For Champions)">Physical Dmg</span></th>
                            <th><span data-uk-tooltip title="Magic Damage Rate (For Champions)">Magic Dmg</span></th>
                            <th><span data-uk-tooltip title="Total Damage Rate">Total Dmg</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $num = 1;
                        $championnum = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 48, 50, 51, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64,
                            67, 68, 69, 72, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 89, 90, 91, 92, 96, 98, 99, 101, 102, 103, 104, 105, 106, 107, 110, 111, 112, 113, 114, 115, 117, 119, 120, 121, 122, 126, 127, 131, 133, 134, 143, 150, 222, 154, 157, 161, 201, 236, 238, 254, 266, 267, 268, 412, 421, 429, 432
                        );
                        for ($c = 0; $c < count($championnum); $c++) {
                            $lastchamp = $championnum[$c];
                            if (!$result = $db->query("SELECT * FROM $serv WHERE id = $lastchamp ORDER BY 'numgames' DESC")) {
                                die('There was an error running the query [' . $db->error . ']');
                            } else {
                                $result2 = $db->query("SELECT * FROM champs WHERE id = $lastchamp");
                                $row = $result->fetch_assoc();
                                $rowx = $result2->fetch_assoc();
                                $result3 = $db->query("
                                   SELECT `id` , sum( `numgames` ) AS `numgames` , sum( `pick` ) AS `pick` , sum( `kda` ) AS `kda` , sum( `ban` ) AS `ban` , sum( `kills` ) AS `kills` , sum( `death` ) AS `death` , sum( `assist` ) AS `assist` , sum( `fb` ) AS `fb` , sum( `dk` ) AS `dk` , sum( `tk` ) AS `tk` , sum( `qk` ) AS `qk` , sum( `pk` ) AS `pk` , sum( `ks` ) AS `ks` , `cs` , sum( `cs` ) AS `cs` , sum( `towerdestroy` ) AS `towerdestroy` , sum( `winrate` ) AS `winrate` , sum( `wardplace` ) AS `wardplace` , sum( `truedmg` ) AS `truedmg` , sum( `phycdmg` ) AS `phycdmg` , sum( `magicdmg` ) AS `magicdmg` , sum( `totaldmg` ) AS `totaldmg`
FROM (
SELECT *
FROM eune where id = $lastchamp
UNION ALL
SELECT *
FROM euw where id = $lastchamp
UNION ALL
SELECT *
FROM oce where id = $lastchamp
UNION ALL
SELECT *
FROM lan where id = $lastchamp
UNION ALL
SELECT *
FROM las where id = $lastchamp
UNION ALL
SELECT *
FROM tr where id = $lastchamp
UNION ALL
SELECT *
FROM kr where id = $lastchamp
UNION ALL
SELECT *
FROM ru where id = $lastchamp
UNION ALL
SELECT *
FROM na where id = $lastchamp
UNION ALL
SELECT *
FROM br where id = $lastchamp
)x
GROUP BY `id`
");
                                $rowx3 = $result3->fetch_assoc();
                                echo "<tr class=\"yellow\">\n";
                                echo "<td><span>$num</span></td>\n";
                                echo "<td><span><img data-uk-tooltip title=\"{$rowx['name']}\" style=\"border-radius: 50%;\" width=\"24\" height=\"24\" src=\"images/champion/" . str_replace(" ", "%20", $rowx['name']) . "46.png\" alt=\"\" /></span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['pick'], 2)) . "%</percentege> Pickrate In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['pick'], 0) / 10) . "%</rate> Pickrate In All Servers\">" . (round($row['pick'], 2)) . "%</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['winrate'], 2)) . "%</percentege> Win Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['winrate'], 0) / 10) . "%</rate> Win Rate Per Game In All Servers\">" . (round($row['winrate'], 2)) . "%</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . round($row['kda'], 2) . "</percentege> KDA Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['kda'], 1) / 10) . "%</rate> KDA Per Game In All Servers\">" . round($row['kda'], 2) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . round($row['ban'], 2) . "%</percentege> Ban Rate In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['ban'], 0) / 10) . "%</rate> Ban Rate In All Servers\">" . round($row['ban'], 2) . "%</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . round($row['kills'], 2) . "</percentege> Kill Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['kills'], 0) / 10) . "</rate> Kill Per Game In All Servers\">" . round($row['kills'], 2) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . round($row['death'], 2) . "</percentege> Assist Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['death'], 0) / 10) . "</rate> Assist Per Game In All Servers\">" . round($row['death'], 2) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . round($row['assist'], 2) . "</percentege> Death Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['assist'], 0) / 10) . "</rate> Death Per Game In All Servers\">" . round($row['assist'], 2) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['fb'], 2)) . "%</percentege> First Blood Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['fb'], 2)) . "%</rate> First Blood Rate Per Game In All Servers\">" . (round($row['fb'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['dk'], 2)) . "%</percentege> Double Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['dk'], 2)) . "%</rate> Double Kill Rate Per Game In All Servers\">" . (round($row['dk'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['tk'], 2)) . "%</percentege> Triple Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['tk'], 2)) . "%</rate> Triple Kill Rate Per Game In All Servers\">" . (round($row['tk'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['qk'], 2)) . "%</percentege> Quadra Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['qk'], 2)) . "%</rate> Quadra Kill Rate Per Game In All Servers\">" . (round($row['qk'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['pk'], 2)) . "%</percentege> Penta Kill Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['pk'], 2)) . "%</rate> Penta Kill Rate Per Game In All Servers\">" . (round($row['pk'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['ks'], 2)) . "%</percentege> Killing Spree Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['ks'], 2)) . "%</rate> Killing Spree Rate Per Game In All Servers\">" . (round($row['ks'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['cs'], 2)) . "</percentege> Creeps Slain Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['cs'], 2)) . "</rate> Creeps Slain Rate Per Game In All Servers\">" . (round($row['cs'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['towerdestroy'], 2)) . "%</percentege> Tower Destroy Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['towerdestroy'], 2)) . "%</rate> Tower Destroy Rate Per Game In All Servers\">" . (round($row['towerdestroy'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>" . (round($row['wardplace'], 2)) . "%</percentege> Ward Place Rate Per Game In $serv <br/>{$rowx['name']} Got <rate>" . (round($rowx3['wardplace'], 2)) . "%</rate> Ward Place Rate Per Game In All Servers\">" . (round($row['wardplace'], 2)) . "</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['truedmg'],0))."</percentege> True Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['truedmg'],0) / 10))."</rate> True Damage Per Game In All Servers\">".number_format(round($row['truedmg'],0))."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['phycdmg'],0))."</percentege> Physical Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['phycdmg'],0) / 10))."</rate> Physical Damage Per Game In All Servers\">".number_format(round($row['phycdmg'],0))."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['magicdmg'],0))."</percentege> Magaic Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['magicdmg'],0) / 10))."</rate> Magaic Damage Per Game In All Servers\">".number_format(round($row['magicdmg'],0))."</span></td>\n";
                                echo "<td><span data-uk-tooltip title=\"{$rowx['name']} Got <percentege>".number_format(round($row['totaldmg'],0))."</percentege> Total Damage Per Game In $serv <br/>{$rowx['name']} Got <rate>".number_format((round($rowx3['totaldmg'],0) / 10))."</rate> Total Damage Per Game In All Servers\">".number_format(round($row['totaldmg'],0))."</span></td>\n";
                                echo "                        </tr> ";
                            }
                            $num++;
                        }
                        ?>
                    </tbody>
                </table>             
                <script type="text/javascript">
                    $(function () {
                        $('#keywords').tablesorter();
                    });
                </script>            
            </div>
        </div>  
    </body>
</html>
