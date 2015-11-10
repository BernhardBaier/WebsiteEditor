<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 09.11.2015
 * Time: 00:30
 */
error_reporting(E_ERROR);
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
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
if($_POST['restore'] == 'true'){
    $path = $_POST['path'];
    $path = substr($path,strpos($path,'/')+1);
    $path = substr($path,strpos($path,'/')+1);
    $file = fopen($path,'r');
    $input = fread($file,filesize($path));
    fclose($file);
    $input = substr($input,strpos($input,'<img'));
    $input = substr($input,0,strpos($input,'<div'));
    echo($input);
}else if($_POST['getPages'] == 'true'){
    $lang = $_POST['lang'];
    if(strlen($lang != 2)){
        $lang = 'de';
    }
    $que = "SELECT * FROM settings WHERE parameter='pagesWithSpecials'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        echo($row['value']);
        exit;
    }
}else{
    $text = replaceUml($_POST['text']);
    $output = '<?php
    if(!$sql){
        exit;
    }
    $pagesWithSpecial = null;
    $que = "SELECT * FROM settings WHERE parameter=\'pagesWithSpecials\'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $pagesWithSpecial = $row["value"];
    }
    mysqli_free_result($erg);
    if(strpos($pagesWithSpecial,";$id;")>-1){
        echo(\'<link rel="stylesheet" href="styleSpecialContent.min.css" />\');
        $specialContent = "'.$text.'";
    }';
    $path = $_POST['path'];
    $path = substr($path,strpos($path,'/')+1);
    $path = substr($path,strpos($path,'/')+1);
    $file = fopen($path,'w');
    fwrite($file,$output);
    fclose($file);
    $pages = $_POST['pages'];
    $que2 = "SELECT * FROM `settings` WHERE 1";
    $erg = mysqli_query($sql, $que2);
    $found = false;
    while ($row = mysqli_fetch_array($erg)) {
        if ($row['parameter'] == 'pagesWithSpecials') {
            $found = $row['url'];
        }
    }
    if ($found === false) {
        $que2 = "INSERT INTO $sqlBase.settings (`parameter`,`value`) VALUES ('pagesWithSpecials','$pages')";
    } else {
        $que2 = "UPDATE `settings` set value='$pages' WHERE `parameter`='pagesWithSpecials'";
    }
    mysqli_query($sql, $que2) or die(mysqli_error($sql));
    echo('0');
}