<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.01.14
 * Time: 15:00
 */
@session_start();
$_SESSION['authlevel'] = '';
$_SESSION['user'] = '';
$_SESSION['extra'] = '';
$_SESSION['register'] = '';
$_SESSION['id'] = '';
$_SESSION['ip'] = '';
session_destroy();
$_SESSION['authlevel'] = '';
setcookie('PHPSESSID','NULL',time()+1);
header('Location: login.php');
exit;