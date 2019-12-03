<?php
class dashboardLib{
	public function printHeader($isSubdirectory = true){
		$this->handleLangStart();
		echo '<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8"> <!-- omg i finally dont have to bother with the xxl long html 4 charset thing -->';
		if($isSubdirectory){
			echo '<base href="../">';
		}
		echo '			<script src="incl/source/jquery-3.2.1.slim.min.js"></script>
						<script async src="incl/source/popper.min.js"></script>
						<script async src="incl/source/bootstrap.min.js"></script>
						<script src="incl/source/Chart.min.js"></script>
						<link rel="stylesheet" href="incl/source/bootstrap.min.css">
						<link async rel="stylesheet" href="incl/cvolton.css">
						<link async rel="stylesheet" href="incl/font-awesome-4.7.0/css/font-awesome.min.css">
						<title>GDPS Dashboard</title>
						<link rel="shortcut icon" href="icon.png" />
						<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
		echo '		</head>
				<body>';
	}
	public function printBoxBody(){
		echo '<div class="container container-box">
					<div class="card">
						<div class="card-block buffer">';
	}
	public function printBox($content, $active = "", $isSubdirectory = true){
		$this->printHeader($isSubdirectory);
		$this->printNavbar($active);
		$this->printBoxBody();
		echo "$content";
		$this->printBoxFooter();
		$this->printFooter();
	}
	public function printBoxFooter(){
		echo '</div></div></div>';
	}
	public function printFooter(){
		echo '</body>
		</html>';
	}
	public function printLoginBox($content){
		$this->printBox("<h1>Login</h1>".$content);
	}
	public function printLoginBoxInvalid(){
		$this->printLoginBox("<p>Invalid username or password. <a href=''>Click here to try again.</a>");
	}
	public function printLoginBoxError($content){
		$this->printLoginBox("<p>An error has occured: $content. <a href=''>Click here to try again.</a>");
	}
	public function printNavbar($active){
		require_once __DIR__."/../../incl/lib/mainLib.php";
		$gs = new mainLib();
		$homeActive = "";
		$accountActive = "";
		$browseActive = "";
		$modActive = "";
		$adminActive = "";
		$reuploadActive = "";
		$statsActive = "";
		switch($active){
			case "home":
				$homeActive = "active";
				break;
			case "account":
				$accountActive = "active";
				break;
			case "browse":
				$browseActive = "active";
				break;
			case "mod":
				$modActive = "active";
				break;
			case "admin":
				$adminActive = "active";
				break;
			case "reupload":
				$reuploadActive = "active";
				break;
			case "stats":
				$statsActive = "active";
				break;
		}
		echo '<nav class="navbar navbar-expand-lg navbar-dark menubar">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNavDropdown">
				<ul class="navbar-nav">
					<li class="nav-item '.$homeActive.' ">
						<a class="nav-link" href="index.php">
							<i class="fa fa-home" aria-hidden="true"></i> '.$this->getLocalizedString("homeNavbar").'
						</a>
					</li>';
		$browse = '<li class="nav-item dropdown '.$browseActive.' ">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-folder-open" aria-hidden="true"></i> '.$this->getLocalizedString("browse").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="browse/browseUser.php">'.$this->getLocalizedString("Browse User").'</a>
							<a class="dropdown-item" href="browse/vipList.php">'.$this->getLocalizedString("ModList").'</a>
							<a class="dropdown-item" href="browse/demoteList.php">'.$this->getLocalizedString("ExmodList").'</a>	
							<a class="dropdown-item" href="browse/modCount.php">'.$this->getLocalizedString("modcount").'</a>
							<a class="dropdown-item" href="browse/ElderCount.php">'.$this->getLocalizedString("eldercount").'</a>';
		if(isset($_SESSION["accountID"]) AND $_SESSION["accountID"] != 0){
			echo '
					<li class="nav-item dropdown '.$accountActive.' ">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-user" aria-hidden="true"></i> '.$this->getLocalizedString("accountManagement").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="account/changePassword.php">'.$this->getLocalizedString("changePassword").'</a>
							<a class="dropdown-item" href="account/changeUsername.php">'.$this->getLocalizedString("changeUsername").'</a>
							<a class="dropdown-item" href="account/unlisted.php">'.$this->getLocalizedString("unlistedLevels").'</a>
							<a class="dropdown-item" href="account/myLevels.php">'.$this->getLocalizedString("myLevels").'</a>
							<a class="dropdown-item" href="account/myComments.php">'.$this->getLocalizedString("commentshistory").'</a>
						</div>
					</li>' . $browse . '</div></li>';
			if($gs->checkPermission($_SESSION["accountID"], "dashboardModTools")){
				echo '<li class="nav-item dropdown '.$modActive.'">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-wrench" aria-hidden="true"></i> '.$this->getLocalizedString("modTools").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="modtools/banned.php">'.$this->getLocalizedString("banned").'</a>
							<a class="dropdown-item" href="modtools/modsendList.php">'.$this->getLocalizedString("sendlevels").'</a>
							<a class="dropdown-item" href="modtools/modActionsList.php">'.$this->getLocalizedString("modactions").'</a>
							<a class="dropdown-item" href="modtools/elderActionsList.php">'.$this->getLocalizedString("elderactions").'</a>
						</div>
					</li>';
			}
			if($gs->checkPermission($_SESSION["accountID"], "dashboardAdminTools")){
				echo '<li class="nav-item dropdown '.$adminActive.'">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-wrench" aria-hidden="true"></i> '.$this->getLocalizedString("adminTools").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
						    <a class="dropdown-item" href="admin/levelReupload.php">Level Reupload</a>
						    <a class="dropdown-item" href="admin/packCreate.php">'.$this->getLocalizedString("packManage").'</a>
						    <a class="dropdown-item" href="admin/gauntletCreate.php">Create Gauntlet</a>
							<a class="dropdown-item" href="admin/restrictions.php">'.$this->getLocalizedString("restrictions").'</a>			
							<a class="dropdown-item" href="admin/passwordRecovery.php">'.$this->getLocalizedString("passrecovery").'</a>					
						</div>
					</li>';
			}
		}else{
			echo $browse . "</div></li>";
		}
		echo '		<li class="nav-item dropdown '.$reuploadActive.'">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-upload" aria-hidden="true"></i> '.$this->getLocalizedString("reuploadSection").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="reupload/songAdd.php">'.$this->getLocalizedString("songAdd").'</a>
							<a class="dropdown-item" href="reupload/togd.php">'.$this->getLocalizedString("leveltogd").'</a>
						</div>
					</li>
					<li class="nav-item dropdown '.$statsActive.'">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-bar-chart" aria-hidden="true"></i> '.$this->getLocalizedString("statsSection").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="stats/top24h.php">'.$this->getLocalizedString("leaderboardTime").'</a>
							<a class="dropdown-item" href="stats/general.php">'.$this->getLocalizedString("generalstats").'</a>
							<a class="dropdown-item" href="stats/dailyTable.php">'.$this->getLocalizedString("dailyTable").'</a>
							<a class="dropdown-item" href="stats/dailyTablePending.php">'.$this->getLocalizedString("dailyTableP").'</a>
							<a class="dropdown-item" href="stats/packTable.php">'.$this->getLocalizedString("packTable").'</a>
							<a class="dropdown-item" href="stats/gauntletTable.php">'.$this->getLocalizedString("gauntletTable").'</a>
							<a class="dropdown-item" href="stats/reportList.php">'.$this->getLocalizedString("reportlist").'</a>
                            <a class="dropdown-item" href="stats/songList.php">'.$this->getLocalizedString("songs").'</a>
							
						</div>
					</li>
				</ul>
				<ul class="nav navbar-nav ml-auto">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-language" aria-hidden="true"></i> '.$this->getLocalizedString("language").'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="lang/switchLang.php?lang=EN">English</a>
						</div>';
		if(isset($_SESSION["accountID"]) AND $_SESSION["accountID"] != 0){
			$userName = $gs->getAccountName($_SESSION["accountID"]);
			echo'<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-user-circle" aria-hidden="true"></i> '.sprintf($this->getLocalizedString("loginHeader"), $userName).'
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="login/logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> '.$this->getLocalizedString("logout").'</a>
						</div>
					</li>';
		}else{
			/*echo '<li class="nav-item">
						<a class="nav-link" href="login/login.php"><i class="fa fa-sign-in" aria-hidden="true"></i> '.$this->getLocalizedString("login").'</a>
					</li>';*/
			echo '<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-sign-in" aria-hidden="true"></i> '.$this->getLocalizedString("login").'
						</a>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink" style="padding:17px;">
									<form action="login/login.php" method="post">
										<div class="form-group">
											<input type="text" class="form-control login-input" id="usernameField" name="userName" placeholder="Username">
										</div>
										<div class="form-group">
											<input type="password" class="form-control login-input" id="passwordField" name="password" placeholder="Password">
										</div>
										<button type="submit" class="btn btn-primary btn-block">'.$this->getLocalizedString("login").'</button>
									</form>
						</div>';
		}		
		echo'	</ul>
			</div>
		</nav>';
	}
	public function printPage($content, $isSubdirectory = true, $navbar = "home"){
		$dl = new dashboardLib();
		$dl->printHeader($isSubdirectory);
		$dl->printNavbar($navbar);
		echo '<div class="container d-flex flex-column">
				<div class="row fill d-flex justify-content-start content buffer">
					'.$content.'
				</div>
			</div>';
		$dl->printFooter();
	}
	public function printPage2($content){
		echo '<div class="container d-flex flex-column">
				<div class="row fill d-flex justify-content-start content buffer">
					'.$content.'
				</div>
			</div>';
	}
	public function printTable($content, $isSubdirectory = true, $navbar = "home"){
		$dl = new dashboardLib();
		echo '<div class="container d-flex flex-column">
				<div class="row fill d-flex justify-content-start content buffer">
					'.$content.'
				</div>
			</div>';
		$dl->printFooter();
	}
	public function handleLangStart(){
		if(!isset($_COOKIE["lang"]) OR !ctype_alpha($_COOKIE["lang"])){
			setcookie("lang", "EN", 2147483647, "/");
		}
	}
	public function getLocalizedString($stringName){
		if(!isset($_COOKIE["lang"]) OR !ctype_alpha($_COOKIE["lang"])){
			$lang = "EN";
		}else{
			$lang = $_COOKIE["lang"];
		}
		$locale = __DIR__ . "/lang/locale".$lang.".php";
		if(file_exists($locale)){
			include $locale;
		}else{
			include __DIR__ . "/lang/localeEN.php";
		}
		if($lang == "TEST"){
			return "lnf:$stringName";
		}
		if(isset($string[$stringName])){
			return $string[$stringName];
		}else{
			return "lnf:$stringName";
		}
	}
	public function convertToDate($timestamp){
		return date("d/m/Y G:i:s", $timestamp);
	}
	public function generateBottomRow($pagecount, $actualpage){
		$pageminus = $actualpage - 1;
		$pageplus = $actualpage + 1;
		$bottomrow = '<div>'.sprintf($this->getLocalizedString("pageInfo"),$actualpage,$pagecount).'</div><div class="btn-group" style="margin-left:auto; margin-right:0;">';
		$bottomrow .= '<a id="first" href="'.strtok($_SERVER["REQUEST_URI"],'?').'?page=1" class="btn btn-outline-secondary"><i class="fa fa-backward" aria-hidden="true"></i> '.$this->getLocalizedString("first").'</a><a id="prev" href="'.strtok($_SERVER["REQUEST_URI"],'?').'?page='. $pageminus .'" class="btn btn-outline-secondary"><i class="fa fa-chevron-left" aria-hidden="true"></i> '.$this->getLocalizedString("previous").'</a>';
		//updated to ".."
		$bottomrow .= '<a class="btn btn-outline-secondary" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">..</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" style="padding:17px;">
				<form action="" method="get">
					<div class="form-group">
						<input type="text" class="form-control" name="page" placeholder="#">';
		foreach($_GET as $key => $param){
			if($key != "page"){
				$bottomrow .= '<input type="hidden" name="'.$key.'" value="'.$param.'">';
			}
		}
		$bottomrow .= '</div>
					<button type="submit" class="btn btn-primary btn-block">'.$this->getLocalizedString("go").'</button>
				</form>
			</div>';
		$bottomrow .= '<a href="'.strtok($_SERVER["REQUEST_URI"],'?').'?page='.$pageplus.'" id="next" class="btn btn-outline-secondary">'.$this->getLocalizedString("next").' <i class="fa fa-chevron-right" aria-hidden="true"></i></a><a id="last" href="'.strtok($_SERVER["REQUEST_URI"],'?').'?page='. $pagecount .'" class="btn btn-outline-secondary">'.$this->getLocalizedString("last").' <i class="fa fa-forward" aria-hidden="true"></i></a>';
		$bottomrow .= "</div><script>
			function disableElement(element){
				if(element){
					element.className += first.className ? ' disabled' : 'disabled';
				}
			}
			var pagecount = $pagecount;
			var actualpage = $actualpage;
			if(actualpage == 1){
				disableElement(document.getElementById('first'));
				disableElement(document.getElementById('prev'));
			}
			if(pagecount == actualpage){
				disableElement(document.getElementById('last'));
				disableElement(document.getElementById('next'));
			}
			</script>";
		return $bottomrow;
	}
	public function generateLineChart($elementID, $name, $data){
		$labels = implode('","', array_keys($data));
		$data = implode(',', $data);
		$chart = "<script>
					var ctx = document.getElementById(\"$elementID\");
					var myChart = new Chart(ctx, {
						type: 'line',
						data: {
							labels: [\"$labels\"],
							datasets: [{
								label: '$name',
								data: [$data],
								backgroundColor: [
									'rgba(255, 99, 132, 0.2)'
								],
								borderColor: [
									'rgba(255,99,132,1)'
								],
							}]
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero:true
									}
								}]
							}
						}
					});
					</script>";
		return $chart;
	}
}