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
if(!empty($_POST["userName"])){
	$userName = $ep->remove($_POST["userName"]);
	// Revisando que el usuario exista
	$query = $db->prepare("SELECT userName FROM users WHERE userName=:userNamecheck");	
	$query->execute([':userNamecheck' => $userName]);
	if($query->rowCount() == 0){
	exit($dl->printBox("<h1>".$dl->getLocalizedString("UserInfo")."</h1>
	                       <p>$userName doesn't exist. <a href='browse/browseUser.php'>Try again</a></p>"));
				}
	// Recolectando datos
	$query2 = $db->prepare("SELECT userID, userName FROM users WHERE userName=:userName");
    $query2->execute([':userName' => $userName]);
    $result = $query2->fetchAll();
	$query3 = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");
    $query3->execute([':userName' => $userName]);
    $result2 = $query3->fetchAll();
	foreach($result as &$action){
		}
	foreach($result2 as &$action2){
		}
	$username = $action["userName"];
	$userid = $action["userID"];
	$accountid = $action2["accountID"];
    //  Mostrando resultado	
	$dl->printBox("<h1>$username</h1>
							User ID: $userid<br>
							Account ID: $accountid<br>
													");
}else{
		$dl->printBox('<h1>'.$dl->getLocalizedString("Browse User").'</h1>
				<form action="" method="post">
					<div class="form-group">
								<label for="usernameField">Username</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
							</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("Browse").'</button>
				</form>',"browse");
}
?>