<?php
session_start();
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
require "../../incl/lib/XORCipher.php";
$xc = new XORCipher();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
if($gs->checkPermission($_SESSION["accountID"], "dashboardAdminTools")==false){
	exit($dl->printBox("<h1>NO NO NO</h1><p>This account do not have the permissions to access this tool.</p>"));
}
function chkarray($source){
	if($source == ""){
		$target = "0";
	}else{
		$target = $source;
	}
	return $target;
}
if(!empty($_POST["userName"] and $_POST["password"] and $_POST["levelid"])){
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if($pass == 1){
		$query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
		$accountID2 = $query->fetchColumn();
		//checking account permissions
		if($gs->checkPermission($accountID2, "dashboardAdminTools") == false){
			exit ($dl->printBox("<h1>Level Reupload</h1><p>This account do not have the permissions to access this tool. <a href='admin/levelReupload.php'>Try again</a></p>"));
		}
		$levelID = $_POST["levelid"];
		$levelID = preg_replace("/[^0-9]/", '', $levelID);
		$url = $_POST["server"];
		$post = ['gameVersion' => '21', 'binaryVersion' => '33', 'gdw' => '0', 'levelID' => $levelID, 'secret' => 'Wmfd2893gb7', 'inc' => '1', 'extras' => '0'];
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($ch);
		curl_close($ch);
		if($result == "" OR $result == "-1" OR $result == "No no no"){
			if($result==""){
				exit ($dl->printBox("<h1>Level Reupload</h1><p>An error has occured while connecting to the server.<br>Error code: $result <a href='admin/levelReupload.php'>Try again</a></p>"));
			}else if($result=="-1"){
				exit ($dl->printBox("<h1>Level Reupload</h1><p>This level doesn't exist.<br>Error code: $result <a href='admin/levelReupload.php'>Try again</a></p>"));
			}else{
				exit ($dl->printBox("<h1>Level Reupload</h1><p>RobTop doesn't like you or something...<br>Error code: $result <a href='admin/levelReupload.php'>Try again</a></p>"));
			}
		}else{
			$level = explode('#', $result)[0];
			$resultarray = explode(':', $level);
			$levelarray = array();
			$x = 1;
			foreach($resultarray as &$value){
				if ($x % 2 == 0) {
					$levelarray["a$arname"] = $value;
				}else{
					$arname = $value;
				}
				$x++;
			}
			//echo $result;
			$echo = "";
			if($_POST["debug"] == 1){
				$echo = "<br>".$result . "<br>";
				var_dump($levelarray);
			}
			if($levelarray["a4"] == ""){
				exit ($dl->printBox("<h1>Level Reupload</h1><p>$echo An error has occured.<br>Error code: ".htmlspecialchars($result,ENT_QUOTES)."<a href='admin/levelReupload.php'>Try again</a></p>"));
			}
			$uploadDate = time();
			//old levelString
			$levelString = chkarray($levelarray["a4"]);
			$gameVersion = chkarray($levelarray["a13"]);
			if(substr($levelString,0,2) == 'eJ'){
				$levelString = str_replace("_","/",$levelString);
				$levelString = str_replace("-","+",$levelString);
				$levelString = gzuncompress(base64_decode($levelString));
				if($gameVersion > 18){
					$gameVersion = 18;
				}
			}
			//check if exists
			$query = $db->prepare("SELECT count(*) FROM levels WHERE originalReup = :lvl OR original = :lvl");
			$query->execute([':lvl' => $levelarray["a1"]]);
			if($query->fetchColumn() == 0){
				$parsedurl = parse_url($url);
				if($parsedurl["host"] == $_SERVER['SERVER_NAME']){
					exit ($dl->printBox("<h1>Level Reupload</h1><p>$echo You're attempting to reupload from the target server. <a href='admin/levelReupload.php'>Try again</a></p>"));
				}
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$hostname = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$hostname = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}else{
					$hostname = $_SERVER['REMOTE_ADDR'];
				}
				//values
				$twoPlayer = chkarray($levelarray["a31"]);
				$songID = chkarray($levelarray["a35"]);
				$coins = chkarray($levelarray["a37"]);
				$reqstar = chkarray($levelarray["a39"]);
				$extraString = chkarray($levelarray["a36"]);
				$starStars = chkarray($levelarray["a18"]);
				$isLDM = chkarray($levelarray["a40"]);
				$password = chkarray($xc->cipher(base64_decode($levelarray["a27"]),26364));
				$starCoins = 0;
				$starDiff = 0;
				$starDemon = 0;
				$starAuto = 0;
				if($parsedurl["host"] == "www.boomlings.com"){
					if($starStars != 0){
						$starCoins = chkarray($levelarray["a38"]);
						$starDiff = chkarray($levelarray["a9"]);
						$starDemon = chkarray($levelarray["a17"]);
						$starAuto = chkarray($levelarray["a25"]);
					}
				}else{
					$starStars = 0;
				}
				if(empty($_POST["targetuser"])){
					$userID = 0;
					$extID = 0;
					$userNameTarget = "reupload";
				}else{
					$query = $db->prepare("SELECT accountID, userName FROM accounts WHERE userName=:targetuser OR accountID=:targetuser");
					$query->execute([':targetuser' => $_POST["targetuser"]]);
					if($query->rowCount() == 0){
						$extID = 0;
						$userNameTarget = $_POST["targetuser"];
						$query2 = $db->prepare("SELECT userID FROM users WHERE userName=:targetuser");
						$query2->execute([':targetuser' => $_POST["targetuser"]]);
						if($query2->rowCount() == 0){
							$query2 = $db->prepare("INSERT INTO `users` (`isRegistered`, `userID`, `extID`, `userName`, `stars`, `demons`, `icon`, `color1`, `color2`, `iconType`, `coins`, `userCoins`, `special`, `gameVersion`, `secret`, `accIcon`, `accShip`, `accBall`, `accBird`, `accDart`, `accRobot`, `accGlow`, `creatorPoints`, `IP`, `lastPlayed`, `diamonds`, `orbs`, `completedLvls`, `accSpider`, `accExplosion`, `chest1time`, `chest2time`, `chest1count`, `chest2count`, `isBanned`, `isCreatorBanned`) 
															VALUES ('0', NULL, '0', :targetuser, '0', '0', '0', '0', '0', '0', '0', '0', '0', '21', 'Wmfd2893gb7', '0', '0', '0', '0', '0', '0', '0', '0', '186.12.112.160', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
							$query2->execute([':targetuser' => $_POST["targetuser"]]);
							$userID = $db->lastInsertId();
						}else{
							$userID = $query2->fetchColumn();
						}	
					}else{
						$userInfo = $query->fetchAll()[0];
						$extID = $userInfo["accountID"];
						$userNameTarget = $userInfo["userName"];
						$query = $db->prepare("SELECT userID FROM users WHERE extID=:extID");
						$query->execute([':extID' => $extID]);
						if($query->rowCount() == 0){
							$userID = 0;
							$extID = 0;
						}else{
							$userID = $query->fetchColumn();
						}
					}
				}
				//query
				$query = $db->prepare("INSERT INTO levels (levelName, gameVersion, binaryVersion, userName, levelDesc, levelVersion, levelLength, audioTrack, auto, password, original, twoPlayer, songID, objects, coins, requestedStars, extraString, levelString, levelInfo, secret, uploadDate, updateDate, originalReup, userID, extID, unlisted, hostname, starStars, starCoins, starDifficulty, starDemon, starAuto, isLDM)
												VALUES (:name ,:gameVersion, '27', :usertarget, :desc, :version, :length, :audiotrack, '0', :password, :originalReup, :twoPlayer, :songID, '0', :coins, :reqstar, :extraString, :levelString, '0', '0', '$uploadDate', '$uploadDate', :originalReup, :userID, :extID, '0', :hostname, :starStars, :starCoins, :starDifficulty, :starDemon, :starAuto, :isLDM)");
				$query->execute([':password' => $password, ':starDemon' => $starDemon, ':starAuto' => $starAuto, ':gameVersion' => $gameVersion, ':name' => $levelarray["a2"], ':desc' => $levelarray["a3"], ':version' => $levelarray["a5"], ':length' => $levelarray["a15"], ':audiotrack' => $levelarray["a12"], ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':coins' => $coins, ':reqstar' => $reqstar, ':extraString' => $extraString, ':levelString' => "", ':originalReup' => $levelarray["a1"], ':hostname' => $hostname, ':starStars' => $starStars, ':starCoins' => $starCoins, ':starDifficulty' => $starDiff, ':userID' => $userID, ':extID' => $extID, ':isLDM' => $isLDM, ':usertarget' => $userNameTarget]);
				$levelID = $db->lastInsertId();
				file_put_contents("../../data/levels/$levelID",$levelString);
				exit ($dl->printBox("<h1>Level Reupload</h1><p>$echo Level reuploaded, ID: $levelID</p>"));
			}else{
				exit ($dl->printBox("<h1>Level Reupload</h1><p>$echo This level has been already reuploaded</p>"));
			}
		}
	}else{
		//if invalid username or password
		exit ($dl->printBox("<h1>Level Reupload</h1><p>Invalid password or nonexistant account. <a href='admin/levelReupload.php'>Try again</a></p>"));
	}
}else{
	$dl->printBox('<h1>Level Reupload</h1>
				<form action="" method="post">
					<div class="form-group">
						<label for="usernameField">Admin Data</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Enter password">
					</div>
					<div class="form-group">
						<label for="levelIDField">levelID</label>
						<input type="text" class="form-control" id="accountIDField" name="levelid" placeholder="Enter levelID">
					</div>
					<div class="form-group">
						<label for="targetuserField">Target User</label>
						<input type="text" class="form-control" id="targetuserField" name="targetuser" placeholder="Enter Tager userName or userID">
					</div>
					<div class="form-group">
						<label for="serverField">URL (dont change if you dont know what youre doing)</label>
						<input type="text" class="form-control" id="URLField" name="server" value="http://www.boomlings.com/database/downloadGJLevel22.php" placeholder="URL">
					</div>
					<div class="form-group">
						<label for="debugField">debug</label>
						<input type="text" class="form-control" id="debugField" name="debug" value="0" placeholder="debug">
					</div>
					<button type="submit" class="btn btn-primary btn-block">Reupload</button>
				</form>',"admin");
}
?>