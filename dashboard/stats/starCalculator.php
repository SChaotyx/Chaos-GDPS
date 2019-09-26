<?php
include "../incl/lib/connection.php";
$stars = 190 + 20; //190 total stars from RobTop Levels | +20 max tolerable ban
$usercoins = 0 + 3;  //+3 user coins tolerable ban
$secretcoins = 66; //66 total secret coins on levels and the vaults
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
echo "
total stars: $stars
<br>total secret coins: $secretcoins
<br>total user coins: $usercoins
<br>total demons: $demons
";
?>