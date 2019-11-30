<?php
//error_reporting(0);
include dirname(__FILE__)."/../../config/connection.php";
@header('Content-Type: text/html; charset=utf-8');
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, array(
    PDO::ATTR_PERSISTENT => true
));
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
/*
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $time = time();
    $your_path = $_SERVER["DOCUMENT_ROOT"] . "/incl/lib/ip/"; 
    $fileName = "$time | $ip";
    file_put_contents($your_path . $fileName, $time);
    */
?>