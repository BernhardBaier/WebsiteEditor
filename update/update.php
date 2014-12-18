<?php
error_reporting(E_ERROR);
include 'auth.php';
if($authLevel == '1111'){
    $updateVersion = "2.0";
    $updateUpdater = false;
    if($_GET['action'] == 'updateFileList'){
        $file = fopen('fileList.list','r');
        $in = fread($file,filesize('fileList.list'));
        fclose($file);
        $version = substr($in,0,strpos($in,PHP_EOL));
        if(strpos($in,'#updateVersion#') > -1){
            $in = substr($in,strpos($in,PHP_EOL));
        }
        $in = $version.'#updateVersion#'.$updateVersion.'#'.$in;
        $file = fopen('fileList.list','w');
        fwrite($file,$in);
        fclose($file);
    }
    $file = fopen('fileList.list','r');
    $in = fread($file,filesize('fileList.list'));
    fclose($file);
    $oldVersion = substr($in,strpos($in,'#version#')+9);
    $oldVersion = substr($oldVersion,0,strpos($oldVersion,'#'));
    if(strpos($in,'#updateVersion#') > -1){
        $upVersionNew = substr($in,strpos($in,'#updateVersion#')+15);
        $upVersionNew = substr($upVersionNew,0,strpos($upVersionNew,'#'));
        if($updateVersion != $upVersionNew){
            // update Updater first!
            $updateUpdater = true;
        }
    }
    $in = substr($in,strpos($in,'#path#')+6);
    $remotePath = substr($in,0,strpos($in,'#'));
    $file = fopen($remotePath.'update/fileList.list','r');
    $remoteIn = fread($file,999999);
    fclose($file);
    $version = substr($remoteIn,strpos($remoteIn,'#version#')+9);
    $version = substr($version,0,strpos($version,'#'));
    $force = $_GET['forceUpdate'];
    if($version == $oldVersion && $force != 'true'){
        header('Location: ../admin.php');
        exit;
    }
    if(!file_exists('access.crypt')){
        copy('../access.crypt','access.crypt');
    }
    // TODO: update plugins after update.
?>
<!DOCTYPE html>
<html>
<head>
    <title>Updater</title>
    <link rel="SHORTCUT ICON" href="../images/editorLogo.png"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
        var max = 0;
        var timer;
        function updateUpdater(){
            $('.updateUpdater').removeClass('hidden');
            $.ajax({
                type: 'POST',
                url: 'copy.php',
                data: 'path=../update/copy.php&remotePath='+remotePath,
                success: function(data) {
                    if(data != "1"){
                        alert(data);
                    }
                    $.ajax({
                        type: 'POST',
                        url: 'copy.php',
                        data: 'path=../update/update.php&remotePath='+remotePath,
                        success: function(data) {
                            if(data != "1"){
                                alert(data);
                            }
                            window.location.href='update.php?forceUpdate=true&action=updateFileList';
                        }
                    });
                }
            });
        }
        function moveFilesNow(){
            max = files.length;
            $('.progressBar').removeClass('hidden');
            $('.progressBarUnder').removeClass('hidden');
            if(max == 0){
                $('.progressBar').html('100%').width(1020);
                $('.file').html('Update Complete');
                $('.data').html('');
            }else{
                timer = window.setTimeout('moveOneFile(1)',15000);
                moveOneFile(0);
            }
        }
        function init(){
            <?php
            if($updateUpdater === true){
            echo("updateUpdater();");
            }
            ?>
            if(files.length == 0){
                document.getElementsByClassName('button')[0].innerHTML = 'Package up to date.';
                document.getElementsByClassName('button')[1].innerHTML = '<a href="../admin.php">Leave Update</a>';
            }
        }
        function moveOneFile(i){
            $('.file').html('Copying file '+files[i].substr(2));
            try{
                window.clearTimeout(timer);
            }catch (ex){}
            timer = window.setTimeout('moveOneFile('+(i+1)+')',15000);
            $.ajax({
                type: 'POST',
                url: 'copy.php',
                data: 'path='+files[i]+'&remotePath='+remotePath,
                success: function(data) {
                    window.clearTimeout(timer);
                    if(data!='1'){
                        $('.data').html(data);
                    }
                    if(i<max-1){
                        var prog = Math.round((i+1)*100/max)-1;
                        $('.progressBar').html(prog+'%').width(20+prog*10);
                        moveOneFile(i+1);
                    }else{
                        $('.file').html('finishing update...');
                        $.ajax({
                            type: 'POST',
                            url: 'finish.php',
                            data: 'version='+version,
                            success: function(data) {
                                if(data == '1'){
                                    $('.progressBar').html('100%').width(1020);
                                    $('.file').html('Update Complete');
                                    $('.data').html('');
                                    document.getElementsByClassName('button')[1].innerHTML = '<a href="../admin.php">Leave Update</a>';
                                }else{
                                    alert(data);
                                }
                            }
                        });
                    }
                }
            });
        }
        var files = [];
<?php
        echo("var remotePath = '$remotePath';
");
    if($oldVersion != $version){
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#file#'));
        $in = substr($in,strpos($in,'#file#'));
        $count = 0;
        while(strpos($remoteIn,'#file#')>-1){
            $remoteIn = substr($remoteIn,strpos($remoteIn,'#file#')+6);
            $path = substr($remoteIn,0,strpos($remoteIn,'#'));
            $add = true;
            if(strpos($remoteIn,'#date#') > -1){
                if(strpos($remoteIn,'#file#') > strpos($remoteIn,'#date#')){
                    if(strpos($in,$path) > -1){
                        $ktxt = substr($in,strpos($in,$path));
                        if(strpos($ktxt,'#date#')>-1){
                            $ktxt = substr($ktxt,strpos($ktxt,'#date#')+6);
                            $oldDate = substr($ktxt,0,strpos($ktxt,'#'));
                            $newDate = substr($remoteIn,strpos($remoteIn,'#date#')+6);
                            $newDate = substr($newDate,0,strpos($newDate,'#'));
                            if($oldDate == $newDate){
                                $add = false;
                            }
                        }
                    }
                }
            }
            if(strpos($in,$path) > -1){
                $ktxt = substr($in,0,strpos($in,$path)-6);
                $in = substr($in,strpos($in,$path));
                if(strpos($in,'#file#') > -1){
                    $in = substr($in,strpos($in,'#file#'));
                }else{
                    $in = '';
                }
                $in = $ktxt.$in;
            }
            if($add){
                echo("files[$count] = '$path';
    ");
                $count++;
            }
            $remoteIn = substr($remoteIn,strpos($remoteIn,'#')+1);
        }
        while(strpos($in,'#file#') > -1){
            $in = substr($in,strpos($in,'#file#')+6);
            $path = substr($in,0,strpos($in,'#'));
            if(file_exists($path)){
                unlink($path);
            }
        }
        echo("var version = '$version';
");
    }
?>
    </script>
    <style>
        .progressBar{
            position:absolute;
            transition:width 1s ease-out;
            -webkit-transition:width 1s ease-out;
            height:25px;
            top:2px;
            left:2px;
            background:#568C0A;
            text-align:center;
            padding:4px 4px 0 4px;
            border-radius:5px;
        }
        .progressBar.hidden{
            display:none;
        }
        .progressBarUnder{
            position:absolute;
            height:27px;
            width:1024px;
            border:1px solid #555;
            padding:4px 4px 0 4px;
            border-radius:5px;
        }
        .progressBarUnder.hidden{
            display:none;
        }
        .button{
            display:inline-flex;
            margin:1px;
            background:#F90;
            border-radius:3px;
            padding:2px;
            cursor:pointer;
        }
        .button:hover{
            background:#FF8000;
        }
        .button a{
            text-decoration:none;
            color:#000;
        }
        .button.green{
            background:#568C0A;
        }
        .button.green:hover{
            background:#88BE14;
        }
        .button.hidden{
            display:none;
        }
        .spacer{
            height:30px;
        }
        .updateUpdater{
            position: absolute;
            background: #fff;
            width: 100%;
            height: 100%;
        }
        .updateUpdater.hidden{
            display: none;
        }
    </style>
</head>
<body onload="init()">
<div class="updateUpdater hidden">Updater is being updated. Please wait.</div>
<div>
    <div class="pageTitle">Welcome to the update Panel.</div>
    <div class="button green" onclick="moveFilesNow();$(this).addClass('hidden')">Start Update</div>
    <div class="button"><a href="../admin.php">Skip Update</a></div>
    <div style="position:relative">
        <div class="progressBarUnder hidden"></div>
        <div class="progressBar hidden">0%</div>
    </div>
    <div class="spacer"></div>
    <div class="file"></div>
    <div class="data"></div>
</div>
</body>
</html>
<?php
}else{
    header('Location: ../admin.php');
}
?>