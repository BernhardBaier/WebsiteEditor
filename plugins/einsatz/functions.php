<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 22.05.14
 * Time: 15:58
 */
error_reporting(E_ERROR);
$lang = $_POST['lang'];
$table = 'pages_'.$lang;
include('../../MySQLHandlerFunctions.php');
include('access.php');
include('../../functionsPlugins.php');
if(substr($authLevel,1,1) == "1"){
    $function = $_POST['function'];
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    switch($function){
        case 'getEinsatzs':
            $callback = "";
            $que2 = "SELECT * FROM settings WHERE parameter='einsatzIds'";
            $erg2 = mysqli_query($sql,$que2);
            $einsatzIds = [];
            while($row = mysqli_fetch_array($erg2)){
                $in = $row['value'];
                if($in != 'NULL'){
                    $einsatzIds = unserialize($in);
                }
            }
            mysqli_free_result($erg2);
            $einsatzs = "<ul>";
            $max = sizeof($einsatzIds);
            for($i=0;$i<$max;$i++){
                $callback = str_replace('$pid',$einsatzIds[$i],$_POST['callback']);
                if(isset($_POST['callback'])){
                    $einsatzs .= "<li class='pluginEinsatzItem2' onclick='$callback'>".getValueById($einsatzIds[$i],'name',$sql)."</li>";
                }else{
                    $einsatzs .= "<li><div class='pluginEinsatzItem'>".getValueById($einsatzIds[$i],'name',$sql)."</div><div class='pluginEinsatzItem2' onclick='removeEinsatz(".$einsatzIds[$i].")'> --> remove from list</div></li>";
                }
            }
            $einsatzs .= '</ul>';
            $einsatzs = $einsatzs == '<ul></ul>'?'none':$einsatzs;
            echo($einsatzs);
            break;
        case 'setNewEinsatz':
            $id = $_POST['id'];
            $que2 = "SELECT * FROM settings WHERE parameter='einsatzIds'";
            $erg2 = mysqli_query($sql,$que2);
            $einsatzIds = [];
            while($row = mysqli_fetch_array($erg2)){
                $einsatzIds = $row['value'];
            }
            mysqli_free_result($erg2);
			if(sizeof($einsatzIds) == 0){
				$einsatzIds = 'NULL';
			}
            if($einsatzIds == 'NULL'){
                $einsatzIds = [];
                $einsatzIds[0] = $id;
                $que2 = "INSERT INTO $sqlBase.settings (value,parameter) VALUES ('".serialize($einsatzIds)."','einsatzIds')";
	            mysqli_query($sql,$que2) or die(mysqli_error($sql));
            }else{
                $einsatzIds = unserialize($einsatzIds);
                if(findInArray($einsatzIds,$id) == -1){
                    array_push($einsatzIds,$id);
                    $que2 = "UPDATE settings set value='".serialize($einsatzIds)."' WHERE parameter='einsatzIds'";
                    mysqli_query($sql,$que2) or die(mysqli_error($sql));
                }
            }
			addHTMLToReplace("{#insertPluginEinsatz$id/$lang#}","plugins/einsatz/content/$id/$lang/einsatz.php");
            echo('1');
            break;
	    case 'removeEinsatz':
		    $id = $_POST['id'];
		    $que2 = "SELECT * FROM settings WHERE parameter='einsatzIds'";
		    $erg2 = mysqli_query($sql,$que2);
		    $einsatzIds = [];
		    while($row = mysqli_fetch_array($erg2)){
			    $einsatzIds = unserialize($row['value']);
		    }
		    mysqli_free_result($erg2);
		    removeHTMLFromReplace("{#insertPluginEinsatz$id/$lang#}");
		    for($i=0;$i<sizeof($einsatzIds);$i++){
			    if($einsatzIds[$i] == $id){
				    array_splice($einsatzIds,$i,1);
			    }
		    }
		    if($einsatzIds == []){
			    $que = "UPDATE settings set value='NULL' WHERE parameter='einsatzIds'";
			    mysqli_query($sql,$que) or die(mysqli_error($sql));
		    }else{
			    $que2 = "UPDATE settings set value='".serialize($einsatzIds)."' WHERE parameter='einsatzIds'";
			    mysqli_query($sql,$que2) or die(mysqli_error($sql));
		    }
		    echo('1');
		    break;

	    default:
		    echo("undefined call to function $function()");
		    break;
    }
}