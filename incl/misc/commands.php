<?php
class Commands {
	public function ownCommand($comment, $command, $accountID, $targetExtID){
		require_once "../lib/mainLib.php";
		$gs = new mainLib();
		$commandInComment = strtolower("!".$command);
		$commandInPerms = ucfirst(strtolower($command));
		$commandlength = strlen($commandInComment);
		if(substr($comment,0,$commandlength) == $commandInComment AND (($gs->checkPermission($accountID, "adminCommands") OR ($targetExtID == $accountID AND $gs->checkPermission($accountID, "ownCommands"))))){
			return true;
		}
		return false;
	}
	public function doCommands($accountID, $comment, $levelID) {
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		require_once "../discord/discordLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		$dis = new discordLib();
		$commentarray = explode(' ', $comment);
		$uploadDate = time();
		//LEVELINFO
		$query2 = $db->prepare("SELECT extID FROM levels WHERE levelID = :id");
		$query2->execute([':id' => $levelID]);
		$targetExtID = $query2->fetchColumn();
		
		
		//ELDER COMMANDS//
		
		if(substr($comment,0,9) == '!updatecp' AND $gs->checkPermission($accountID, "elderCommands")){
			$gs->updatecp($levelID);
		return true;
		}
		if(substr($comment,0,7) == '!unrate' AND $gs->checkPermission($accountID, "elderCommands")){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starFeatured='0', starEpic='0', starStars='0', starCoins='0', starDemon='0', starDemonDiff='0', issend='0', israted='0', sendcount='0', sendtime='0', sendstars='0', sendrate='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			//update creator point
			$gs->updatecp($levelID);
			//insert action into mod actions
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('16', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			//discord notify
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(3), $dis->diffthumbnail($levelID), $dis->embedColor(3), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
			return true;
		}
		if(substr($comment,0,8) == '!played' AND $gs->checkPermission($accountID, "elderCommands")){
			$query = $db->prepare("UPDATE levels SET israted='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(4), $dis->diffthumbnail($levelID), $dis->embedColor(5), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
		return true;
		}
		if(substr($comment,0,8) == '!feature' AND $gs->checkPermission($accountID, "elderCommands")){
			$starred = $gs->getLevelValue($levelID, "starStars");
			if($starred > 0){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starFeatured='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			//update creator point
			$gs->updatecp($levelID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			$discordString = $dis->embedContent(2, $dis->title(5), $dis->diffthumbnail($levelID), $dis->embedColor(1), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0);
			$dis->discordNotify(1, $discordString);
			//$dis->discordDMNotify(1, $levelID, $discordString);
			return true;
			}
		}
		if(substr($comment,0,8) == '!unfeat' AND $gs->checkPermission($accountID, "elderCommands")){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starFeatured='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("UPDATE levels SET starEpic='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			//update creator point
			$gs->updatecp($levelID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(6), $dis->diffthumbnail($levelID), $dis->embedColor(4), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
			return true;
		}
		if(substr($comment,0,5) == '!epic' AND $gs->checkPermission($accountID, "elderCommands")){
			$starred = $gs->getLevelValue($levelID, "starStars");
			if($starred > 0){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starEpic='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("UPDATE levels SET starFeatured='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			//update creator point
			$gs->updatecp($levelID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('4', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(7), $dis->diffthumbnail($levelID), $dis->embedColor(1), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
			return true;
			}
		}
		if(substr($comment,0,7) == '!unepic' AND $gs->checkPermission($accountID, "elderCommands")){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starEpic='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			//update creator point
			$gs->updatecp($levelID);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('4', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(8), $dis->diffthumbnail($levelID), $dis->embedColor(4), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
				return true;
		}
		if(substr($comment,0,12) == '!verifycoins' AND $gs->checkPermission($accountID, "elderCommands")){
			$starred = $gs->getLevelValue($levelID, "starStars");
			if($starred > 0){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starCoins='1' WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('3', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(9), $dis->diffthumbnail($levelID), $dis->embedColor(2), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
			return true;
			}
		}
		if(substr($comment,0,14) == '!unverifycoins' AND $gs->checkPermission($accountID, "elderCommands")){
			$timerated = time() - $gs->getLevelValue($levelID, "rateDate");
			if(86400 > $timerated OR $gs->checkPermission($accountID, "adminCommands")){
			}else{return false;}
			$query = $db->prepare("UPDATE levels SET starCoins='0' WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('3', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(10), $dis->diffthumbnail($levelID), $dis->embedColor(4), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
			return true;
		}
		//MUTE v1.1
		if(substr($comment,0,5) == '!mute' AND $gs->checkPermission($accountID, "headCommands")){
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
		//UNMUTE v1.0
		if(substr($comment,0,7) == '!unmute' AND $gs->checkPermission($accountID, "headCommands")){
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
		
		
		//ADMIN COMMANDS//
		
		if(substr($comment,0,6) == '!daily' AND $gs->checkPermission($accountID, "commandDaily")){
			/*
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 0");
				$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return false;
			}
			*/
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 0 ORDER BY timestamp DESC LIMIT 1");
			$query->execute([':tomorrow' => strtotime("tomorrow 00:00:00")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("tomorrow 00:00:00");
			}else{
				$timestamp = $query->fetchColumn() + 86400;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 0)");
				$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('5', :value, :levelID, :timestamp, :id, :dailytime, 0)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
			$dis->discordNotify(1, $dis->embedContent(4, $dis->title(11), $dis->diffthumbnail($levelID), $dis->embedColor(6), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, date("d/m/Y", $timestamp - 1)));
			return true;
		}
		if(substr($comment,0,7) == '!weekly' AND $gs->checkPermission($accountID, "commandDaily")){
			/*
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 1");
			$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return false;
			}
			*/
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 1 ORDER BY timestamp DESC LIMIT 1");
				$query->execute([':tomorrow' => strtotime("next monday")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("next monday");
			}else{
				$timestamp = $query->fetchColumn() + 604800;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 1)");
			$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('5', :value, :levelID, :timestamp, :id, :dailytime, 1)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
			$dis->discordNotify(1, $dis->embedContent(4, $dis->title(12), $dis->diffthumbnail($levelID), $dis->embedColor(6), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, date("d/m/Y", $timestamp - 1)));
			return true;
		}
		if(substr($comment,0,6) == '!delet' AND $gs->checkPermission($accountID, "adminCommands")){
			if(!is_numeric($levelID)){
				return false;
			}
			$dis->discordNotify(1, $dis->embedContent(2, $dis->title(13), $dis->thumbnail(11), $dis->embedColor(3), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, 0));
			$query = $db->prepare("DELETE from levels WHERE levelID=:levelID LIMIT 1");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('6', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			if(file_exists(dirname(__FILE__)."../../data/levels/$levelID")){
				rename(dirname(__FILE__)."../../data/levels/$levelID",dirname(__FILE__)."../../data/levels/deleted/$levelID");
			}
			return true;
		}
		if(substr($comment,0,7) == '!setacc' AND $gs->checkPermission($accountID, "adminCommands")){
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName OR accountID = :userName LIMIT 1");
			$query->execute([':userName' => $commentarray[1]]);
			if($query->rowCount() == 0){
				return false;
			}
			$targetAcc = $query->fetchColumn();
			//var_dump($result);
			$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID LIMIT 1");
			$query->execute([':extID' => $targetAcc]);
			$userID = $query->fetchColumn();
			$dis->discordNotify(1, $dis->embedContent(5, $dis->title(14), $dis->thumbnail(12), $dis->embedColor(6), $dis->modBadge($accountID), $dis->footerText($accountID), $levelID, $commentarray[1]));
			$query = $db->prepare("UPDATE levels SET extID=:extID, userID=:userID, userName=:userName WHERE levelID=:levelID");
			$query->execute([':extID' => $targetAcc["accountID"], ':userID' => $userID, ':userName' => $commentarray[1], ':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('7', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1], ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if(substr($comment,0,7) == '!length' AND $gs->checkPermission($accountID, "adminCommands")){
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
			}
			$query = $db->prepare("UPDATE levels SET levelLength=:setlength WHERE levelID=:levelID");
			$query->execute([':setlength' => $setlength, ':levelID' => $levelID]);
			return true;
		}
		
		
	//PUBLIC COMMANDS//
	
	    $query = $db->prepare("SELECT starStars FROM levels WHERE levelID = :levelID");
		$query->execute([':levelID' => $levelID]);
		$starStars = $query->fetchColumn();
	
		if($this->ownCommand($comment, "rename", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
			$name = $ep->remove(str_replace("!rename ", "", $comment));
			$query = $db->prepare("UPDATE levels SET levelName=:levelName WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID, ':levelName' => $name]);
			$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('8', :value, :timestamp, :id, :levelID)");
			$query->execute([':value' => $name, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "pass", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
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
		if($this->ownCommand($comment, "song", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
			$song = $ep->remove(str_replace("!song ", "", $comment));
			if(is_numeric($song)){
				$query = $db->prepare("UPDATE levels SET songID=:song WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID, ':song' => $song]);
				$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('16', :value, :timestamp, :id, :levelID)");
				$query->execute([':value' => $song, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return true;
			}
		}
		if($this->ownCommand($comment, "description", $accountID, $targetExtID)){
			$desc = base64_encode($ep->remove(str_replace("!description ", "", $comment)));
			$query = $db->prepare("UPDATE levels SET levelDesc=:desc WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID, ':desc' => $desc]);
			$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('13', :value, :timestamp, :id, :levelID)");
			$query->execute([':value' => $desc, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "public", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
			$query = $db->prepare("UPDATE levels SET unlisted='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "unlist", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
			$query = $db->prepare("UPDATE levels SET unlisted='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "ldm", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
			$query = $db->prepare("UPDATE levels SET isLDM='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('14', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "unldm", $accountID, $targetExtID) AND (($starStars == 0) OR ($gs->checkPermission($accountID, "adminCommands")))){
			$query = $db->prepare("UPDATE levels SET isLDM='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('14', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		return false;
	}
	public function doProfileCommands($accountID, $command){
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		require_once "../discord/discordLib.php";
		$dis = new discordLib();
		$ep = new exploitPatch();
		$gs = new mainLib();
		$commentarray = explode(' ', $command);
		if(substr($command, 0, 9) == '!updatecp'){
			$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID");
			$query->execute([':extID' => $accountID]);
			$userID = $query->fetchColumn();
			$query = $db->prepare("SELECT levelID FROM levels WHERE userID = :userID LIMIT 1");
			$query->execute([':userID' => $userID]);
			$levelID = $query->fetchColumn();
			$gs->updatecp($levelID);
			return true;
		}
		if(substr($command, 0, 8) == '!setrank' AND $gs->checkPermission($accountID, "devCommands")){
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :commentarray OR accountID = :commentarray LIMIT 1");
			$query->execute([':commentarray' => $commentarray[1]]);
			if($query->rowCount() == 0){
				return false;
			}
			$targetAccID = $query->fetchColumn();
			switch($commentarray[2]){
			//Demote
			case "demote": $roleID = 1;
			break;
			//Moderador
			case "mod": $roleID = 2;
			break;
			//Elder
			case "elder": $roleID = 3;
			break;
			//Head
			case "head": $roleID = 4;
			break;
			//Admin
			case "admin": $roleID = 5;
			break;
			//Developer
			case "dev": $roleID = 6;
			break;
			//Owner
			case "owner": $roleID = 7;
			break;
		}
		//new check correct rolename
		if($roleID==0){
			return false;
		}	
		//checking if the user already has rank
		$query = $db->prepare("SELECT accountID FROM roleassign WHERE accountID=:accountID");
		$query->execute([':accountID' => $targetAccID]);
		if($query->rowCount() == 0){
			$titleID = 16;
			//if not, insert new roleassign
			$query = $db->prepare("INSERT INTO roleassign (roleID, accountID) VALUES (:roleID, :accountID)");
			$query->execute([':roleID' => $roleID, ':accountID' => $targetAccID]);
		}else{
			$query = $db->prepare("SELECT roleID FROM roleassign WHERE accountID=:accountID");
			$query->execute([':accountID' => $targetAccID]);
			$readyrank = $query->fetchColumn();
			//if already on rank?
			if($roleID==$readyrank){
				return false;
			}
			//if rank degraded?
			if($readyrank < $roleID){
				$titleID = 16;
			}else{
				$titleID = 26;
			}
			//if yes, update roleID
			$query = $db->prepare("UPDATE roleassign SET roleID=:roleID WHERE accountID=:accountID");
			$query->execute([':roleID' => $roleID, ':accountID' => $targetAccID]);
		}
		if($roleID==1){
			$dis->discordNotify(1, $dis->accEmbedContent(1, $dis->title(17), $dis->iconProfile($targetAccID), $dis->embedColor(7), $dis->modBadge($accountID), $dis->footerText($accountID), $targetAccID, 0));
		}else{
			$dis->discordNotify(1, $dis->accEmbedContent(1, $dis->title($titleID), $dis->iconProfile($targetAccID), $dis->embedColor(7), $dis->modBadge($accountID), $dis->footerText($accountID), $targetAccID, 0));
		}
		return true;
		}
		//LEADERBAORD BAN COMMAND
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
		return false;
	}
}
?>