<?php
include "../../incl/lib/connection.php";
include __DIR__ . "/../../config/discord.php";
include "../../incl/discord/emojis.php";
require_once "../../incl/discord/discordLib.php";
$dis = new discordLib();
$stars = 190 + 20; //190 total stars from RobTop Levels | +20 max tolerable ban
$usercoins = 0 + 3;  //+3 user coins tolerable ban
$secretcoins = 66; //66 total secret coins on levels and the vaults //removed map packs secret coins
$demons = 3 + 2; //3 total demons by RobTop | +2 max tolerable ban
//calculate stars from rated levels
$query = $db->prepare("SELECT starStars, coins, starCoins, starDemon FROM levels");
$query->execute();
$levelstuff = $query->fetchAll();
foreach($levelstuff as $level){
	$stars = $stars + $level["starStars"];
	if($level["starCoins"] != 0){
		$usercoins += $level["coins"];
	}
	if($level["starDemon"] != 0){
		$demons++;
	}
}
//calculate stars from daily/weekly levels
$query = $db->prepare("SELECT levelID, type FROM dailyfeatures");
$query->execute();
$leveldaily = $query->fetchAll();
foreach($leveldaily as $daily){
	$levelIDdaily = $daily["levelID"];
	$query = $db->prepare("SELECT starStars, coins, starCoins, starDemon FROM levels WHERE levelID = :levelIDdaily");
	$query->execute([':levelIDdaily' => $levelIDdaily]);
	$dailycalculate = $query->fetchAll();
	foreach($dailycalculate as $dailystars){
		$stars += $dailystars["starStars"];
		if($dailystars["starCoins"] != 0){
			$usercoins += $dailystars["coins"];
		}
		if($dailystars["starDemon"] != 0){
			$demons++;
		}
	}
}
//calculate stars from gauntlets
$query = $db->prepare("SELECT level1, level2, level3, level4, level5 FROM gauntlets");
$query->execute();
$levelgauntlet = $query->fetchAll();
foreach($levelgauntlet as $gauntlet){
	for($x = 1; $x < 6; $x++){
		$query = $db->prepare("SELECT starStars, coins, starCoins, starDemon FROM levels WHERE levelID = :levelIDgauntlet");
		$query->execute([':levelIDgauntlet' => $gauntlet["level".$x]]);
		$gauntletcalculate = $query->fetchAll();
		foreach($gauntletcalculate as $gauntletstars){
			$stars += $gauntletstars["starStars"];
			if($gauntletstars["starCoins"] != 0){
				$usercoins += $gauntletstars["coins"];
			}
			if($gauntletstars["starDemon"] != 0){
				$demons++;
			}
		}
	}	
}
//calculate stars from mappacks
$query = $db->prepare("SELECT stars, coins FROM mappacks");
$query->execute();
$result = $query->fetchAll();
foreach($result as $pack){
	$stars += $pack["stars"];
	$secretcoins += $pack["coins"];
}
//BAN USERS
$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE stars > :stars AND coins > :secretcoins AND userCoins > :usercoins AND demons > :demons");
$query->execute([':stars' => $stars, ':secretcoins' => $secretcoins, ':usercoins' => $usercoins, ':demons' => $demons]);
//DISCORD NOTIFY
$starsMax = $stars - 20;
$usercMax = $usercoins - 3;
$demonsMax = $demons - 2;
//accounts
$query = $db->prepare("SELECT count(*) FROM accounts");
$query->execute();
$totalaccounts = $query->fetchColumn();
$timeago = time() - 86400;
$query = $db->prepare("SELECT count(*) FROM users WHERE lastPlayed > :lastPlayed");
$query->execute([':lastPlayed' => $timeago]);
$activeusers = $query->fetchColumn();
$query = $db->prepare("SELECT count(*) FROM levels");
$query->execute();
$levelcount = $query->fetchColumn();
$query = $db->prepare("SELECT count(*) FROM levels WHERE starStars != 0");
$query->execute();
$ratedlevelcount = $query->fetchColumn();
//content message
$info = "These are the maximum leaderboard stats to date";
$gdpsstats = "$icon_star $starsMax | $icon_diamond ??? | $icon_secretcoin $secretcoins | $icon_verifycoins $usercMax | $icon_demon $demonsMax";
$bar = "───────────────────";
$gdpsinfo = "
__Levels__
**Total levels:** $levelcount
**Total rated levels:** $ratedlevelcount
__Accounts__
**Registered accounts:** $totalaccounts
**Active users:** $activeusers";
$boticon = "misc/auto.png";
$botinfo = "Chaos-Bot";
$thumbnail = "misc/gdps.png";
//BUILD JSON
$data = array(
				'embed'=> [
					"title"=> $dis->title(25),
				    "fields"=> [
						["name"=> $info, "value"=> $gdpsstats],
						["name"=> $bar, "value"=> $gdpsinfo]],					
					"color"=> $dis->embedColor(7),
					"footer"=> ["icon_url"=> ($iconhost.$boticon), "text"=> $botinfo],
					"thumbnail"=> ["url"=> ($iconhost.$thumbnail)],
				]);
$data_string = json_encode($data);
$dis->discordNotify(1, $data_string);
echo "done!";
?>