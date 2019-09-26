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
if($gs->checkPermission($_SESSION["accountID"], "dashboardAdminTools")==false){
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
								<th>'.$dl->getLocalizedString("User").'</th>
								<th>'.$dl->getLocalizedString("Restriction").'</th>
								<th>'.$dl->getLocalizedString("rtime").'</th>
								</tr>';

$query = $db->prepare("SELECT * FROM restrictions ORDER BY ID DESC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$action){
	//detecting User
	$userID = $action["userID"];
	$userID = $gs->getUserName($userID);
	//detecting action
	$restriction = $dl->getLocalizedString("restrictiontype".$action["restrictiontype"]);
	if ($action["timestamp"]==0){
		$timerestriction = "Undefined";
	}else
	if($action["timestamp"] > time()){
		$time = $action["timestamp"] - time();
		$time2 = time() - $time; 
		$timerestriction = $gs->timeElapsed($time2);
	}else{
		$timerestriction = "Expired";
	}
	$table .= "<tr><th scope='row'>".$x."</th>
	                    <td>".$userID."</td>
						<td>".$restriction."</td>
						<td>".$timerestriction."</td>
						</tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) FROM restrictions");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "admin");
?>