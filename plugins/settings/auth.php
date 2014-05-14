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
$authLevel = $_SESSION['authlevel'];