<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../../lib/connection.php";
require_once "../../lib/GJPCheck.php";
require_once "../../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../../lib/mainLib.php";
$gs = new mainLib();
require_once "../discordLib.php";
$dis = new discordLib();
if(empty($_POST["userName"])){
	exit ("The server did not receive data");
}
$userName = $_POST['userName'];
$channelID = $_POST['channel'];
$query = $db->prepare("SELECT extID FROM users WHERE userName = :userName OR userID = :userName LIMIT 1");
$query->execute([':userName' => $userName]);
if($query->rowCount() == 0){
	$nothing = "<@".$_POST['tagID'].">, Nothing Found";
	$data = array("content"=> $nothing);                                               
	$data_string = json_encode($data);
	$dis->discordNotify($channelID, $data_string);
	exit ("profile Command: nothing found");
}
$targetAccID = $query->fetchColumn();
$query = $db->prepare("SELECT accountID FROM accounts WHERE accountID = :accountID");
$query->execute([':accountID' => $targetAccID]);
if($query->rowCount() == 0){
	$nothing = "<@".$_POST['tagID'].">, Nothing Found";
	$data = array("content"=> $nothing);                                               
	$data_string = json_encode($data);
	$dis->discordNotify($channelID, $data_string);
	exit ("profile Command: nothing found");
}
$dis->discordNotify($channelID, $dis->accEmbedContent(2, $dis->title(22), $dis->iconProfile($targetAccID), $dis->embedColor(7), "misc/auto.png", "Chaos-Bot", $targetAccID, $_POST["tagID"]));
echo "profile Command: $userName's stats found!";
?>