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
$query->execute([':userName' => $userName]);
if($query->rowCount() == 0){
	$nothing = "<@".$_POST['tagID'].">, Nothing Found";
	$data = array("content"=> $nothing);                                               
	$data_string = json_encode($data);
	$dis->discordNotify($channelID, $data_string);
	exit ("profile Command: nothing found");
}
$array = $query->fetchAll();
foreach($array as $row) {
    $Luser[] = $row['userName'];
    $Ltypevalue[] = $row[$type];
    $Lstars[] = $row['stars'];
    $Ldiamond[] = $row['diamonds'];
    $Lscoins[] = $row['coins'];
    $Lucoins[] = $row['userCoins'];
    $Ldemons[] = $row['demons'];
    $Lcp[] = $row['creatorPoints'];
}
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

$data = array(
    "content"=> "<@".$_POST["tagID"].">, Here TOP 10 Leaderboard based on $Ltype",
    'embed'=> [
        "title"=> "$Licon __Top 10 Leaderboards!!!__",
        "description"=> "───────────────────",
        "fields"=> [
            ["name"=> "$icon_top1 1# __".$Luser[0]."__ - $Licon: `".$Ltypevalue[0]."`", 
            "value"=> "⠀$icon_star `".$Lstars[0]."`|$icon_diamond `".$Ldiamond[0]."`|$icon_secretcoin `".$Lscoins[0]."`|$icon_verifycoins `".$Lucoins[0]."`|$icon_demon `".$Ldemons[0]."`|$icon_cp `".$Lcp[0]."`\n───────────────────"],
            ["name"=> "$icon_top10 2# __".$Luser[1]."__ - $Licon: `".$Ltypevalue[1]."`", 
            "value"=> "⠀$icon_star `".$Lstars[1]."`|$icon_diamond `".$Ldiamond[1]."`|$icon_secretcoin `".$Lscoins[1]."`|$icon_verifycoins `".$Lucoins[1]."`|$icon_demon `".$Ldemons[1]."`|$icon_cp `".$Lcp[1]."`\n───────────────────"],
            ["name"=> "$icon_top50 3# __".$Luser[2]."__ - $Licon: `".$Ltypevalue[2]."`", 
            "value"=> "⠀$icon_star `".$Lstars[2]."`|$icon_diamond `".$Ldiamond[2]."`|$icon_secretcoin `".$Lscoins[2]."`|$icon_verifycoins `".$Lucoins[2]."`|$icon_demon `".$Ldemons[2]."`|$icon_cp `".$Lcp[2]."`\n───────────────────"],
            ["name"=> "$icon_top100 4# __".$Luser[3]."__ - $Licon: `".$Ltypevalue[3]."`", 
            "value"=> "⠀$icon_star `".$Lstars[3]."`|$icon_diamond `".$Ldiamond[3]."`|$icon_secretcoin `".$Lscoins[3]."`|$icon_verifycoins `".$Lucoins[3]."`|$icon_demon `".$Ldemons[3]."`|$icon_cp `".$Lcp[3]."`\n───────────────────"],
            ["name"=> "$icon_top200 5# __".$Luser[4]."__ - $Licon: `".$Ltypevalue[4]."`", 
            "value"=> "⠀$icon_star `".$Lstars[4]."`|$icon_diamond `".$Ldiamond[4]."`|$icon_secretcoin `".$Lscoins[4]."`|$icon_verifycoins `".$Lucoins[4]."`|$icon_demon `".$Ldemons[4]."`|$icon_cp `".$Lcp[4]."`\n───────────────────"],
            ["name"=> "$icon_top500 6# __".$Luser[5]."__ - $Licon: `".$Ltypevalue[5]."`", 
            "value"=> "⠀$icon_star `".$Lstars[5]."`|$icon_diamond `".$Ldiamond[5]."`|$icon_secretcoin `".$Lscoins[5]."`|$icon_verifycoins `".$Lucoins[5]."`|$icon_demon `".$Ldemons[5]."`|$icon_cp `".$Lcp[5]."`\n───────────────────"],
            ["name"=> "$icon_top1000 7# __".$Luser[6]."__ - $Licon: `".$Ltypevalue[6]."`", 
            "value"=> "⠀$icon_star `".$Lstars[6]."`|$icon_diamond `".$Ldiamond[6]."`|$icon_secretcoin `".$Lscoins[6]."`|$icon_verifycoins `".$Lucoins[6]."`|$icon_demon `".$Ldemons[6]."`|$icon_cp `".$Lcp[6]."`\n───────────────────"],
            ["name"=> "$icon_globalrank 8# __".$Luser[7]."__ - $Licon: `".$Ltypevalue[7]."`", 
            "value"=> "⠀$icon_star `".$Lstars[7]."`|$icon_diamond `".$Ldiamond[7]."`|$icon_secretcoin `".$Lscoins[7]."`|$icon_verifycoins `".$Lucoins[7]."`|$icon_demon `".$Ldemons[7]."`|$icon_cp `".$Lcp[7]."`\n───────────────────"],
            ["name"=> "$icon_globalrank 9# __".$Luser[8]."__ - $Licon: `".$Ltypevalue[8]."`", 
            "value"=> "⠀$icon_star `".$Lstars[8]."`|$icon_diamond `".$Ldiamond[8]."`|$icon_secretcoin `".$Lscoins[8]."`|$icon_verifycoins `".$Lucoins[8]."`|$icon_demon `".$Ldemons[8]."`|$icon_cp `".$Lcp[8]."`\n───────────────────"],
            ["name"=> "$icon_globalrank 10# __".$Luser[9]."__ - $Licon: `".$Ltypevalue[9]."`", 
            "value"=> "⠀$icon_star `".$Lstars[9]."`|$icon_diamond `".$Ldiamond[9]."`|$icon_secretcoin `".$Lscoins[9]."`|$icon_verifycoins `".$Lucoins[9]."`|$icon_demon `".$Ldemons[9]."`|$icon_cp `".$Lcp[9]."`\n───────────────────"]],
        "color"=> $dis->embedColor(7),
        "footer"=> ["text"=> "Leaderboard dated on: ".date('Y-m-d H:i:s')],
        "thumbnail"=> ["url"=> ($iconhost."misc/gdps.png")],
    ]);
$data_string = json_encode($data);
$dis->discordNotify($_POST['channel'], $data_string);
echo "$Ltype Leaderboard command: Done!";
?>