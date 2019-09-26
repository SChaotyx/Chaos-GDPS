<?php
session_start();
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
if(!empty($_POST["userName"]) AND !empty($_POST["newusr"]) AND !empty($_POST["password"])){
//f(isset($_POST["userName"]) AND isset($_POST["newusr"]) AND isset($_POST["password"])){
	$userName = $_POST["userName"];
	$newusr = $_POST["newusr"];
	$password = $_POST["password"];
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
		$query = $db->prepare("UPDATE accounts SET username=:newusr WHERE userName=:userName");	
		$query->execute([':newusr' => $newusr, ':userName' => $userName]);
		if($query->rowCount()==0){
			$dl->printBox("<h1>".$dl->getLocalizedString("changeUsername").'</h1>
	                       <p>Invalid password or nonexistant account. <a href="account/changeUsername.php">Try again</a></p>');
		}else{
			$dl->printBox("<h1>".$dl->getLocalizedString("changeUsername").'</h1>
	                       <p>Username changed. <a href="">Please click here to continue.</a></p>');
		}
	}else{
			$dl->printBox("<h1>".$dl->getLocalizedString("changeUsername").'</h1>
	                       <p>Invalid password or nonexistant account. <a href="account/changeUsername.php">Try again</a></p>');
	}
}else{
	$dl->printBox('<h1>'.$dl->getLocalizedString("changeUsername").'</h1>
				<form action="" method="post">
					       <div class="form-group">
								<label for="usernameField">Old Username</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter old username">
							</div>
							<div class="form-group">
								<label for="newusrField">New Username</label>
								<input type="text" class="form-control" id="newusrField" name="newusr" placeholder="Enter new username">
							</div>							
							<div class="form-group">
								<label for="passwordField">Password</label>
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Password">
							</div>
					<button type="submit" class="btn btn-primary btn-block">'.$dl->getLocalizedString("changeUsername").'</button>
				</form>',"account");
/*	$loginbox = '<form action="" method="post">
							<div class="form-group">
								<label for="usernameField">Old Username</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter old username">
							</div>
							<div class="form-group">
								<label for="newusrField">New Username</label>
								<input type="text" class="form-control" id="newusrField" name="newusr" placeholder="Enter new username">
							</div>							
							<div class="form-group">
								<label for="passwordField">Password</label>
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Password">
							</div>';
	if(isset($_SERVER["HTTP_REFERER"])){
		$loginbox .= '<input type="hidden" name="ref" value="'.$_SERVER["HTTP_REFERER"].'">';
	}
	$loginbox .= '<button type="submit" class="btn btn-primary">Change Username</button>
						</form>';
	$dl->printLoginBox2($loginbox); */
}
?>