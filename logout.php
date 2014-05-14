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
session_destroy();
$_SESSION['authlevel'] = '';
header('Location: login.php');
exit;