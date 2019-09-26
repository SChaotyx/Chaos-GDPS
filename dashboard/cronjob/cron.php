<?php
chdir(dirname(__FILE__));
set_time_limit(0);
include "SCAutoban.php";
ob_flush();
flush();
include "removeInvalidUsers.php";
ob_flush();
flush();
file_put_contents("../logs/cronlastrun.txt",time());
?>
