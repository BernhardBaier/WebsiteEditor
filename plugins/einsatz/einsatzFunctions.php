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
    $date = $_POST['date'];
    $time = $_POST['time'];
    $short = $_POST['short'];
    $lang = $_POST['lang'];
    $text = $_POST['editor1'];
    $text = str_replace('src="../../','src="',$text);
    $text = str_replace("src='../../","src='",$text);
    $output = "<div class='pluginEinsatzOuter'><div class='pluginEinsatzTitle' title='Zum erweitern anklicken' onclick='$(\"#pluginEinsatzContent$maxId\").toggleClass(\"out\")'>
    <div class='pluginEinsatzCount' title='Einsatz Nummer'>$maxId</div><div class='pluginEinsatzDate' title='Datum'>$date</div><div class='pluginEinsatzTeam' title='Uhrzeit'>$time</div><div class='pluginEinsatzShort'>$short</div></div>";
    $output .= "<div id='pluginEinsatzContent$maxId' class='pluginEinsatzContent out'><div class='pluginEinsatzContentInner'>$text</div></div></div>";
    $input = '';
    if(file_exists("content/$id/$lang/einsatz.php")){
        $file = fopen("content/$id/$lang/einsatz.php",'r');
        $input = fread($file,filesize("content/$id/$lang/einsatz.php"));
        fclose($file);
    }
    $input .= $output;
    $file = fopen("content/$id/$lang/einsatz.php",'w');
    fwrite($file,$input);
    fclose($file);
    header("Location: einsatz.php?success=true&id=$id&lang=$lang");
}