<?php
session_start();
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../../incl/lib/mainLib.php";
$gs = new mainLib();
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
if($gs->checkPermission($_SESSION["accountID"], "dashboardModTools")==false){
	exit($dl->printBox("<h1>NO NO NO</h1><p>This account do not have the permissions to access this tool.</p>"));
}
if(!empty($_POST["userName"]) AND !empty($_POST["password"]) AND !empty($_POST["userID"])){
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$userID = $ep->remove($_POST["userID"]);
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
		$query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
		$accountID = $query->fetchColumn();
		if($gs->checkPermission($accountID, "toolLeaderboardsban")){
			if(!is_numeric($userID)){
				exit($dl->printBox("<h1>".$dl->getLocalizedString("banuser").'</h1>
	                       <p>Invalid UserID <a href="modtools/leaderboardsBan.php">Try again</a></p>'));
			}
			$query = $db->prepare("UPDATE users SET isBanned = 1 WHERE userID = :id");
			$query->execute([':id' => $userID]);
			if($query->rowCount() != 0){
				//echo "Banned succesfully.";
				$dl->printBox("<h1>".$dl->getLocalizedString("banuser").'</h1>
	                       <p>Banned succesfully. <a href="">Please click here to continue.</a></p>');
			}else{
				//echo "Ban failed.";
			$dl->printBox("<h1>".$dl->getLocalizedString("banuser").'</h1>
	                       <p>Ban failed <a href="modtools/leaderboardsBan.php">Try again</a></p>');
			}
			$query = $db->prepare("INSERT INTO modactions  (type, value, value2, timestamp, account) 
													VALUES ('15',:userID, '1',  :timestamp,:account)");
			$query->execute([':userID' => $userID, ':timestamp' => time(), ':account' => $accountID]);
		}else{
			//exit("You do not have the permission to do this action. <a href='leaderboardsBan.php'>Try again</a
			exit($dl->printBox("<h1>".$dl->getLocalizedString("banuser").'</h1>
	                       <p>You do not have the permission to do this action. <a href="modtools/leaderboardsBan.php">Try again</a></p>'));
		}
	}else{
		//echo "Invalid password or nonexistant account. <a href='leaderboardsBan.php'>Try again</a>";
		$dl->printBox("<h1>".$dl->getLocalizedString("banuser").'</h1>
	                       <p>Invalid password or nonexistant account. <a href="modtools/leaderboardsBan.php">Try again</a></p>');
	}
}else{
		$dl->printBox('<h1>'.$dl->getLocalizedString("banuser").'</h1>
				<form action="" method="post">
					<div class="form-group">
								<label for="usernameField">Username</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
							</div>
							<div class="form-group">
								<label for="newusrField">Password</label>
								<input type="password" class="form-control" id="newusrField" name="password" placeholder="Password">
							</div>							
							<div class="form-group">
								<label for="passwordField">UserID</label>
								<input type="text" class="form-control" id="userIDField" name="userID" placeholder="Enter userID">
							</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("banuser").'</button>
				</form>',"mod");
}
?>