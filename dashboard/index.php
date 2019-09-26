<?php
session_start();
require "incl/dashboardLib.php";
$dl = new dashboardLib();
require "../incl/lib/connection.php";

$chartdata = array();
for($x = 7; $x >= 0;){
	$timeBefore = time() - (86400 * $x);
	$timeAfter = time() - (86400 * ($x + 1));
	$query = $db->prepare("SELECT count(*) FROM levels WHERE uploadDate < :timeBefore AND uploadDate > :timeAfter AND userID != 388");
	$query->execute([':timeBefore' => $timeBefore, ':timeAfter' => $timeAfter]);
	switch($x){
		case 1:
			$identifier = $x . " day ago";
			break;
		case 0:
			$identifier = "Last 24 hours";
			break;
		default:
			$identifier = $x . " days ago";
			break;
	}
	$chartdata[$identifier] = $query->fetchColumn();
	$x--;
}

$levelsChart2 = array();
$months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
$x = 0;
foreach($months as &$month){
	$x++;
	$nextMonthYear = date('Y');
	if($x == 12){
		$x = 0;
		$nextMonthYear++;
	}
	$nextMonth = $months[$x];
	$timeBefore = strtotime("first day of $month ".date('Y'));
	$timeAfter = strtotime("first day of $nextMonth ".$nextMonthYear);
	$query = $db->prepare("SELECT count(*) FROM levels WHERE uploadDate > :timeBefore AND uploadDate < :timeAfter AND userID != 388");
	$query->execute([':timeBefore' => $timeBefore, ':timeAfter' => $timeAfter]);
	$amount = $query->fetchColumn();
	if($amount != 0){
		$levelsChart2[$month] = $amount;
	}
}
$query = $db->prepare("SELECT count(*) FROM accounts");
$query->execute();
$totalaccounts = $query->fetchColumn();
$timeago = time() - 86400;
$query = $db->prepare("SELECT count(*) FROM users WHERE lastPlayed > :lastPlayed");
$query->execute([':lastPlayed' => $timeago]);
$activeusers = $query->fetchColumn();

$dl->printPage('<p>Welcome to the Geometry Dash Chaos Dashboard. Please choose a tool above.
				<br>------------------------------------
				<br>Registered accounts: '.$totalaccounts.'.
				<br>Active users: '.$activeusers.'.
				<br>------------------------------------
				<br>Cvolton GDPS Source <a href="https://github.com/Cvolton/GMDprivateServer">Here</a>.
					<div class="chart-container" style="position: relative; height:30vh; width:80vw">
						<canvas id="levelsChart"></canvas>
					</div>
				<br>
					<div class="chart-container" style="position: relative; height:30vh; width:80vw">
						<canvas id="levelsChart2"></canvas>
					</div>
				<br>
				</p><iframe src="https://discordapp.com/widget?id=427525423102951446&theme=dark" width="500" height="350" allowtransparency="true" frameborder="0"></iframe>' . $dl->generateLineChart("levelsChart","Levels Uploaded",$chartdata) . $dl->generateLineChart("levelsChart2","Levels Uploaded",$levelsChart2), false);
?>