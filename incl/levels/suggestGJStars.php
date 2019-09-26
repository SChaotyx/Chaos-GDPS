<?php
//error_reporting(0);
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
require_once "../discord/discordLib.php";
$ep = new exploitPatch();
$gs = new mainLib();
$dis = new discordLib();
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$feature = $ep->remove($_POST["feature"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
if($accountID != "" AND $gjp != ""){
	$GJPCheck = new GJPCheck();
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		$levelLength = $gs->getLevelValue($levelID, "levelLength");
		if($levelLength > 1){
			if($gs->checkPermission($accountID, "actionRateStars")){
				$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
				if($gs->getLevelValue($levelID, "rateDate") == 0){
					$timerated = 0;
				}
				if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
				$difficulty = $gs->getDiffFromStars($stars);
				$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"], $feature);
				$gs->updatecp($levelID);
				$dis->discordNotify(1, $dis->embedContent(2, $dis->title(1), $dis->diffthumbnail($levelID), $dis->embedColor(1), $dis->modBadge($accountID), $dis->footerText($accountID)." | Level ID: $levelID", $levelID, 0));
				echo 1;
				}
			}
			if($gs->checkPermission($accountID, "actionSentLevel")){
				$starred = $gs->getLevelValue($levelID, "starStars");
				if($starred == 0){
					$difficulty = $gs->getDiffFromStars($stars);
					$gs->sendLevel($accountID, $levelID, $stars, $feature, $difficulty["diff"], $difficulty["auto"]);
					$gs->rateDifficulty($accountID, $levelID, $difficulty["diff"], $difficulty["auto"]);
					$dis->discordNotify(1, $dis->embedContent(2, $dis->title(2), $dis->iconSent($stars, $feature), $dis->embedColor(2), $dis->modBadge($accountID), $dis->footerText($accountID)." | Level ID: $levelID", $levelID, $stars));
					echo 1;
				}
			}
		}
	}
}
echo -1;
?>