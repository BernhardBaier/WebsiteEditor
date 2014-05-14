<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 12.05.14
 * Time: 22:04
 */
error_reporting(E_ERROR);
$sql = mysqli_connect('localhost','testuser','tester','testbase');
if(isset($_POST['timestamp'])){
    $ts = $_POST['timestamp'];
    $que = "SELECT * FROM communication";
    $erg = mysqli_query($sql, $que);
    $values = [];
    while($row = mysqli_fetch_array($erg)){
        array_push($values,[$row['ts'],$row['value']]);
    }
    if($ts == $values[sizeof($values)-1][0]){
        echo("$ts#end");
    }else{
        $ts = date('Ymdhs');
        echo("$ts#".$values[sizeof($values)-1][1]."#end");
    }
}else{
    if($_POST['value'] != ''){
        $que = "CREATE TABLE `testbase`.`communication`(`ts` VARCHAR ( 100 ) NULL,`value` VARCHAR( 150 ) NULL);";
        mysqli_query($sql, $que);
        $value = $_POST['value'];
        $ts = date('Ymdhs');
        $que = "INSERT INTO communication (ts,value) VALUES ('$ts','$value')";
        mysqli_query($sql,$que);
        $_POST['value'] = '';
        header('server.php');
    }else{
        echo("<form action='server.php' method='post'><input type='text' name='value' placeholder='text' /><input type='submit' value='send' /></form>");
    }
}