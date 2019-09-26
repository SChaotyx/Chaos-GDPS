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
								<th>'.$dl->getLocalizedString("mod").'</th>
								<th>'.$dl->getLocalizedString("action").'</th>
								<th>'.$dl->getLocalizedString("levelName").'</th>
								<th>'.$dl->getLocalizedString("author").'</th>
								<th>'.$dl->getLocalizedString("level").'</th>
								<th>'.$dl->getLocalizedString("time").'</th>
								</tr>';

$query = $db->prepare("SELECT * FROM modactions WHERE type = 100 ORDER BY ID DESC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$action){
	//detecting mod
	$account = $action["account"];
	$account = $gs->getAccountName($account);
	//detecting level
	$lvlid = $action["value3"];
	$levelname = $action["value3"];
	$levelname = $gs->getLevelName($lvlid);
	$creator = $action["value3"];
	$creator = $gs->getAuthor($lvlid);
	//detecting action
	if($action["type"] == 5){
		if(is_numeric($value2)){
			$value2 = date("d/m/Y G:i:s", $value2);
		}
	}
	$actionname = $dl->getLocalizedString("modAction".$action["type"]);
	$time = $action["timestamp"] ;
	$time = $gs->timeElapsed($time);
	//$time = date("d/m/Y G:i:s", $action["timestamp"]);
	if($action["type"] == 5 AND $action["value2"] > time()){
		$value3 = "future";
	}
	$table .= "<tr><th scope='row'>".$x."</th>
	                    <td>".$account."</td>
						<td>".$actionname."</td>
						<td>".$levelname."</td>
						<td>".$creator."</td>
						<td>".$lvlid."</td>
						<td>".$time."</td>
						</tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) FROM modactions WHERE type = 100");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "browse");
?>