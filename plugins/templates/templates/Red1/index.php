<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 20.01.14
 * Time: 17:08
 */
error_reporting(E_ERROR);
function redirectToFirstPage($lang){
    global $sql;
    $que = "SELECT * FROM pages_$lang WHERE parent='0' and rank='1'";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    while($row = mysqli_fetch_array($erg)){
        return $row['id'];
    }
}
if(!isset($_GET['id'])){
    include 'access.php';
    $base = $sqlBase;
    $table = 'pages_'.$lang;
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
    $lang = $_GET['lang'];
    if($lang == ''){
        $lang = 'de';
    }
    $id = redirectToFirstPage($lang);
    header("Location: index.php?id=$id&lang=$lang");
    exit;
}
$html5 = false;
$mobile = false;
if($_GET['forceMobile'] == 'true'){
    $mobile = true;
}
$browser = $_SERVER['HTTP_USER_AGENT'];
if(strpos($browser,'Firefox') > -1){
    if(substr($browser,strpos($browser,'Firefox')+8,4) >= 16){
        $html5 = true;
        if(strpos($browser,'Mobile') > -1){
            $mobile = true;
        }
    }
    $browser = 'Firefox';
}elseif(strpos($browser,'Chrome') > -1){
    if(substr($browser,strpos($browser,'Chrome')+7,4) >= 21){
        $html5 = true;
        if(strpos($browser,'Mobile') > -1){
            $mobile = true;
        }
    }
    $browser = 'Chrome';
}elseif(strpos($browser,'OPR/') > -1){
    if(substr($browser,strpos($browser,'OPR/')+4,4) >= 10.5){
        $html5 = true;
        if(strpos($browser,'Mobile') > -1){
            $mobile = true;
        }
    }
    $browser = 'Opera';
}elseif(strpos($browser,'MSIE') > -1){
    if(substr($browser,strpos($browser,'MSIE')+5,2) >= 10){
        $html5 = true;
        if(strpos($browser,'Windows Phone') > -1){
            $mobile = true;
        }
    }
    $browser = 'IE';
}elseif(strpos($browser,'rv:') > -1 && strpos($browser,'like Gecko') > -1){
    if(substr($browser,strpos($browser,'rv:')+3,2) >= 10){
        $html5 = true;
        if(strpos($browser,'Windows Phone') > -1){
            $mobile = true;
        }
    }
    $browser = 'IE';
}elseif(strpos($browser,'Android') > -1){
    $html5 = true;
    $mobile = true;
}
if($html5 === true){
    if($mobile === true){
        include('mobile.php');
    }else{
        include('html5.php');
    }
}else{
    $brMsg = $browser == 'IE'?" oder verwenden statt des Internet Explorers einen aktuellen Alternativbrowser wie Mozilla Firefox.":".";
    echo("<style>
    .oldBrowserWarning{
        background: #f36c60;
        border: 1px solid #f00;
        padding: 2px;
        margin: 5px;
    }
    </style>
    <div class='oldBrowserWarning'>Sie verwenden eine veraltete Browserversion. Um diese Seite korrekt darstellen zu können aktualisieren Sie bitte Ihren Browser$brMsg</div>");
    if($mobile === true){
        include('mobile.php');
    }else{
        include('html5.php');
    }
}