<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 19.05.14
 * Time: 09:20
 */
$version = $_POST['version'];
include "access.php";
if(substr($authLevel,0,1) == '1'){
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    $que = "UPDATE settings SET value='$version' WHERE parameter='editorVersion'";
    mysqli_query($sql,$que);
    echo('1');
}