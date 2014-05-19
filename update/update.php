<?php
error_reporting(E_ERROR);
include 'auth.php';
if(substr($authLevel,0,1) == '1'){
    $file = fopen('fileList.list','r');
    $in = fread($file,filesize('fileList.list'));
    fclose($file);
    $oldVersion = substr($in,strpos($in,'#version#')+9);
    $oldVersion = substr($oldVersion,0,strpos($oldVersion,'#'));
    $in = substr($in,strpos($in,'#path#')+6);
    $remotePath = substr($in,0,strpos($in,'#'));
    $file = fopen($remotePath.'update/fileList.list','r');
    $in = fread($file,999999);
    fclose($file);
    $version = substr($in,strpos($in,'#version#')+9);
    $version = substr($version,0,strpos($version,'#'));
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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
        var max = 0;
        var timer;
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
            if(files.length == 0){
                document.getElementsByClassName('button')[0].innerHTML = 'Package up to date.';
                document.getElementsByClassName('button')[1].innerHTML = '<a href="../admin.php">Leave Update</a>';
            }
        }
        function moveOneFile(i){
            $('.file').html('Copying file '+files[i]);
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
                        var prog = Math.round(i*100/max);
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
    if($oldVersion != $version){
        $in = substr($in,strpos($in,'#file#'));
        $count = 0;
        echo("var remotePath = '$remotePath';
");
        while(strpos($in,'#')>-1){
            $in = substr($in,strpos($in,'#file#')+6);
            $path = substr($in,0,strpos($in,'#'));
            echo("files[$count] = '$path';
    ");$count++;
            $in = substr($in,strpos($in,'#')+1);
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
        .button a{
            text-decoration:none;
            color:#000;
        }
        .button.hidden{
            display:none;
        }
        .button:hover{
            background:#FF8000;
        }
        .spacer{
            height:30px;
        }
    </style>
</head>
<body onload="init()">
<div>
    <div class="button" onclick="moveFilesNow();$(this).addClass('hidden')">Start Update</div>
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
}
?>