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
	$page = ($_GET["page"] - 1) * 20;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$table = '<table class="table table-inverse">
                        <tr>
						        <th>#</th>
								<th>'.$dl->getLocalizedString("User").'</th>
								<th>'.$dl->getLocalizedString("ID").'</th>
								</tr>';

$query = $db->prepare("SELECT userID,userName FROM users WHERE isBanned = 1 ORDER BY userID DESC LIMIT 20 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$action){
	//detecting userID
	$userID = $action["userID"];
	//detecting NameSong
    $name = $action["userName"];
	$table .= "<tr><th scope='row'>".$x."</th>
	                    <td>".$name."</td>
						<td>".$userID."</td>
						</tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) userID, userName FROM users WHERE isBanned = 1");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 20);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "mod");
?>