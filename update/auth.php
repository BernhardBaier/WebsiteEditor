<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.01.14
 * Time: 14:46
 */
session_start();

$hostname = $_SERVER['HTTP_HOST'];

if (!isset($_SESSION['authlevel'])  || !$_SESSION['authlevel'] || $_SESSION['authlevel'] == '0000') {
    header('Location: login.php');
    exit;
}
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
if($ip == $_SESSION['ip']){
    $authLevel = $_SESSION['authlevel'];
}else{
    header('logout.php');
}