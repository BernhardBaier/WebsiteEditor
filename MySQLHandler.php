<?php
/**
 * User: Bernhard
 * Date: 12.01.14
 * Time: 11:19
 */
error_reporting(E_ERROR);
$lang = 'de';
include "MySQLHandlerFunctions.php";
include('access.php');
if($authLevel != '' && $authLevel != '0000'){
    if(substr($authLevel,0,1) == '1'){
        $base = $sqlBase;
        $table = $_POST['table'];
        $lang = substr($table,strrpos($table,'_')+1);
        $function = $_POST['function'];
        $hostname = $_SERVER['HTTP_HOST'];
        $host = $hostname == 'localhost'?$hostname:$sqlHost;
        if($table == 'users'){
            if(substr($authLevel,2,1) == '1'){
                $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
            }
        }else{
            $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
        }
        $backup = mysqli_connect($host,$sqlUser,$sqlPass,$base.'_backup');
        if(!$sql){
            echo('connection failed');
        }else{
            if(strpos($function,':')>-1){
                $option = substr($function,strpos($function,':') + 1);
                $function = substr($function,0,strpos($function,':'));
                $i=0;
                while(strpos($option,':') > -1){
                    $options[$i] =  substr($option,0,strpos($option,':'));
                    $option = substr($option,strpos($option,':')+1);
                    $i++;
                }
                $options[$i] = $option;
            }
            switch($function){
                case 'addMainPage':
                    if(!empty($options) && sizeof($options)>=1){
                        if(addMainPage(replaceUml($options[0]),$sql) >0){
                            echo('1');
                        }
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'addSubPage':
                    if(!empty($options) && sizeof($options)>=2){
                        if(addSubPage(replaceUml($options[0]),$options[1],$sql) > 0){
                            echo('1');
                        }
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'addEqualPage':
                    if(!empty($options) && sizeof($options)>=3){
                        $id = addSubPage(replaceUml($options[0]),$options[1],$sql);
                        moveRank($id,$options[2],$sql);
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'changeName':
                    if(!empty($options) && sizeof($options)>=2){
                        changeName($options[0],replaceUml($options[1]),$sql);
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'deletePage':
                    if(!empty($options) && sizeof($options)>=1){
                        deletePage($options[0],$sql);
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'movePage':
                    if(!empty($options) && sizeof($options)>=2){
                        movePage($options[0],$options[1],$sql);
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'movePageAndRank':
                    if(!empty($options) && sizeof($options)>=2){
                        movePage($options[0],$options[1],$sql);
                        moveRank($options[0],$options[2],$sql);
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'moveRank':
                    if(!empty($options) && sizeof($options)>=2){
                        moveRank($options[0],$options[1],$sql);
                    }else{
                        echo('missing Option(s) for function!');
                    }
                    break;
                case 'setVisibility':
                    if(!empty($options) && sizeof($options)>=2){
                        setVisibility($options[0],$options[1],$sql);
                        echo('1');
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'getNameById':
                    if(!empty($options) && sizeof($options)>=1){
                        echo(getValueById($options[0],'name',$sql));
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'getValueById':
                    if(!empty($options) && sizeof($options)>=2){
                        echo(getValueById($options[0],$options[1],$sql));
                    }else{
                        echo('missing Options for function!');
                    }
                    break;

                case 'deleteTable':
                    deleteTable($sql);
                    break;

                case 'printMenu':
                    echo('<div class="menuItemGroup">');
                    printMenu($sql);
                    echo('</div>');
                    break;

                case 'createUser':
                    if(!empty($options) && sizeof($options)>=4){
                        echo(createUser($options[0],$options[1],$options[2],$options[3],$sql));
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'deleteUser':
                    if(!empty($options) && sizeof($options)>=1){
                        echo(deleteUser($options[0],$sql));
                    }else{
                        echo('missing Options for function!');
                    }
                    break;
                case 'changeUserRights':
                    if(!empty($options) && sizeof($options)>=2){
                        echo(changeUserRights($options[0],$options[1],$sql));
                    }else{
                        echo('missing Options for function!');
                    }
                    break;

                default:
                    echo('mismatching function!');
                    break;
            }
        }
    }
}