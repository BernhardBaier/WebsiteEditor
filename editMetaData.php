<?php
include 'access.php';
error_reporting(E_ERROR);
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}
function updateValue($key,$value){
    global $sql,$sqlBase;
    $que2 = "SELECT * FROM `settings` WHERE 1";
    $erg = mysqli_query($sql, $que2);
    $found = false;
    while ($row = mysqli_fetch_array($erg)) {
        if ($row['parameter'] == $key) {
            $found = $row['value'];
        }
    }
    if ($found === false) {
        $que2 = "INSERT INTO $sqlBase.settings (`parameter`,`value`) VALUES ('$key','$value')";
    } else {
        $que2 = "UPDATE `settings` set value='$value' WHERE `parameter`='$key'";
    }
    mysqli_query($sql, $que2) or die(mysqli_error($sql));
}
if(substr($authLevel,0,1) == '1'){
    if($_POST['action'] == 'get'){
        $file = fopen('html5.php','r');
        $input = fread($file,filesize('html5.php'));
        fclose($file);
        $meta = "";
        if(strpos($input,'<!--#meta data#-->') > -1){
            $input = substr($input,strpos($input,'<!--#meta data#-->') + 18);
            $meta = substr($input,0,strpos($input,'<!--#end#-->'));
        }
        if(strlen($meta) < 10){
            $meta = 'false';
        }
        echo($meta);
        exit;
    }elseif($_POST['action'] == 'set'){
        $hostname = $_SERVER['HTTP_HOST'];
        $host = $hostname == 'localhost'?$hostname:$sqlHost;
        $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
        if(!$sql){
            echo('auth failed');
            exit;
        }
        $file = fopen('html5.php','r');
        $input = fread($file,filesize('html5.php'));
        fclose($file);
        $start = "";
        $end = "";
        if(strpos($input,'<!--#meta data#-->') > -1){
            $start = substr($input,0,strpos($input,'<!--#meta data#-->') + 18);
            $input = substr($input,strpos($input,'<!--#meta data#-->') + 18);
            $end = substr($input,strpos($input,'<!--#end#-->'));
        }else{
            echo('no meta supported!');
            exit;
        }
        $des = replaceUml($_POST['des']);
        $des = str_replace('"',"'",$des);
        $key = replaceUml($_POST['key']);
        $key = str_replace('"',"'",$key);
        $aut = replaceUml($_POST['aut']);
        $aut = str_replace('"',"'",$aut);
        $meta = "";
        if(strlen($des) > 2){
            $meta .= '
<meta name="description" content="'.$des.'">';
            updateValue('metaDes',$des);
        }
        if(strlen($key) > 2){
            $meta .= '
<meta name="keywords" content="'.$key.'">';
            updateValue('metaKey',$key);
        }
        if(strlen($aut) > 2){
            $meta .= '
<meta name="author" content="'.$aut.'">';
            updateValue('metaAut',$aut);
        }
        if(strlen($meta) < 2){
            exit;
        }
        $html = $start.$meta.$end;
        $file = fopen('html5.php','w');
        fwrite($file,$html);
        fclose($file);
        $file = fopen('mobile.php','r');
        $input = fread($file,filesize('html5.php'));
        fclose($file);
        $start = "";
        $end = "";
        if(strpos($input,'<!--#meta data#-->') > -1){
            $start = substr($input,0,strpos($input,'<!--#meta data#-->') + 18);
            $input = substr($input,strpos($input,'<!--#meta data#-->') + 18);
            $end = substr($input,strpos($input,'<!--#end#-->'));
        }else{
            exit;
        }
        $html = $start.$meta.$end;
        $file = fopen('mobile.php','w');
        fwrite($file,$html);
        fclose($file);
        exit;
    }
}
echo('false');