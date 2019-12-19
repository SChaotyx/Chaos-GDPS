<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../../lib/connection.php";
include __DIR__ . "/../../../config/discord.php";
require_once "../../lib/GJPCheck.php";
require_once "../../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../../lib/mainLib.php";
$gs = new mainLib();
require_once "../discordLib.php";
$dis = new discordLib();
require_once "../emojis.php";

if(empty($_POST["type"])){
	exit ("The server did not receive data");
}
$type = $_POST["type"];
$tag = $_POST["tagID"];

$query = $db->prepare("SELECT * FROM users WHERE $type AND isBanned = 0 ORDER BY $type DESC LIMIT 10");
if($type === "creatorPoints"){
    $query = $db->prepare("SELECT * FROM users WHERE $type AND isCreatorBanned = 0 ORDER BY $type DESC LIMIT 10");
}
$query->execute();
if($query->rowCount() == 0){
	$nothing = "<@".$_POST['tagID'].">, Nothing Found";
	$data = array("content"=> $nothing);                                               
	$data_string = json_encode($data);
	$dis->discordNotify($_POST['channel'], $data_string);
	exit ("profile Command: nothing found");
}
$array = $query->fetchAll();
$lol = "";
$pos = 0;
switch($type){
    case "creatorPoints": $Ltype = "Creator Points"; $Licon = $icon_cp;
    break;
    case "stars": $Ltype = "Stars"; $Licon = $icon_star;
    break;
    case "demons": $Ltype = "Demons"; $Licon = $icon_demon;
    break;
    case "userCoins": $Ltype = "User Coins"; $Licon = $icon_verifycoins;
    break;
    case "coins": $Ltype = "Secret Coins"; $Licon = $icon_secretcoin;
    break;
    case "diamonds": $Ltype = "Diamonds"; $Licon = $icon_diamond;
    break;
}
foreach($array as $row) {
    $pos ++;
    switch($pos){
        case 1: $icontop = $icon_top1; break;
        case 2: $icontop = $icon_top10; break;
        case 3: $icontop = $icon_top50; break;
        case 4: $icontop = $icon_top100; break;
        case 5: $icontop = $icon_top200; break;
        case 6: $icontop = $icon_top500; break;
        case 7: $icontop = $icon_top1000; break;
        case 8: $icontop = $icon_globalrank; break;
        case 9: $icontop = $icon_globalrank; break;
        case 10: $icontop = $icon_globalrank; break;
    }
    $lol .= "$icontop `$pos#` - $Licon `".$dis->charCount($row[$type])."` __**".$row['userName']."**__\n───────────────────\n";
}


$data = array(
    "content"=> "<@".$_POST["tagID"].">, Here TOP 10 Leaderboard based on $Ltype",
    'embed'=> [
        "title"=> "$Licon __Top 10 Leaderboards!!!__",
        "description"=> "───────────────────\n".$lol,
        "footer"=> ["text"=> "Leaderboard dated on: ".date('Y-m-d H:i:s')],
        "thumbnail"=> ["url"=> ($iconhost."misc/gdpsthumb.png")],
    ]);
$data_string = json_encode($data);
$dis->discordNotify($_POST['channel'], $data_string);
echo "$Ltype Leaderboard command: Done!";
?>