<?php
class Commands {
	//----------------
	//----------------
	//LEVEL COMMENTS COMMANDS
	//----------------
	//----------------
	public function doCommands($accountID, $comment, $levelID) {
		include dirname(__FILE__)."/../lib/connection.php";
		require_once dirname(__FILE__)."/../lib/exploitPatch.php";
		require_once dirname(__FILE__)."/../lib/mainLib.php";
		require_once dirname(__FILE__)."/../discord/discordLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		$dis = new discordLib();
		$commentarray = explode(' ', $comment);
		$uploadDate = time();
		//LEVELINFO
		$query = $db->prepare("SELECT userID, extID, starStars, rateDate, levelLength, original FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $levelID]);
		$result = $query->fetchAll();
		if ($query->rowCount() == 0) { return false; }
		foreach($result as $lvl){
			$lvlUserID = $lvl["userID"];
			$lvlExtID = $lvl["extID"];
			$lvlstars = $lvl["starStars"];
			$lvlLength = $lvl["levelLength"];
			$lvlRateDate = $lvl["rateDate"];
			$lvlOriginal = $lvl["original"];
			$timerated = time() - $lvlRateDate;
			if($lvlRateDate == 0){ $timerated = 0;}
		}
		//----------------
		//----------------
		//ELDER COMMANDS
		//----------------
		//----------------
		if($gs->checkPermission($accountID, "elderCommands")){
			if(substr($comment,0,9) == '!updatecp'){
				$gs->updatecp(0, $lvlUserID);
				return true;
			}
			if(substr($comment,0,8) == '!played'){
				if($lvlstars != 0){ return false; }
				$query = $db->prepare("UPDATE levels SET israted='1' WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID]);
				$dis->discordNotifyNew(1, $levelID, 1, 2, 4, 5, $accountID, 1, 0, 0);
				return true;
			}
			if($lvlstars > 0 AND $lvlLength > 1 AND $timerated < 86400 OR $gs->checkPermission($accountID, "adminCommands")){
				if(substr($comment,0,7) == '!unrate'){
					$query = $db->prepare("UPDATE levels SET starFeatured='0', starEpic='0', starStars='0', starCoins='0', starDemon='0', starDemonDiff='0', issend='0', israted='0', sendcount='0', sendtime='0', sendstars='0', sendrate='0', cpCount='0' WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID]);
					$gs->updatecp(0, $lvlUserID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('16', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 3, 3, $accountID, 1, 0, 0);
					return true;
				}
				if(substr($comment,0,8) == '!feature' OR substr($comment,0,5) == '!feat'){
					if($lvlLength == 2 OR $lvlOriginal == 1){ $cpCount = 1; }else{ $cpCount = 2; }
					$query = $db->prepare("UPDATE levels SET starFeatured='1', cpCount=:cpCount WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID, ':cpCount' => $cpCount]);
					$gs->updatecp(0, $lvlUserID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 5, 1, $accountID, 1, 0, 0);
					return true;
				}
				if(substr($comment,0,8) == '!unfeat'){
					$query = $db->prepare("UPDATE levels SET starFeatured='0', starEpic='0', cpCount='1' WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID]);
					$gs->updatecp(0, $lvlUserID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 6, 4, $accountID, 1, 0, 0);
					return true;
				}
				if(substr($comment,0,5) == '!epic'){
					if($lvlLength == 2 OR $lvlOriginal == 1){ $cpCount = 1; }else{ $cpCount = 3; }
					$query = $db->prepare("UPDATE levels SET starEpic='1', starFeatured='1', cpCount=:cpCount WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID, ':cpCount' => $cpCount]);
					$gs->updatecp(0, $lvlUserID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('4', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 7, 1, $accountID, 1, 0, 0);
					return true;
				}
				if(substr($comment,0,7) == '!unepic'){
					if($lvlLength == 2 OR $lvlOriginal == 1){ $cpCount = 1; }else{ $cpCount = 2; }
					$query = $db->prepare("UPDATE levels SET starEpic='0', cpCount=:cpCount WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID, ':cpCount' => $cpCount]);
					$gs->updatecp(0, $lvlUserID);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('4', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 8, 4, $accountID, 1, 0, 0);
					return true;
				}
				if(substr($comment,0,12) == '!verifycoins'){
					$query = $db->prepare("UPDATE levels SET starCoins='1' WHERE levelID = :levelID");
					$query->execute([':levelID' => $levelID]);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('3', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 9, 2, $accountID, 1, 0, 0);
					return true;
				}
				if(substr($comment,0,14) == '!unverifycoins'){
					$query = $db->prepare("UPDATE levels SET starCoins='0' WHERE levelID = :levelID");
					$query->execute([':levelID' => $levelID]);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('3', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					$dis->discordNotifyNew(1, $levelID, 1, 2, 10, 4, $accountID, 1, 0, 0);
					return true;
				}
			}
		}
		//----------------
		//----------------
		//HEAD COMMANDS
		//----------------
		//----------------
		if($gs->checkPermission($accountID, "headCommands")){
			if(substr($comment,0,6) == '!daily' OR substr($comment,0,7) == '!weekly'){
				if($lvlstars == 0){ return false; }
				if(substr($comment,0,6) == '!daily'){ $type = 0; $title = 11; }
				if(substr($comment,0,7) == '!weekly'){ 
					if($lvlstars != 10){ return false; }
					$type = 1; $title = 12; 
				}
				$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = :type");
				$query->execute([':level' => $levelID, ':type' => $type]);
				if($query->fetchColumn() != 0){
					return false;
				}
				$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = :type ORDER BY timestamp DESC LIMIT 1");
				$query->execute([':tomorrow' => strtotime("tomorrow 00:00:00"), ':type' => $type]);
				if($query->rowCount() == 0){
					$timestamp = strtotime("tomorrow 00:00:00");
				}else{
					$timestamp = $query->fetchColumn() + 86400;
				}
				$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, :type)");
				$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp, ':type' => $type]);
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('5', :value, :levelID, :timestamp, :id, :dailytime, 0)");
				$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
				$date = date("d/m/Y", $timestamp - 1);
				$dis->discordNotifyNew(1, $levelID, 1, 4, $title, 6, $accountID, 1, 0, $date);
				return true;
			}
			if(substr($comment,0,5) == '!mute'){
				$query = $db->prepare("SELECT userID FROM users WHERE userName = :userName OR userID = :userName LIMIT 1");
				$query->execute([':userName' => $commentarray[1]]);
				if($query->rowCount() == 0){
					return false;
				}
				$userID = $query->fetchColumn();
				if(!empty($commentarray[2])){
					$mutetime = 60 * 60 * 24 * $commentarray[2];
					$timestamp = $uploadDate + $mutetime;
				}else{
					$timestamp = 0;
				}
				$query = $db->prepare("SELECT * FROM restrictions WHERE restrictiontype=:restrictiontype AND userID=:userID LIMIT 1");
				$query->execute([':restrictiontype' => 1, ':userID' => $userID]);
				if($query->rowCount() == 0){
					$query = $db->prepare("INSERT INTO restrictions (userID, timestamp, restrictiontype) VALUES (:userID, :timestamp, '1')");
					$query->execute([':userID' => $userID, ':timestamp' => $timestamp]);
				}else{
					$query = $db->prepare("UPDATE restrictions SET timestamp=:timestamp WHERE userID=:userID");
					$query->execute([':userID' => $userID, ':timestamp' => $timestamp]);
				}
				return true;
			}
			if(substr($comment,0,7) == '!unmute'){
				$query = $db->prepare("SELECT userID FROM users WHERE userName = :userName OR userID = :userName LIMIT 1");
				$query->execute([':userName' => $commentarray[1]]);
				if($query->rowCount() == 0){
					return false;
				}
				$userID = $query->fetchColumn();
				$query = $db->prepare("DELETE from restrictions WHERE userID=:userID AND restrictiontype = 1");
				$query->execute([':userID' => $userID]);
				return true;
			}
		}
		//----------------
		//----------------
		//ADMIN COMMANDS
		//----------------
		//----------------
		if($gs->checkPermission($accountID, "adminCommands")){
			if(substr($comment,0,6) == '!delet'){
				if(!is_numeric($levelID)){
					return false;
				}
				$dis->discordNotifyNew(1, $levelID, 1, 2, 13, 3, $accountID, 2, 11, 0);
				$query = $db->prepare("DELETE from levels WHERE levelID=:levelID LIMIT 1");
				$query->execute([':levelID' => $levelID]);
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('6', :value, :levelID, :timestamp, :id)");
				$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				if(file_exists(dirname(__FILE__)."../../data/levels/$levelID")){
					rename(dirname(__FILE__)."../../data/levels/$levelID",dirname(__FILE__)."../../data/levels/deleted/$levelID");
				}
				return true;
			}
			if(substr($comment,0,7) == '!setacc'){
				$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName OR accountID = :userName LIMIT 1");
				$query->execute([':userName' => $commentarray[1]]);
				if($query->rowCount() == 0){
					return false;
				}
				$targetAcc = $query->fetchColumn();
				$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID LIMIT 1");
				$query->execute([':extID' => $targetAcc]);
				$userID = $query->fetchColumn();
				$dis->discordNotifyNew(1, $levelID, 1, 5, 14, 6, $accountID, 2, 12, $commentarray[1]);
				$query = $db->prepare("UPDATE levels SET extID=:extID, userID=:userID, userName=:userName WHERE levelID=:levelID");
				$query->execute([':extID' => $targetAcc, ':userID' => $userID, ':userName' => $commentarray[1], ':levelID' => $levelID]);
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('7', :value, :levelID, :timestamp, :id)");
				$query->execute([':value' => $commentarray[1], ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				if($lvlstars > 0){ $gs->updatecp(0, $lvlUserID); $gs->updatecp(0, $userID); }
				return true;
			}
			if(substr($comment,0,7) == '!length'){
				if(empty($commentarray[1])){ return false; }
				switch($commentarray[1]){
				case "tiny": $setlength = 0;
				break;
				case "short": $setlength = 1;
				break;
				case "medium": $setlength = 2;
				break;
				case "long": $setlength = 3;
				break;
				case "xl": $setlength = 4;
				break;
				default: return false;
				break;
				}
				$query = $db->prepare("UPDATE levels SET levelLength=:setlength WHERE levelID=:levelID");
				$query->execute([':setlength' => $setlength, ':levelID' => $levelID]);
				return true;
			}
			if(substr($comment,0,3) == '!cp'){
				$cpCount = $commentarray[1];
				if(!is_numeric($cpCount)){ return false; }
				if(empty($cpCount)){ return false; }
				$query = $db->prepare("UPDATE levels SET cpCount = :cpValue WHERE levelID=:levelID");
				$query->execute([':cpValue' => $cpCount, ':levelID' => $levelID]);
				$gs->updatecp(0, $lvlUserID);
				return true;
			}
		}
		//----------------
		//----------------
		//PUBLIC COMMANDS
		//----------------
		//----------------
		if($gs->checkPermission($accountID, "ownCommands") AND $lvlExtID == $accountID OR $gs->checkPermission($accountID, "adminCommands")){
			if($lvlstars == 0 OR  $gs->checkPermission($accountID, "adminCommands")){
				if(substr($comment,0,7) == '!rename'){
					$name = $ep->remove(str_replace("!rename ", "", $comment));
					$query = $db->prepare("UPDATE levels SET levelName=:levelName WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID, ':levelName' => $name]);
					$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('8', :value, :timestamp, :id, :levelID)");
					$query->execute([':value' => $name, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					return true;
				}
				if(substr($comment,0,7) == '!public'){
					$query = $db->prepare("UPDATE levels SET unlisted='0' WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID]);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					return true;
				}
				if(substr($comment,0,7) == '!unlist'){
					$query = $db->prepare("UPDATE levels SET unlisted='1' WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID]);
					$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
					$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					return true;
				}
			}
			if(substr($comment,0,5) == '!pass'){
				$pass = $ep->remove(str_replace("!pass ", "", $comment));
				if(is_numeric($pass)){
					$pass = sprintf("%06d", $pass);
					if($pass == "000000"){
						$pass = "";
					}
					$pass = "1".$pass;
					$query = $db->prepare("UPDATE levels SET password=:password WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID, ':password' => $pass]);
					$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('9', :value, :timestamp, :id, :levelID)");
					$query->execute([':value' => $pass, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					return true;
				}
			}
			if(substr($comment,0,5) == '!song'){
				$song = $ep->remove(str_replace("!song ", "", $comment));
				if(is_numeric($song)){
					$query = $db->prepare("UPDATE levels SET songID=:song WHERE levelID=:levelID");
					$query->execute([':levelID' => $levelID, ':song' => $song]);
					$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('16', :value, :timestamp, :id, :levelID)");
					$query->execute([':value' => $song, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
					return true;
				}
			}
			if(substr($comment,0,12) == '!description'){
				$desc = base64_encode($ep->remove(str_replace("!description ", "", $comment)));
				$query = $db->prepare("UPDATE levels SET levelDesc=:desc WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID, ':desc' => $desc]);
				$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('13', :value, :timestamp, :id, :levelID)");
				$query->execute([':value' => $desc, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return true;
			}
			if(substr($comment,0,4) == '!ldm'){
				$query = $db->prepare("UPDATE levels SET isLDM='1' WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID]);
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('14', :value, :levelID, :timestamp, :id)");
				$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return true;
			}
			if(substr($comment,0,6) == '!unldm'){
				$query = $db->prepare("UPDATE levels SET isLDM='0' WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID]);
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('14', :value, :levelID, :timestamp, :id)");
				$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return true;
			}
		}
		return false;
	}
	//----------------
	//----------------
	//ACCOUNT COMMENTS COMMANDS
	//----------------
	//----------------
	public function doProfileCommands($accountID, $userID, $command){
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		require_once "../discord/discordLib.php";
		require_once "../lib/XORCipher.php";
		$xc = new XORCipher();
		$dis = new discordLib();
		$ep = new exploitPatch();
		$gs = new mainLib();
		$commentarray = explode(' ', $command);
		//----------------
		//----------------
		//PUBLIC COMMANDS
		//----------------
		//----------------
		if(substr($command, 0, 9) == '!updatecp'){
			$gs->updatecp(0, $userID);
			return true;
		}
		if(substr($command, 0, 8) == '!confirm'){
			$code = $commentarray[1];
			$query = $db->prepare("SELECT discordLinkReq, discordID FROM accounts WHERE accountID = :accountID");
			$query->execute([':accountID' => $accountID]);
			$result = $query->fetchAll();
			foreach($result as $userdata){
				$linkReq = $userdata["discordLinkReq"];
				$discordID = $userdata["discordID"];
			}
			if($linkReq === 1){
				return false;
			}
			if($linkReq === 0){
				return false;
			}
			if($linkReq === $code){
				$query = $db->prepare("UPDATE accounts SET discordLinkReq = :discordLinkReq WHERE accountID = :accountID");
				$query->execute([':discordLinkReq' => 1, ':accountID' => $accountID]);
				$dis->discordDMNotify($discordID, $dis->accEmbedContent(3, $dis->title(27), $dis->iconProfile($accountID), $dis->embedColor(7), $dis->modBadge($accountID), $dis->footerText($accountID), $accountID, 0));
				return true;
			}
			return false;
		}
		//----------------
		//----------------
		//HEAD COMMANDS
		//----------------
		//----------------
		if(substr($command, 0, 12) == '!leaderboard' AND $gs->checkPermission($accountID, "headCommands")){
			$cmdaction = 10;
			switch($commentarray[1]){
				case "ban": $cmdaction = 1;
				break;
				case "unban": $cmdaction = 0;
				break;
			}
			if($cmdaction==10){
				return false;
			}else{
				$query = $db->prepare("SELECT * FROM users WHERE userName = :userName LIMIT 1");
				$query->execute([':userName' => $commentarray[2]]);
				$result = $query->fetchAll();
				foreach($result as $userdata){
					$userName = $userdata["userName"];
					$userID = $userdata["userID"];
				}
				if($query->rowCount() == 0){
					return false;
				}else{
					$query = $db->prepare("UPDATE users SET isBanned = :cmdaction WHERE userName = :userName LIMIT 1");
					$query->execute([':userName' => $userName, ':cmdaction' => $cmdaction]);
					$query = $db->prepare("INSERT INTO modactions  (type, value, value2, timestamp, account) VALUES ('15',:userID, :cmdaction,  :timestamp,:account)");
					$query->execute([':userID' => $userID, ':cmdaction' => $commentarray[1], ':timestamp' => time(), ':account' => $accountID]);
					return true;
				}
			}
		}
		//----------------
		//----------------
		//DEV COMMANDS
		//----------------
		//----------------
		if($gs->checkPermission($accountID, "devCommands")){
			if(substr($command, 0, 8) == '!setrank'){
				$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :commentarray OR accountID = :commentarray LIMIT 1");
				$query->execute([':commentarray' => $commentarray[1]]);
				if($query->rowCount() == 0){
					return false;
				}
				$targetAccID = $query->fetchColumn();
				switch($commentarray[2]){
					case "demote": $roleID = 1; break;
					case "mod": $roleID = 2; break;
					case "elder": $roleID = 3; break;
					case "head": $roleID = 4; break;
					case "admin": $roleID = 5; break;
					case "dev": $roleID = 6; break;
					case "owner": $roleID = 7; break;
				}
				if($roleID==0){
					return false;
				}
				$query = $db->prepare("SELECT accountID FROM roleassign WHERE accountID=:accountID");
				$query->execute([':accountID' => $targetAccID]);
				if($query->rowCount() == 0){
					$titleID = 16;
					$query = $db->prepare("INSERT INTO roleassign (roleID, accountID) VALUES (:roleID, :accountID)");
					$query->execute([':roleID' => $roleID, ':accountID' => $targetAccID]);
				}else{
					$query = $db->prepare("SELECT roleID FROM roleassign WHERE accountID=:accountID");
					$query->execute([':accountID' => $targetAccID]);
					$readyrank = $query->fetchColumn();
					if($roleID==$readyrank){
						return false;
					}
					if($readyrank < $roleID){
						$titleID = 16;
					}else{
						$titleID = 26;
					}
					$query = $db->prepare("UPDATE roleassign SET roleID=:roleID WHERE accountID=:accountID");
					$query->execute([':roleID' => $roleID, ':accountID' => $targetAccID]);
				}
				if($roleID==1){
					$dis->discordNotifyNew(1, $targetAccID, 2, 1, 17, 7, $accountID, 0, 0, 0);
				}else{
					$dis->discordNotifyNew(1, $targetAccID, 2, 1, $titleID, 7, $accountID, 0, 0, 0);
				}
				return true;
			}
			if(substr($command, 0, 9) == '!reupload'){
				function chkarray($source){
					if($source == ""){
						$target = "0";
					}else{
						$target = $source;
					}
					return $target;
				}
				$levelID = $commentarray[1];
				$levelID = preg_replace("/[^0-9]/", '', $levelID);
				$url = "http://www.boomlings.com/database/downloadGJLevel22.php";
				$post = ['gameVersion' => '21', 'binaryVersion' => '33', 'gdw' => '0', 'levelID' => $levelID, 'secret' => 'Wmfd2893gb7', 'inc' => '1', 'extras' => '0'];
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				$result = curl_exec($ch);
				curl_close($ch);
				if($result == "" OR $result == "-1" OR $result == "No no no"){
					if($result==""){
						return false;
					}else if($result=="-1"){
						return false;
					}else{
						return false;
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
					if($levelarray["a4"] == ""){
						return false;
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
							return false;
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
						if(empty($commentarray[2])){
							return false;
						}else{
							$query = $db->prepare("SELECT accountID, userName FROM accounts WHERE userName=:targetuser");
							$query->execute([':targetuser' => $commentarray[2]]);
							if($query->rowCount() == 0){
								$extID = 0;
								$userNameTarget = $commentarray[2];
								$query2 = $db->prepare("SELECT userID FROM users WHERE userName=:targetuser");
								$query2->execute([':targetuser' => $commentarray[2]]);
								if($query2->rowCount() == 0){
									$query2 = $db->prepare("INSERT INTO `users` (`isRegistered`, `userID`, `extID`, `userName`, `stars`, `demons`, `icon`, `color1`, `color2`, `iconType`, `coins`, `userCoins`, `special`, `gameVersion`, `secret`, `accIcon`, `accShip`, `accBall`, `accBird`, `accDart`, `accRobot`, `accGlow`, `creatorPoints`, `IP`, `lastPlayed`, `diamonds`, `orbs`, `completedLvls`, `accSpider`, `accExplosion`, `chest1time`, `chest2time`, `chest1count`, `chest2count`, `isBanned`, `isCreatorBanned`) 
																	VALUES ('0', NULL, '0', :targetuser, '0', '0', '0', '0', '0', '0', '0', '0', '0', '21', 'Wmfd2893gb7', '0', '0', '0', '0', '0', '0', '0', '0', '186.12.112.160', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
									$query2->execute([':targetuser' => $commentarray[2]]);
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
														VALUES (:name ,:gameVersion, '27', :usertarget, :desc, :version, :length, :audiotrack, '0', :password, '1', :twoPlayer, :songID, '0', :coins, :reqstar, :extraString, :levelString, '0', '0', '$uploadDate', '$uploadDate', :originalReup, :userID, :extID, '0', :hostname, :starStars, :starCoins, :starDifficulty, :starDemon, :starAuto, :isLDM)");
						$query->execute([':password' => $password, ':starDemon' => $starDemon, ':starAuto' => $starAuto, ':gameVersion' => $gameVersion, ':name' => $levelarray["a2"], ':desc' => $levelarray["a3"], ':version' => $levelarray["a5"], ':length' => $levelarray["a15"], ':audiotrack' => $levelarray["a12"], ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':coins' => $coins, ':reqstar' => $reqstar, ':extraString' => $extraString, ':levelString' => "", ':originalReup' => $levelarray["a1"], ':hostname' => $hostname, ':starStars' => $starStars, ':starCoins' => $starCoins, ':starDifficulty' => $starDiff, ':userID' => $userID, ':extID' => $extID, ':isLDM' => $isLDM, ':usertarget' => $userNameTarget]);
						$levelID = $db->lastInsertId();
						file_put_contents("../../data/levels/$levelID",$levelString);
						return true;
					}else{
						return false;
					}
				}
			}
		}
		return false;
	}
}
?>