<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 04.07.14
 * Time: 11:13
 */
error_reporting(E_ERROR);
if(!$authLevel){
    include 'access.php';
    $sql = null;
}
function addHTMLToReplace($html, $path)
{
    global $sqlBase, $sql;
    $que2 = "SELECT * FROM `toreplace` WHERE 1";
    $erg = mysqli_query($sql, $que2);
    $found = false;
    while ($row = mysqli_fetch_array($erg)) {
        if ($row['replace'] == $html) {
            $found = $row['url'];
        }
    }
    if ($found === false) {
        $que2 = "INSERT INTO $sqlBase.toreplace (`replace`,`url`) VALUES ('$html','$path')";
    } else {
        $que2 = "UPDATE `toreplace` set url='$path' WHERE `replace`='$html'";
    }
    mysqli_query($sql, $que2) or die(mysqli_error($sql));
}

function removeHTMLFromReplace($html)
{
    global $sqlBase, $sql;
    $que2 = "DELETE FROM $sqlBase.toreplace WHERE `replace`='$html'";
    if (!(mysqli_query($sql, $que2))) {
        echo("couldn't delete: $que2");
    }
}
if(substr($authLevel,0,1) == '1') {
    $html_in = $_POST['html'];
    $path_in = $_POST['path'];
    $action = $_POST['action'];
    if(isset($_POST['action'])){
        $hostname = $_SERVER['HTTP_HOST'];
        $host = $hostname == 'localhost'?$hostname:$sqlHost;
        $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    }
    if($action == 'addHTML'){
        addHTMLToReplace($html_in,$path_in);
        echo('1');
    }else if($action == 'removeHTML'){
        removeHTMLFromReplace($html_in);
        echo('0');
    }
}