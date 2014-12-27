<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 19.05.14
 * Time: 09:20
 */
$version = $_POST['version'];
include "access.php";
if($authLevel == '1111'){
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    $que = "UPDATE `".$sqlBase."`.`settings` SET value='$version' WHERE parameter='editorVersion'";
    echo(mysqli_query($sql,$que) or die(mysqli_error($sql)));
}else{
    echo('authentication failed!');
}