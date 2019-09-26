<?php
session_start();
require "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
require "../../incl/lib/mainLib.php";
$gs = new mainLib();
include "../../incl/lib/connection.php";
if(isset($_GET["page"]) AND is_numeric($_GET["page"]) AND $_GET["page"] > 0){
	$page = ($_GET["page"] - 1) * 50;
	$actualpage = $_GET["page"];
}else{
	$page = 0;
	$actualpage = 1;
}
$table = '<table class="table table-inverse">
                        <tr>
						        <th>#</th>
								<th>'.$dl->getLocalizedString("ID").'</th>
								<th>'.$dl->getLocalizedString("User").'</th>
								<th>'.$dl->getLocalizedString("stars").'</th>
								</tr>';

$starsgain = array();
$time = time() - 86400;
$x = 1;
$query = $db->prepare("SELECT * FROM actions WHERE type = '9' AND timestamp > :time");
$query->execute([':time' => $time]);
$result = $query->fetchAll();
foreach($result as &$gain){
	if(!empty($starsgain[$gain["account"]])){
		$starsgain[$gain["account"]] += $gain["value"];
	}else{
		$starsgain[$gain["account"]] = $gain["value"];
	}
}
arsort($starsgain);
foreach ($starsgain as $userID => $stars){
	$query = $db->prepare("SELECT userName, isBanned FROM users WHERE userID = :userID");
	$query->execute([':userID' => $userID]);
	$userinfo = $query->fetchAll()[0];
	$username = htmlspecialchars($userinfo["userName"], ENT_QUOTES);
	if($userinfo["isBanned"] == 0){
		$table .= "<tr><th scope='row'>".$x."</th>
	                    <td>".$userID."</td>
						<td>".$username."</td>
						<td>".$stars."</td>
						</tr>";
	$x++;
}
}
$table .= "</table>";
$dl->printPage($table, true, "stats");
?>