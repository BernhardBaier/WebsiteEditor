<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 22.05.14
 * Time: 14:42
 */
include "auth.php";
if(substr($authLevel,1,1) == "1"){
    $id = $_POST['id'];
    $maxId = $_POST['maxId'];
    $adminId = $_POST['adminId'];
    $date = $_POST['date'];
    if(strpos($date,'-') == 4){
        $year = substr($date,0,4);
        $month = substr($date,5,2);
        $day = substr($date,8);
        $date = $day.'.'.$month.'.'.$year;
    }else{
        $date = str_replace('-','.',$date);
    }
    $time = $_POST['time'];
    $short = $_POST['short'];
    $lang = $_POST['lang'];
    $text = $_POST['editor1'];
    $text = str_replace('src="../../','src="',$text);
    $text = str_replace("src='../../","src='",$text);
    $output = "<div class='pluginEinsatzOuter'><div class='pluginEinsatzTitle' title='Zum erweitern anklicken' onclick='$(\"#pluginEinsatzContent$adminId\").toggleClass(\"out\")'>";
    $output .= "<div class='pluginEinsatzCount' title='Einsatz Nummer'>$adminId</div><div class='pluginEinsatzDate' title='Datum'>$date</div><div class='pluginEinsatzTeam' title='Uhrzeit'>$time</div><div class='pluginEinsatzShort'>$short</div></div>";
    $output .= "<div id='pluginEinsatzContent$adminId' class='pluginEinsatzContent out'><div class='pluginEinsatzContentInner'>$text</div></div></div>";
    $input = '';
    if(file_exists("content/$id/$lang/einsatz.php")){
        $file = fopen("content/$id/$lang/einsatz.php",'r');
        $input = fread($file,filesize("content/$id/$lang/einsatz.php"));
        fclose($file);
    }
    $empty = ["<br>"," ","<p>&nbsp;</p>"];
    if($adminId != $maxId){
        $pos = strpos($input,'#pluginEinsatzContent'.$adminId)-300;
        $pos = $pos<0?0:$pos;
        $ktxt = substr($input,$pos);
        $ktxt = substr($ktxt,strpos($ktxt,'pluginEinsatzOuter')-12);
        while(strrpos($ktxt,'pluginEinsatzOuter') > 20){
            $ktxt = substr($ktxt,0,strrpos($ktxt,'pluginEinsatzOuter')-12);
        }
        $ktxt = substr($ktxt,0,strrpos($ktxt,'</div>')+6);
        if(str_replace($empty,"",$text) == ""){
            $input = str_replace($ktxt,"",$input);
        }else{
            $input = str_replace($ktxt,$output,$input);
        }
    }else{
        if(str_replace($empty,"",$text) != ""){
            $input = $output . $input;
        }
    }
    $file = fopen("content/$id/$lang/einsatz.php",'w');
    fwrite($file,$input);
    fclose($file);
    header("Location: einsatz.php?success=true&id=$id&lang=$lang");
}