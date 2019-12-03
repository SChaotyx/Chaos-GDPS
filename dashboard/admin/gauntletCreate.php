<?php
session_start();
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require "../../incl/lib/exploitPatch.php";
require "../../incl/lib/mainLib.php";
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
if(!empty($_POST["userName"]) AND !empty($_POST["password"]) AND !empty($_POST["gName"]) AND !empty($_POST["level1"]) AND !empty($_POST["level2"]) AND !empty($_POST["level3"]) AND !empty($_POST["level4"]) AND !empty($_POST["level5"])){
    function levelExist($levelID){
        include "../../incl/lib/connection.php";
        $dl = new dashboardLib();
        $query = $db->prepare("SELECT levelName FROM levels WHERE levelID=:levelID");	
        $query->execute([':levelID' => $levelID]);
        if($query->rowCount() == 0){
            exit($dl->printBox("<h1>Gauntlet Create</h1><p>Level #$levelID doesn't exist. <a href='admin/gauntletCreate.php'>Try again</a></p>"));
        }else{
            return $query->fetchColumn();
        }
    }
    $userName = $ep->remove($_POST["userName"]);
    $password = $ep->remove($_POST["password"]);
    $gName = $ep->remove($_POST["gName"]);
    $level1 = $ep->remove($_POST["level1"]);
    $level2 = $ep->remove($_POST["level2"]);
    $level3 = $ep->remove($_POST["level3"]);
    $level4 = $ep->remove($_POST["level4"]);
    $level5 = $ep->remove($_POST["level5"]);
    $generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
        $query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
        $accountID = $query->fetchColumn();
		if(!$gs->checkPermission($accountID, "toolPackcreate")){
			exit($dl->printBox('<h1>Gauntlet Create</h1><p>This account do not have the permissions to access this tool. <a href="admin/gauntletCreate.php">Try again</a></p>'));
        }
        $level1Name = levelExist($level1);
        $level2Name = levelExist($level2);
        $level3Name = levelExist($level3);
        $level4Name = levelExist($level4);
        $level5Name = levelExist($level5);
        switch($gName){
            case 'fire': $gID = 1; break;
            case 'ice': $gID = 2; break;
            case 'poison': $gID = 3; break;
            case 'shadow': $gID = 4; break;
            case 'lava': $gID = 5; break;
            case 'bonus': $gID = 6; break;
            case 'chaos': $gID = 7; break;
            case 'demon': $gID = 8; break;
            case 'time': $gID = 9; break;
            case 'crystal': $gID = 10; break;
            case 'magic': $gID = 11; break;
            case 'spike': $gID = 12; break;
            case 'monster': $gID = 13; break;
            case 'doom': $gID = 14; break;
            case 'death': $gID = 15; break;
            default:
			exit($dl->printBox('<h1>Gauntlet Create</h1><p>'.$gName.' is invalid. <a href="admin/gauntletCreate.php">Try again</a></p>'));
            break;

        }
        $query = $db->prepare("SELECT ID FROM gauntlets WHERE ID=:gID");	
        $query->execute([':gID' => $gID]);
        if($query->rowCount() != 0){
			exit($dl->printBox('<h1>Gauntlet Create</h1><p>'.$gName.' gauntlet already exist. <a href="admin/gauntletCreate.php">Try again</a></p>'));
        }
        $query = $db->prepare("INSERT INTO gauntlets (ID, level1, level2, level3, level4, level5) VALUES (:gID, :level1, :level2, :level3, :level4, :level5)");
        $query->execute([':gID'=>$gID, ':level1'=>$level1, ':level2'=>$level2, ':level3'=>$level3, ':level4'=>$level4, ':level5'=>$level5]);
        $dl->printBox("<h1>".$gs->getGauntletName($gID)." Gauntlet</h1>
                        Level1: $level1Name ($level1) <br>
                        Level2: $level2Name ($level2) <br>
                        Level3: $level3Name ($level3) <br>
                        Level4: $level4Name ($level4) <br>
                        Level5: $level5Name ($level5) <br>");
    }else{
		$dl->printBox('<h1>Gauntlet Create</h1><p>Invalid password or nonexistant account. <a href="admin/gauntletCreate.php">Try again</a></p>');
    }
}else{
    $dl->printBox('<h1>Gauntlet Create</h1>
		        <script src="incl/jscolor/jscolor.js"></script>
				<form action="" method="post">
					<div class="form-group">
								<label for="usernameField">Admin Data</label>
								<input type="text" class="form-control" id="usernameField" name="userName" placeholder="Enter username">
								<input type="password" class="form-control" id="passwordField" name="password" placeholder="Enter password">
                                <label for="gNameField">Gauntlet Name</label>
                                <input type="text" class="form-control" id="gNameField" name="gName" placeholder="Enter Gauntlet Name">
                                <label for="levelsField">Levels</label>
                                <input type="text" class="form-control" id="level1Field" name="level1" placeholder="Enter Level 1 ID">
                                <input type="text" class="form-control" id="level2Field" name="level2" placeholder="Enter Level 2 ID">
                                <input type="text" class="form-control" id="level3Field" name="level3" placeholder="Enter Level 3 ID">
                                <input type="text" class="form-control" id="level4Field" name="level4" placeholder="Enter Level 4 ID">
                                <input type="text" class="form-control" id="level5Field" name="level5" placeholder="Enter Level 5 ID">
							</div>
					<button type="submit" class="btn btn-primary btn-block">Create Gauntlet</button>
				</form>',"mod");
}
?>