<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
if($_POST["levelID"]){
	$levelID =  $ep->remove($_POST["levelID"]);
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	$query = $db->prepare("SELECT count(*) FROM actions WHERE type=:type AND value=:levelID AND value2=:ip");
	$query->execute([':type' => 50, ':levelID' => $levelID, ':ip' => $ip]);
	if($query->fetchColumn() == 0){
		$query2 = $db->prepare("SELECT count(*) FROM reports WHERE levelID=:levelID");
		$query2->execute([':levelID' => $levelID]);
		if($query2->fetchColumn() == 0){
			$query = $db->prepare("INSERT INTO reports (levelID, count) VALUES (:levelID, :count)");	
			$query->execute([':levelID' => $levelID, ':count' => 1]);
			$query = $db->prepare("INSERT INTO actions (type, value, timestamp, value2) VALUES (:type,:levelID, :time, :ip)");
			$query->execute([':type' => 50, ':levelID' => $levelID, ':time' => time(), ':ip' => $ip]);
		}else{
			$query = $db->prepare("UPDATE reports SET count = count + 1 WHERE levelID=:levelID");	
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO actions (type, value, timestamp, value2) VALUES (:type,:levelID, :time, :ip)");
			$query->execute([':type' => 50, ':levelID' => $levelID, ':time' => time(), ':ip' => $ip]);
		}	
	}
}
?>