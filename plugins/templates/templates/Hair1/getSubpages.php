<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 30.12.2015
 * Time: 20:59
 */
error_reporting(E_ERROR);
include "access.php";
if(isset($sqlBase)){
    $base = $sqlBase;
    $id = $_POST['id'];
    $lang = $_POST['lang'];
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
    if(!$sql){
        exit;
    }
    if($_POST['action'] == 'sub'){
        $que = "SELECT * FROM pages_$lang WHERE parent='$id'";
        $erg = mysqli_query($sql, $que);
        $ids = [];
        $unsorted = [];
        $return = "";
        $pageNames = "";
        while ($row = mysqli_fetch_array($erg)) {
            array_push($unsorted,$row['id']);
        }
        for ($i = 0; $i < sizeof($unsorted); $i++) {
            $que = "SELECT * FROM pages_$lang WHERE id='$unsorted[$i]'";
            $erg = mysqli_query($sql, $que);
            $j = 0;
            while ($row = mysqli_fetch_array($erg)) {
                $j = $row['rank'] - 1;
            }
            $ids[$j] = $unsorted[$i];
        }
        for ($i = 0; $i < sizeof($ids); $i++) {
            $que = "SELECT * FROM pages_$lang WHERE id='" . $ids[$i] . "'";
            $erg = mysqli_query($sql, $que);
            while ($row = mysqli_fetch_array($erg)) {
                if($row['extra'] == '1') {
                    $return .= $ids[$i] . "{;}";
                    $pageNames .= $row['name'] . "{#}";
                }
            }
        }
        echo($return . $pageNames);
    }else {
        $que = "SELECT * FROM pages_$lang WHERE id='$id'";
        $erg = mysqli_query($sql, $que);
        $ids = [];
        $unsorted = [];
        $return = "";
        $pageNames = "";
        while ($row = mysqli_fetch_array($erg)) {
            $unsorted = unserialize($row['child']);
        }
        $diff = 0;
        $hidden = false;
        for ($i = 0; $i < sizeof($unsorted); $i++) {
            $hidden = false;
            $que = "SELECT * FROM pages_$lang WHERE id='$unsorted[$i]'";
            $erg = mysqli_query($sql, $que);
            $j = 0;
            while ($row = mysqli_fetch_array($erg)) {
                $j = $row['rank'] - 1;
                if($row['extra'] != '1'){
                    $hidden = true;
                    $diff++;
                }
            }
            if(!$hidden){
                $ids[$j-$diff] = $unsorted[$i-$diff];
            }
        }
        for ($i = 0; $i < sizeof($ids); $i++) {
            $return .= $ids[$i] . "{;}";
            $que = "SELECT * FROM pages_$lang WHERE id='" . $ids[$i] . "'";
            $erg = mysqli_query($sql, $que);
            while ($row = mysqli_fetch_array($erg)) {
                $pageNames .= $row['name'] . "{#}";
            }
        }
        echo($return . $pageNames);
    }
}else{
    echo('auth');
}