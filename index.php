<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 20.01.14
 * Time: 17:08
 */
error_reporting(E_ERROR);
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
}elseif(strpos($browser,'Chrome') > -1){
    if(substr($browser,strpos($browser,'Chrome')+7,4) >= 21){
        $html5 = true;
        if(strpos($browser,'Mobile') > -1){
            $mobile = true;
        }
    }
}elseif(strpos($browser,'OPR/') > -1){
    if(substr($browser,strpos($browser,'OPR/')+4,4) >= 10.5){
        $html5 = true;
        if(strpos($browser,'Mobile') > -1){
            $mobile = true;
        }
    }
}elseif(strpos($browser,'MSIE') > -1){
    if(substr($browser,strpos($browser,'MSIE')+5,2) >= 10){
        $html5 = true;
        if(strpos($browser,'Windows Phone') > -1){
            $mobile = true;
        }
    }
}elseif(strpos($browser,'rv:') > -1 && strpos($browser,'like Gecko') > -1){
    if(substr($browser,strpos($browser,'rv:')+3,2) >= 10){
        $html5 = true;
        if(strpos($browser,'Windows Phone') > -1){
            $mobile = true;
        }
    }
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
    echo('old browser!');
}