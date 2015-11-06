<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 29.03.14
 * Time: 11:54
 */
if($authLevel == '1111'){
    $que2 = "SELECT * FROM plugins WHERE name='settings';";
    $erg2 = mysqli_query($sql,$que2);
    $location = "";
    while($row = mysqli_fetch_array($erg2)){
        $location = $row['location'];
        $name = $row['name'];
        $plugId = $row['id'];
    }
    echo("<img src='".$location."images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
    mysqli_free_result($erg2);
    $location = substr($location,0,strlen($location)-1);
    if(!file_exists("$location/script.js")){
        $output="
    function initPlugin_$plugId(th){
        if(th != 0){
            resetAllPlugins();
            th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
        }
        $.ajax({
            type: 'POST',
            url: '$location/functions.php',
            data: 'function=searchPlugins',
            success: function(data) {
                $('.pluginInner').html(data);
            }
        });
    }
    function updatePlugin(id){
        $.ajax({
            type: 'POST',
            url: '$location/functions.php',
            data: 'function=updatePlugin&id='+id,
            success: function(data) {
                if(data == '1'){
                    reloadLocation('showPlugins&pluginId=$plugId');
                }else{
                    alert(data);
                }
            }
        });
    }
    function removePlugin(id){
        $.ajax({
            type: 'POST',
            url: '$location/functions.php',
            data: 'function=removePlugin&id='+id,
            success: function(data) {
                if(data == '1'){
                    reloadLocation('showPlugins&pluginId=$plugId');
                }else{
                    alert(data);
                }
            }
        });
    }
    function updateAllPlugins(url){
        url = url.substr(url.length-1,1) == '/'?url:url+'/';
        var returnThis = false;
        $.ajax({
            type: 'POST',
            url: url+'functions.php',
            data: 'function=updateAllPlugins',
            success: function(data) {
                if(data != '1'){
                    alert(data);
                }else{
                    returnThis = true;
                }
            }
        });
        return returnThis;
    }";
        $file = fopen("$location/script.js",'w');
        fwrite($file,$output);
        fclose($file);
    }
    echo("
<script src='$location/script.js'></script>
<link rel='stylesheet' href='$location/style.css' />
");
}