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
if(substr($authLevel,1,1) == "1"){
    $function = $_POST['function'];
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    switch($function){
        case 'getArticles':
            $callback = "";
            $que2 = "SELECT * FROM settings WHERE parameter='articleIds'";
            $erg2 = mysqli_query($sql,$que2);
            $articleIds = [];
            while($row = mysqli_fetch_array($erg2)){
                $in = $row['value'];
                if($in != 'NULL'){
                    $articleIds = unserialize($in);
                }
            }
            mysqli_free_result($erg2);
            $articles = "<ul>";
            $max = sizeof($articleIds);
            for($i=0;$i<$max;$i++){
                $callback = str_replace('$pid',$articleIds[$i],$_POST['callback']);
                if(isset($_POST['callback'])){
                    $articles .= "<li class='pluginArticleItem2' onclick='$callback'>".getValueById($articleIds[$i],'name',$sql)."</li>";
                }else{
                    $articles .= "<li><div class='pluginArticleItem'>".getValueById($articleIds[$i],'name',$sql)."</div><div class='pluginArticleItem2' onclick='removeArticle(".$articleIds[$i].")'> --> remove from list</div></li>";
                }
            }
            $articles .= '</ul>';
            $articles = $articles == '<ul></ul>'?'none':$articles;
            echo($articles);
            break;
        case 'setNewArticle':
            $id = $_POST['id'];
            $que2 = "SELECT * FROM settings WHERE parameter='articleIds'";
            $erg2 = mysqli_query($sql,$que2);
            $articleIds = [];
            while($row = mysqli_fetch_array($erg2)){
                $articleIds = $row['value'];
            }
            mysqli_free_result($erg2);
			if(sizeof($articleIds) == 0){
				$articleIds = 'NULL';
			}
            if($articleIds == 'NULL'){
                $articleIds = [];
                $articleIds[0] = $id;
                $que2 = "INSERT INTO $sqlBase.settings (value,parameter) VALUES ('".serialize($articleIds)."','articleIds')";
	            mysqli_query($sql,$que2) or die(mysqli_error($sql));
            }else{
                $articleIds = unserialize($articleIds);
                if(findInArray($articleIds,$id) == -1){
                    array_push($articleIds,$id);
                    $que2 = "UPDATE settings set value='".serialize($articleIds)."' WHERE parameter='articleIds'";
                    mysqli_query($sql,$que2) or die(mysqli_error($sql));
                }
            }
	        $que2 = "INSERT INTO $sqlBase.toreplace (`replace`,`url`) VALUES ('{#insertPluginArticle$id/$lang#}','plugins/article/content/$id/$lang/article.php')";
	        mysqli_query($sql,$que2) or die(mysqli_error($sql));
            echo('1');
            break;
        case 'removeArticle':
            $id = $_POST['id'];
	        $que2 = "SELECT * FROM settings WHERE parameter='articleIds'";
	        $erg2 = mysqli_query($sql,$que2);
	        $articleIds = [];
	        while($row = mysqli_fetch_array($erg2)){
		        $articleIds = unserialize($row['value']);
	        }
	        mysqli_free_result($erg2);
	        $que2 = "DELETE FROM $sqlBase.toreplace WHERE `replace`='{#insertPluginArticle$id/$lang#}'";
	        if(!(mysqli_query($sql,$que2))){
		        echo("couldn't delete: $que2");
	        }
            for($i=0;$i<sizeof($articleIds);$i++){
                if($articleIds[$i] == $id){
                    array_splice($articleIds,$i,1);
                }
            }
            if($articleIds == []){
                $que = "UPDATE settings set value='NULL' WHERE parameter='articleIds'";
                mysqli_query($sql,$que) or die(mysqli_error($sql));
            }else{
                $que2 = "UPDATE settings set value='".serialize($articleIds)."' WHERE parameter='articleIds'";
                mysqli_query($sql,$que2) or die(mysqli_error($sql));
            }
            echo('1');
            break;
    }
}