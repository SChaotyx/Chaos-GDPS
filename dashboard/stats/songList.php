<?php
session_start();
require "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
include "../../incl/lib/connection.php";
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
								<th>'.$dl->getLocalizedString("songid").'</th>
								<th>'.$dl->getLocalizedString("Name").'</th>
								</tr>';

$query = $db->prepare("SELECT ID,name FROM songs WHERE ID >= 5000000 ORDER BY ID DESC LIMIT 20 OFFSET $page");
$query->execute();
$result = $query->fetchAll();
$x = $page + 1;
foreach($result as &$action){
	//detecting SongID
	$songID = $action["ID"];
	//detecting NameSong
    $name = $action["name"];
	$table .= "<tr><th scope='row'>".$x."</th>
	                    <td>".$songID."</td>
						<td>".$name."</td>
						</tr>";
	$x++;
}
$table .= "</table>";
/*
	bottom row
*/
//getting count
$query = $db->prepare("SELECT count(*) FROM songs WHERE ID >= 5000000 ");
$query->execute();
$packcount = $query->fetchColumn();
$pagecount = ceil($packcount / 20);
$bottomrow = $dl->generateBottomRow($pagecount, $actualpage);
$dl->printPage($table . $bottomrow, true, "stats");
?>