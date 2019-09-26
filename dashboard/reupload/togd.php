<?php
session_start();
include "../../incl/lib/connection.php";
require "../incl/dashboardLib.php";
$dl = new dashboardLib();
function chkarray($source){
	if($source == ""){
		$target = "0";
	}else{
		$target = $source;
	}
	return $target;
}
//error_reporting(0);
require "../../incl/lib/XORCipher.php";
$xc = new XORCipher();
require_once "../../incl/lib/generatePass.php";
$generatePass = new generatePass();
require_once "../../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../../incl/lib/generateHash.php";
$gh = new generateHash();
if(!empty($_POST["userhere"]) AND !empty($_POST["passhere"]) AND !empty($_POST["usertarg"]) AND !empty($_POST["passtarg"]) AND !empty($_POST["levelID"])){
	$userhere = $ep->remove($_POST["userhere"]);
	$passhere = $ep->remove($_POST["passhere"]);
	$usertarg = $ep->remove($_POST["usertarg"]);
	$passtarg = $ep->remove($_POST["passtarg"]);
	$levelID = $ep->remove($_POST["levelID"]);
	$pass = $generatePass->isValidUsrname($userhere, $passhere);
	if ($pass != 1) { //verifying if valid local usr
	exit($dl->printBox("<h1>Level to GD</h1>
	                       <p>Wrong local username/password combination <a href='reupload/togd.php'>Try again</a></p>"));
		//exit("Wrong local username/password combination");
	}
	$query = $db->prepare("SELECT * FROM levels WHERE levelID = :level");
	$query->execute([':level' => $levelID]);
	$levelInfo = $query->fetch();
	$userID = $levelInfo["userID"];
	$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :user");
	$query->execute([':user' => $userhere]);
	$accountID = $query->fetchColumn();
	$query = $db->prepare("SELECT userID FROM users WHERE extID = :ext");
	$query->execute([':ext' => $accountID]);
	if($query->fetchColumn() != $userID){ //verifying if lvl owned
	    exit($dl->printBox("<h1>Level to GD</h1>
	                       <p>This level doesn't belong to the account you're trying to reupload from <a href='reupload/togd.php'>Try again</a></p>"));
		//exit("This level doesn't belong to the account you're trying to reupload from");
	}
	$udid = "S" . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(111111111,999999999) . mt_rand(1,9); //getting accountid
	$sid = mt_rand(111111111,999999999) . mt_rand(11111111,99999999);
	//echo $udid;
	$post = ['userName' => $usertarg, 'udid' => $udid, 'password' => $passtarg, 'sID' => $sid, 'secret' => 'Wmfv3899gc9'];
	$ch = curl_init($server . "http://www.boomlings.com/database/accounts/loginGJAccount.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	curl_close($ch);
	if($result == "" OR $result == "-1" OR $result == "No no no"){
		if($result==""){
			$dl->printBox("<h1>Level to GD</h1>
	                       <p>An error has occured while connecting to the login server. <a href='reupload/togd.php'>Try again</a></p>");
			//echo "An error has occured while connecting to the login server.";
		}else if($result=="-1"){
			$dl->printBox("<h1>Level to GD</h1>
	                       <p>Login to the target server failed. <a href='reupload/togd.php'>Try again</a></p>");
			//echo "Login to the target server failed.";
		}else{
			$dl->printBox("<h1>Level to GD</h1>
	                       <p>RobTop doesn't like you or something... <a href='reupload/togd.php'>Try again</a></p>");
			//echo "RobTop doesn't like you or something...";
		}
		exit($dl->printBox("<h1>Level to GD</h1>
	                       <p>Error code: $result <a href='reupload/togd.php'>Try again</a></p>"));
		//exit("<br>Error code: $result");
	}
	if(!is_numeric($levelID)){ //checking if lvlid is numeric cuz exploits
	    exit($dl->printBox("<h1>Level to GD</h1>
	                       <p>Invalid levelID <a href='reupload/togd.php'>Try again</a></p>"));
		//exit("Invalid levelID");
	}
	$levelString = file_get_contents("../../data/levels/$levelID"); //generating seed2
	$seed2 = base64_encode($xc->cipher($gh->genSeed2noXor($levelString),41274));
	$accountID = explode(",",$result)[0]; //and finally reuploading
	$gjp = base64_encode($xc->cipher($passtarg,37526));
	$post = ['gameVersion' => $levelInfo["gameVersion"], 
	'binaryVersion' => $levelInfo["binaryVersion"], 
	'gdw' => "0", 
	'accountID' => $accountID, 
	'gjp' => $gjp,
	'userName' => $usertarg,
	'levelID' => "0",
	'levelName' => $levelInfo["levelName"],
	'levelDesc' => $levelInfo["levelDesc"],
	'levelVersion' => $levelInfo["levelVersion"],
	'levelLength' => $levelInfo["levelLength"],
	'audioTrack' => $levelInfo["audioTrack"],
	'auto' => $levelInfo["auto"],
	'password' => $levelInfo["password"],
	'original' => "0",
	'twoPlayer' => $levelInfo["twoPlayer"],
	'songID' => $levelInfo["songID"],
	'objects' => $levelInfo["objects"],
	'coins' => $levelInfo["coins"],
	'requestedStars' => $levelInfo["requestedStars"],
	'unlisted' => "0",
	'wt' => "0",
	'wt2' => "3",
	'extraString' => $levelInfo["extraString"],
	'seed' => "v2R5VPi53f",
	'seed2' => $seed2,
	'levelString' => $levelString,
	'levelInfo' => $levelInfo["levelInfo"],
	'secret' => "Wmfd2893gb7"];
	if($_POST["debug"] == 1){
		var_dump($post);
	}
	$ch = curl_init("http://www.boomlings.com/database/uploadGJLevel21.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	curl_close($ch);
	if($result == "" OR $result == "-1" OR $result == "No no no"){
		if($result==""){
			$dl->printBox("<h1>Level to GD</h1>
	                       <p>An error has occured while connecting to the upload server. <a href='reupload/togd.php'>Try again</a></p>");
			//echo "An error has occured while connecting to the upload server.";
		}else if($result=="-1"){
			$dl->printBox("<h1>Level to GD</h1>
	                       <p>Reuploading level failed. <a href='reupload/togd.php'>Try again</a></p>");
			//echo "Reuploading level failed.";
		}else{
			$dl->printBox("<h1>Level to GD</h1>
	                       <p>RobTop doesn't like you or something... (upload) <a href='reupload/togd.php'>Try again</a></p>");
			//echo "RobTop doesn't like you or something... (upload)";
		}
		exit($dl->printBox("<h1>Level to GD</h1>
	                       <p>Error code: $result <a href='reupload/togd.php'>Try again</a></p>"));
		//exit("<br>Error code: $result");
	}
	$dl->printBox("<h1>Level to GD</h1>
	                       <p>Level reuploaded - $result <a href=''>Please click here to continue.</a></p>");
	//echo "Level reuploaded - $result";
}else{
	$dl->printBox('<h1>Level to GD</h1>
	                       <td>'.$dl->getLocalizedString("Your password for the RobTop server is NOT saved, it\'s used for one-time verification purposes only.").'</td>
				<form action="" method="post">
<div class="form-group">
								<label for="userhereField">Username (On GDPS)</label>
								<input type="text" class="form-control" id="userhereField" name="userhere" placeholder="Enter username GDPS">
							</div>
							<div class="form-group">
								<label for="passhereField">Password (On GDPS)</label>
								<input type="password" class="form-control" id="passhereField" name="passhere" placeholder="Password GDPS">
							</div>				
							<div class="form-group">
								<label for="usertargField">Username (RobTop Server)</label>
								<input type="text" class="form-control" id="usertargField" name="usertarg" placeholder="Enter username RobTop Server">
							</div>
							<div class="form-group">
								<label for="passtargField">Password (RobTop Server)</label>
								<input type="password" class="form-control" id="passtargField" name="passtarg" placeholder="Password RobTop Server">
							</div>
							<div class="form-group">
								<label for="levelIDField">levelID</label>
								<input type="text" class="form-control" id="levelIDField" name="levelID" placeholder="Level ID">
							</div>
					<button type="submit" class="btn btn-primary btn-block">Transfer Level</button>
				</form>',"reupload");
}
?>