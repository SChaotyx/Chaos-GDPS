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
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
if($gs->checkPermission($_SESSION["accountID"], "dashboardAdminTools")==false){
	exit($dl->printBox("<h1>NO NO NO</h1><p>This account do not have the permissions to access this tool.</p>"));
}
if(!empty($_POST["userName"] and $_POST["password"] and $_POST["accountID"] and $_POST["newpass"])){
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$accountID = $ep->remove($_POST["accountID"]);
	$newpass = $ep->remove($_POST["newpass"]);
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if($pass == 1){
		$query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
		$accountID2 = $query->fetchColumn();
		//checking account permissions
		if($gs->checkPermission($accountID2, "dashboardAdminTools") == false){
			exit($dl->printBox("<h1>".$dl->getLocalizedString("passrecovery").'</h1><p>This account do not have the permissions to access this tool. <a href="admin/passwordRecovery.php">Try again</a></p>'));
		}
		//checking if numeric accountID
		if(!is_numeric($accountID)){
			exit($dl->printBox("<h1>Password Recovery</h1><p>$accountID isn't a number <a href='admin/passwordRecovery.php'>Try again</a></p>"));
			}
		//checking existing accountID
		$query = $db->prepare("SELECT accountID FROM accounts WHERE accountID=:accountID");	
		$query->execute([':accountID' => $accountID]);
		if($query->rowCount() == 0){
			exit($dl->printBox("<h1>Password Recovery</h1><p>accountID #$accountID doesn't exist. <a href='admin/passwordRecovery.php'>Try again</a></p>"));
		}
		//change password
		$hashpass = password_hash($newpass, PASSWORD_DEFAULT);
		$query = $db->prepare("UPDATE accounts SET password=:password WHERE accountID=:accountID");
		$query->execute([':password' => $hashpass, ':accountID' => $accountID]);
		$dl->printBox("<h1>Success!!!</h1><p><a href='admin/passwordRecovery.php'>Return</a></p>");
	}else{
		//if invalid username or password
		exit ($dl->printBox("<h1>Password Recovery</h1><p>Invalid password or nonexistant account. <a href='admin/passwordRecovery.php'>Try again</a></p>"));
	}
}else{
	$dl->printBox('<h1>Password Recovery</h1>
				<form action="" method="post">
					<div class="form-group">
						<label for="usernameField">Admin Data</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Enter password">
					</div>
					<div class="form-group">
						<label for="accountIDIDField">AccountID</label>
						<input type="text" class="form-control" id="accountIDField" name="accountID" placeholder="Enter accountID">
					</div>
					<div class="form-group">
						<label for="newpassField">New Password</label>
						<input type="text" class="form-control" id="newpassField" name="newpass" placeholder="New Password">
					</div>
					<button type="submit" class="btn btn-primary btn-block">UPDATE</button>
				</form>',"admin");
}
?>