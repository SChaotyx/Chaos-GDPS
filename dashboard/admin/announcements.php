<?php
session_start();
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require "../../incl/lib/exploitPatch.php";
require "../../incl/lib/mainLib.php";
require "../../incl/discord/discordLib.php";
    $dis = new discordLib();
$gs = new mainLib();
$ep = new exploitPatch();
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
if($gs->checkPermission($_SESSION["accountID"], "dashboardAdminTools")==false){
	exit($dl->printBox("<h1>NO NO NO</h1><p>This account do not have the permissions to access this tool.</p>"));
}
if($_POST){
    $userName = $ep->remove($_POST["userName"]);
    $password = $ep->remove($_POST["password"]);
    $generatePass = new generatePass();
    $pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
        $query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
        $accountID = $query->fetchColumn();
		if(!$gs->checkPermission($accountID, "toolPackcreate")){
			exit($dl->printBox('<h1>Bot DM Announcements</h1><p>This account do not have the permissions to access this tool. <a href="admin/announcements.php">Try again</a></p>'));
        }
        $query = $db->prepare("SELECT discordID, discordLinkReq FROM accounts");	
        $query->execute();
        $result = $query->fetchAll();
        $total = 0;
        foreach($result as $user) {
            if($user["discordLinkReq"] == 1){
                $dis->discordDMNotify($user["discordID"], $_POST["content"]);
                $total++;
            }
        }
        $dl->printBox("<h1>Bot DM Announcements</h1>$total notified users.<p><a href='admin/announcements.php'>OK</a></p>");
    }else{
		$dl->printBox('<h1>Bot DM Announcements</h1><p>Invalid password or nonexistant account. <a href="admin/announcements.php">Try again</a></p>');
    }
}else{
    $dl->printBox('<h1>Bot DM Announcements</h1>
				<form action="" method="post">
					<div class="">
								<label for="usernameField">Admin Data</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Enter password">
                                <label for="Embed">Embed</label>
                                <textarea rows="10" cols="150"<input type="text" class="form-control" id="content" name="content" placeholder="Json content"></textarea>
                            </div>
                    <label for="sent">Warning: This will notify all users who have linked their Discord account</label>
					<button type="submit" class="btn btn-primary btn-block">Submit</button>
				</form>',"admin");
}