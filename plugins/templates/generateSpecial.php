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
include 'auth.php';
if($authLevel != '1111'){
    die('authentification failed');
}
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
echo('0');