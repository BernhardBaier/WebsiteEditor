<?php
error_reporting(E_ERROR);
include 'auth.php';
if($authLevel == '1111'){
    $updateVersion = "2.9";
    $updateUpdater = false;
    if($_GET['action'] == 'updateFileList'){
        $file = fopen('fileList.list','r');
        $in = fread($file,filesize('fileList.list'));
        fclose($file);
        $in = substr($in,strpos($in,'#'));
        $description = "";
        if(strpos($in,'#description#')>-1){
            $in = substr($in,strpos($in,'#description#')+13);
            $description = '#description#'.substr($in,0,strpos($in,'#')+1);
            $in = substr($in,strpos($in,'#')+1);
        }
        $version = '#version#4#';
        if(strpos($in,'#version#')>-1){
            $in = substr($in,strpos($in,'#version#')+9);
            $version = '#version#'.substr($in,0,strpos($in,'#')+1);
            $in = substr($in,strpos($in,'#')+1);
        }
        if(strpos($in,'#updateVersion#') > -1){
            $in = substr($in,strpos($in,'#updateVersion#')+15);
            $in = substr($in,strpos($in,'#')+1);
        }
        if(strpos($in,'#path#') > -1){
            $in = substr($in,strpos($in,'#path#')+6);
            $path = '#path#'.substr($in,0,strpos($in,'#')+1);
            $in = substr($in,strpos($in,'#')+1);
        }
        if(strpos($in,'#') < strpos($in,'#file#')){
            $in = substr($in,strpos($in,'#file#'));
        }
        $in = $description.$version.'
#updateVersion#'.$updateVersion.'#'.$path.$in;
        $file = fopen('fileList.list','w');
        fwrite($file,$in);
        fclose($file);
    }
    $file = fopen('fileList.list','r');
    $in = fread($file,filesize('fileList.list'));
    fclose($file);
    $oldVersion = '4';
    if(strpos($in,'#version#')>-1){
        $in = substr($in,strpos($in,'#version#')+9);
        $oldVersion = substr($in,0,strpos($in,'#'));
        $in = substr($in,strpos($in,'#')+1);
    }
    $remotePath = false;
    if(strpos($in,'#path#')>-1){
        $in = substr($in,strpos($in,'#path#')+6);
        $remotePath = substr($in,0,strpos($in,'#'));
        $in = substr($in,strpos($in,'#')+1);
    }
    if($remotePath == false){
        die('files corrupted!');
    }
    copy($remotePath.'update/fileList.list','workList.list');
    $file = fopen('workList.list','r');
    $remoteIn = fread($file,filesize('workList.list'));
    fclose($file);
    unlink('workList.list');
    if(strpos($remoteIn,'#updateVersion#') > -1){
        $upVersionNew = substr($remoteIn,strpos($remoteIn,'#updateVersion#')+15);
        $upVersionNew = substr($upVersionNew,0,strpos($upVersionNew,'#'));
        if($updateVersion != $upVersionNew){
            // update Updater first!
            $updateUpdater = true;
        }
    }
    $description = "";
    if(strpos($remoteIn,'#description#')>-1){
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#description#')+13);
        $description = substr($remoteIn,0,strpos($remoteIn,'#'));
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#')+1);
    }
    $version = '4';
    if(strpos($remoteIn,'#version#')>-1){
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#version#')+9);
        $version = substr($remoteIn,0,strpos($remoteIn,'#'));
        $remoteIn = substr($remoteIn,strpos($remoteIn,'#')+1);
    }
    copy($remotePath.'update/versions.des','workVersions.des');
    $file = fopen('workVersions.des','r');
    $desFile = fread($file,filesize('workVersions.des'));
    fclose($file);
    unlink('workVersions.des');
    if(!(strpos($desFile,$version) > -1) && $desFile != ''){
        $desFile .= "
#version#$version#$description#";
    }
    if(strpos($desFile,$oldVersion) > -1){
        $desFile = substr($desFile,strpos($desFile,$oldVersion)-1);
        $description = '';
        while(strpos($desFile,'#version#') > -1){
            $desFile = substr($desFile,strpos($desFile,'#version#')+9);
            $ver = substr($desFile,0,strpos($desFile,'#'));
            $desFile = substr($desFile,strpos($desFile,'#')+1);
            $des = substr($desFile,0,strpos($desFile,'#'));
            $desFile = substr($desFile,strpos($desFile,'#')+1);
            $description .= "<div class='description version'>$ver</div>$des";
        }
    }
    $force = $_GET['forceUpdate'];
    if($version == $oldVersion && $force != 'true'){
        header('Location: ../admin.php');
        exit;
    }
    if(!file_exists('access.crypt')){
        copy('../access.crypt','access.crypt');
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Updater</title>
    <link rel="SHORTCUT ICON" href="../images/editorLogo.png"/>
    <link rel="stylesheet" href="style.css"/>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="../plugins/settings/script.js"></script>
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
                            $.ajax({
                                type: 'POST',
                                url: 'copy.php',
                                data: 'path=../update/style.css&remotePath='+remotePath,
                                success: function(data) {
                                    $.ajax({
                                        type: 'POST',
                                        url: 'copy.php',
                                        data: 'path=../update/versions.des&remotePath='+remotePath,
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
                $('.file').html('Copying files:<br>');
                moveOneFile(0);
                var maxThreads = 2;
                for(var j=1;j<maxThreads;j++){
                    window.setTimeout('moveOneFile(nextFileToMove)',500*j);
                }
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
        function leaveUpdate(){
            $.ajax({
                type: 'POST',
                url: '../plugins/settings/functions.php',
                data: 'function=updateAllPlugins',
                success: function(data) {
                    if(data != '1'){
                        alert(data);
                    }else{
                        window.location = '../admin.php';
                    }
                }
            });
        }
        var nextFileToMove = 0;
        var threadsRunning = 0;
        var filesMoved = 0;
        function moveOneFile(i){
            if(i==-1){
                i=nextFileToMove;
            }
            threadsRunning++;
            nextFileToMove = i + 1;
            $('.file').html($('.file').html()+files[i].substr(3)+'<br>');
            try{
                window.clearTimeout(timer);
            }catch (ex){}
            timer = window.setTimeout('moveOneFile('+(nextFileToMove)+')',15000);
            $.ajax({
                type: 'POST',
                url: 'copy.php',
                data: 'path='+files[i]+'&remotePath='+remotePath,
                success: function(data) {
                    threadsRunning--;
                    window.clearTimeout(timer);
                    if(data!='1'){
                        $('.data').html(data);
                    }
                    filesMoved++;
                    if(nextFileToMove < max){
                        var prog = Math.round((filesMoved)*100/max)-1;
                        $('.progressBar').html(prog+'%').width(20+prog*10);
                        moveOneFile(-1);
                    }else if(threadsRunning == 0){
                        $.ajax({
                            type: 'POST',
                            url: 'copy.php',
                            data: 'path=../update/fileList.list&remotePath='+remotePath,
                            success: function(data) {
                                if(data != "1"){
                                    alert(data);
                                }
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
                                            document.getElementsByClassName('button')[1].innerHTML = '<a href="javascript:leaveUpdate()">Leave Update</a>';
                                        }else{
                                            alert(data);
                                        }
                                    }
                                });
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
            if(!file_exists($path)){
                $add = true;
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
        echo("var version = '$version';
");
    }
?>
    </script>
</head>
<body onload="init()">
<div class="container" align="center">
    <div class="updateUpdater hidden">Updater is being updated. Please wait.</div>
    <div class="pageTitle">Welcome to the update Panel.<?php if($oldVersion!=$version){echo("<br>Your WebsiteEditor will be updated from Version $oldVersion to $version:");}?></div>
    <div class="description"><?php  if($oldVersion!=$version){echo($description);}?></div>
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