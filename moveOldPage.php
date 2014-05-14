<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 14.05.14
 * Time: 17:33
 */
error_reporting(E_ERROR);
$headlines = [];
include '../sites_de.php';
$lang = 'de';
$table = 'pages_de';
$title = '';
include 'MySQLHandlerFunctions.php';
include('auth.php');
function moveFile($org,$dest,$name){
    global $title;
    $name = str_replace($title.' - ','',$name);
    $file = fopen($org,'r');
    $in = fread($file,filesize($org));
    fclose($file);
    $in = "<h1>$name</h1>$in";
    $file = fopen($dest,'w');
    fwrite($file,$in);
    fclose($file);
}
if($authLevel != '' && $authLevel != '0000'){
    if(substr($authLevel,0,1) == '1'){
        include('access.php');
        $base = $sqlBase;
        $lang = substr($table,strrpos($table,'_')+1);
        $hostname = $_SERVER['HTTP_HOST'];
        $host = $hostname == 'localhost'?$hostname:$sqlHost;
        $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
        for($i1=1;$i1<10;$i1++){
            if($headlines[$i1] != ''){
                $title = $headlines[$i1];
                $id1 = addMainPage($headlines[$i1],$sql);
                setVisibility($id1,'1',$sql);
                moveFile("../content/de/$i1.php","content/de/$id1.php",$headlines[$i1]);
                for($i2=$i1*10;$i2<$i1*10+10;$i2++){
                    if($headlines[$i2] != ''){
                        $id2 = addSubPage(str_replace($title.' - ','',$headlines[$i2]),$id1,$sql);
                        setVisibility($id2,'1',$sql);
                        moveFile("../content/de/$i2.php","content/de/$id2.php",$headlines[$i2]);
                        for($i3=$i2*10;$i3<$i2*10+10;$i3++){
                            if($headlines[$i3] != ''){
                                $id3 = addSubPage(str_replace($title.' - ','',$headlines[$i3]),$id2,$sql);
                                setVisibility($id3,'1',$sql);
                                moveFile("../content/de/$i3.php","content/de/$id3.php",$headlines[$i3]);
                                for($i4=$i3*10;$i4<$i3*10+10;$i4++){
                                    if($headlines[$i4] != ''){
                                        $id4 = addSubPage(str_replace($title.' - ','',$headlines[$i4]),$id3,$sql);
                                        setVisibility($id4,'1',$sql);
                                        moveFile("../content/de/$i4.php","content/de/$id4.php",$headlines[$i4]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}