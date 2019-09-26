<?php
session_start();
require "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
include "../../incl/lib/connection.php";
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 100;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$table = '<table class="table table-inverse">
                        <tr>
						        <th>'.$dl->getLocalizedString("ID").'</th>
								<th>'.$dl->getLocalizedString("User").'</th>
								<th>'.$dl->getLocalizedString("Rank").'</th>
								<th>'.$dl->getLocalizedString("lastSeen").'</th>
								</tr>';

$query = $db->prepare("SELECT * FROM roleassign WHERE roleID != 1 ORDER BY roleID DESC LIMIT 100 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$action){
	//detecting User
	$accountID = $action["accountID"];
	$accountID = $gs->getAccountName($accountID);
	//detecting action
	$rank = $dl->getLocalizedString("rank".$action["roleID"]);
	if($action["roleID"] == 2 OR $action["roleID"] == 3 OR $action["roleID"] == 4){
	}
	if($action["roleID"] == 5 OR $action["roleID"] == 6){
		$value = "";
	}
	if($action["roleID"] == 13){
		$value = base64_decode($value);
	}
	$query = $db->prepare("SELECT userName, lastPlayed FROM users WHERE extID = :id");
	$query->execute([':id' => $action["accountID"]]);
	$result2 = $query->fetchAll();
	$row2 = 0;
 foreach($result2 as &$mod2){
	$time = $mod2["lastPlayed"];
	$time = $gs->timeElapsed2($time);
 }
	$table .= "<tr>
	                    <td>".$action["accountID"]."</td>
	                    <td>".$mod2["userName"]."</td>
						<td>".$rank."</td>
						<td>".$time."</td>
						</tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) FROM roleassign WHERE roleID != 1");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 100);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "browse");
?>