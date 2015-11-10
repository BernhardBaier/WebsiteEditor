<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 06.11.2015
 * Time: 20:21
 */
error_reporting(E_ERROR);
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
if($_POST['saveText'] == 'true'){
    include 'auth.php';
    if($authLevel != '1111'){
        die('authentification failed');
    }
    $text = replaceUml($_POST['text']);
    $path = $_POST['path'];
    $path = substr($path,strpos($path,'/')+1);
    $path = substr($path,strpos($path,'/')+1);
    $file = fopen($path,'w');
    fwrite($file,$text);
    fclose($file);
}else{
    include 'access.php';
    if($authLevel != '1111'){
        die('authentification failed');
    }
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if(!$sql){
        die('MySQL-Error');
    }
    $name = replaceUml($_POST['name']);
    $lang = $_POST['lang'];
    if(strlen($lang != 2)){
        $lang = 'de';
    }
    if(strlen($name) > 3){
        $que = "SELECT * FROM pages_$lang WHERE name='$name'";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            if($row['name'] == $name){
                echo($row['id']);
                exit;
            }
        }
    }
}
echo('0');