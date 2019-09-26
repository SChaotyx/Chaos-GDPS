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
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 40;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$table = '<table class="table table-inverse">
			<thead>
				<tr>
					<th>'.$dl->getLocalizedString("comment").'</th>
					<th>'.$dl->getLocalizedString("likes").'</th>
					<th>'.$dl->getLocalizedString("timeago").'</th>
					<th>'.$dl->getLocalizedString("levelID").'</th>
				</tr>
			</thead>
			<tbody>';
$query = $db->prepare("SELECT userID, userName FROM users WHERE extID=:extID");
$query->execute([":extID" => $_SESSION["accountID"]]);
$result = $query->fetchAll();
foreach($result as &$get1){
	
$query = $db->prepare("SELECT * FROM comments WHERE userID=:userID ORDER BY timestamp DESC LIMIT 40 OFFSET $page");
$query->execute([":userID" => $get1["userID"]]);
$result = $query->fetchAll();
foreach($result as &$comment){
	
	$actualcomment = $comment["comment"];
	$actualcomment = base64_decode($actualcomment);
	$commentdate = $comment["timestamp"] ;
	$commentdate = $gs->timeElapsed($commentdate);
	
	$table .= "<tr>
				<td>".$actualcomment."</td>
				<td>".$comment["likes"]."</td>
				<td>".$commentdate."</td>
                <td>".$comment["levelID"]."</td>
			</tr>";
}
}
$table .= "</tbody></table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) FROM comments WHERE userID=:userID");
$query->execute([':userID' => $get1["userID"]]);
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 40);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "account");