<!DOCTYPE html>
<html>
<head>
    <!-- todo: remove this in final!--><script src="../jquery-1.9.1.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
        var max = 0;
        function moveFilesNow(){
            max = files.length;
            $('.progressBars').removeClass('hidden');
            moveOneFile(0);
        }
        function moveOneFile(i){
            $('.file').html('Copying file '+files[i]);
            $.ajax({
                type: 'POST',
                url: 'copy.php',
                data: 'path='+files[i]+'&remotePath='+remotePath,
                success: function(data) {
                    if(data!='1'){
                        $('.data').html(data);
                    }
                    if(i<max){
                        var prog = Math.round(i*100/max);
                        $('.progressBar').html(prog+'%').width(20+prog*10);
                        moveOneFile(i+1);
                    }else{
                        $('.progressBar').html('100%').width(1020);
                    }
                }
            });
        }
        var files = [];
<?php
include 'access.php';
if(substr($authLevel,0,1) == '1'){
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    $file = fopen('fileList.list','r');
    $in = fread($file,200);
    fclose($file);
    $in = substr($in,strpos($in,'#path#')+6);
    $remotePath = substr($in,0,strpos($in,'#'));
    $file = fopen($remotePath.'update/fileList.list','r');
    $in = fread($file,999999);
    fclose($file);
    $version = substr($in,strpos($in,'#version#')+9);
    $version = substr($version,0,strpos($version,'#'));
    $in = substr($in,strpos($in,'#file#'));
    $count = 0;
    echo("var remotePath = '$remotePath';");
    while(strpos($in,'#')>-1){
        $in = substr($in,strpos($in,'#file#')+6);
        $path = substr($in,0,strpos($in,'#'));
        echo("files[$count] = '$path';
");$count++;
        $in = substr($in,strpos($in,'#')+1);
    }
}
?>
    </script>
    <style>
        .progressBar{
            position:absolute;
            transition:width 1s ease-out;
            -webkit-transition:width 1s ease-out;
            height:25px;
            background:#568C0A;
            text-align:center;
            padding:4px 4px 0 4px;
            border-radius:5px;
        }
        .progressBar.hidden{
            display:none;
        }
        .button{
            display:inline-flex;
            margin:1px;
            background:#F90;
            border-radius:3px;
            padding:2px;
        }
        .button.hidden{
            display:none;
        }
        .spacer{
            height:30px;
        }
    </style>
</head>
<body>
<div>
    <div class="button" onclick="moveFilesNow();$(this).addClass('hidden')">Start Update</div>
    <div class="progressBar hidden">0%</div>
    <div class="spacer"></div>
    <div class="file"></div>
    <div class="data"></div>
</div>
</body>
</html>