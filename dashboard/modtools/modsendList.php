<?php
session_start();
require "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
include "../../incl/lib/connection.php";
if(!isset($_SESSION["accountID"]) OR $_SESSION["accountID"] == 0){
	header("Location: ../login/login.php");
	exit();
}
if($gs->checkPermission($_SESSION["accountID"], "dashboardModTools")==false){
	exit($dl->printBox("<h1>NO NO NO</h1><p>This account do not have the permissions to access this tool.</p>"));
}
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 10;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$table = '<table class="table table-inverse">
              <tr>
			        <th>#</th>
					<th>'.$dl->getLocalizedString("levelName").'</th>
					<th>'.$dl->getLocalizedString("userName").'</th>
					<th>'.$dl->getLocalizedString("sendstars").'</th>
					<th>'.$dl->getLocalizedString("sendrate").'</th>
					<th>'.$dl->getLocalizedString("levelID").'</th>
					<th>'.$dl->getLocalizedString("sendcount").'</th>
					<th>'.$dl->getLocalizedString("time").'</th>
					</tr>';
$query = $db->prepare("SELECT levelName, userName, sendstars, sendrate, levelID, israted, sendcount, sendtime, issend, starStars FROM levels WHERE issend = 1 AND israted = 0 AND starStars = 0 ORDER BY sendcount DESC LIMIT 10 OFFSET $page");
$query->execute([':time' => time()]);
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$action){
	$levelName= $action["levelName"];
	$userName= $action["userName"];
	$sendcount= $action["sendcount"];
	$sendstars = $action["sendstars"];
	$sendrate = $action["sendrate"];
	$levelID = $action["levelID"];
//REPLACE STRINGS
       //Request Stars Strings
$sendstars2 = $dl->getLocalizedString("starsreq".$action["sendstars"]);
	if($action["sendstars"] == 2 OR $action["sendstars"] == 3 OR $action["sendstars"] == 4){
		if($action["sendstars"] == 1){
			$value = "True";
		}else{
			$value = "False";
		}
	}
	if($action["sendstars"] == 5 OR $action["sendstars"] == 6){
		$value = "";
	}
	if($action["sendstars"] == 13){
		$value = base64_decode($value);
	}
       //Request Rate Strings
$sendrate2 = $dl->getLocalizedString("featft".$action["sendrate"]);
	if($action["sendrate"] == 2 OR $action["sendrate"] == 3 OR $action["sendrate"] == 4){
		if($action["sendrate"] == 1){
			$value = "True";
		}else{
			$value = "False";
		}
	}
	if($action["sendrate"] == 5 OR $action["sendrate"] == 6){
		$value = "";
	}
	if($action["sendrate"] == 13){
		$value = base64_decode($value);
	}
	$time = date("d/m/Y G:i:s", $action["sendtime"]);
	if($action["sendrate"] > time()){
		$value3 = "future";
	}
	$table .= "<tr><th scope='row'>".$x."</th>
				   <td>".$levelName."</td>
				   <td>".$userName."</td>
				   <td>".$sendstars2."</td>
				   <td>".$sendrate2."</td>
				   <td>".$levelID."</td>
				   <td>".$sendcount."</td>
				   <td>".$time."</td></tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) FROM levels WHERE issend = 1 AND israted = 0 AND starStars = 0");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "mod");
?>