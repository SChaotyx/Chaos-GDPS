<?php
session_start();
require "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
include "../../incl/lib/connection.php";
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
					<th>'.$dl->getLocalizedString("levelID").'</th>
					<th>'.$dl->getLocalizedString("reportcount").'</th>
					</tr>';
$query = $db->prepare("SELECT * FROM reports ORDER BY count DESC LIMIT 10 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$report){
	$id = $report["levelID"];
	$count = $report["count"];

	$table .= "<tr><th scope='row'>".$x."</th>
				   <td>".$id."</td>
				   <td>".$count."</td></tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) levelID FROM reports");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 10);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "stats");
?>