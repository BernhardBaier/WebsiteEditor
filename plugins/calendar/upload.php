<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 17.12.14
 * Time: 22:23
 */
error_reporting(E_ERROR);
include('access.php');
if(substr($authLevel,0,1) == '1'){
    $lang = $_GET['lang'];
    if(!isset($_GET['lang'])){
        $lang = 'de';
    }
    $plugId = $_GET['plugId'];
?>
<html>
<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
        var lang = '<?php echo($lang);?>';
        function replaceUml(text){
            var umlaute = [['ä','ö','ü','Ä','Ö','Ü','ß','&',':'],['<und>auml;','<und>ouml;','<und>uuml;','<und>Auml;','<und>Ouml;','<und>Uuml;','<und>szlig;','<und>','<dpp>']];
            for(var i=0;i<umlaute[0].length;i++){
                while(text.search(umlaute[0][i]) > -1){
                    text=text.replace(umlaute[0][i],umlaute[1][i]);
                }
            }
            return text;
        }
        function addAll(max){
            for(var i=0;i<max;i++){
                document.getElementById('addEvent'+i).onclick();
            }
        }
        function init(){

        }
        function insertEvent(date,start,end,name,place,href){
            date = replaceUml(date);
            start = replaceUml(start);
            end = replaceUml(end);
            name = replaceUml(name);
            place = replaceUml(place);
            href = replaceUml(href);
            $.ajax({
                type: 'POST',
                url: 'calendar.php',
                data: 'function=insert:'+date+':'+start+':'+end+':'+name+':'+place+':'+href+'&lang='+lang,
                success: function(data) {
                    if(data == '1'){
                        parent.$('.overlayCalendar').removeClass('hidden').css('top',document.getElementsByClassName('pluginInner')[0].scrollTop);
                    }else{
                        alert(data);
                    }
                }
            });
        }
        function parsed(){
            parent.parsed();
        }
</script>
<style>
    .importOptions{
        position: absolute;
        z-index: 5;
        background: #90a4ae;
        border: 1px solid #37474f;
        padding: 3px;
        border-radius: 3px;
        width: 230px;
        top:2px;
    }
    .importOptions.hidden{
        display: none;
    }
    .event{
        display: inline-flex;
        width: 100%;
        position: relative;
        height: 21px;
        border: 1px solid #4fc3f7;
        border-radius: 2px;
        margin: 0 0 1px 0;
    }
    .addEvent{
        height: 18px;
        padding: 1px;
        border: 1px solid #03a9f4;
        background: #b3e5fc;
        border-radius: 2px;
        cursor: pointer;
        position: absolute;
        top: 0;
        right: 0;
    }
    .addEvent:hover{
        background: #03a9f4;
        border: 1px solid #b3e5fc;
    }
    .eventFooter{
        width: 100%;
        height: 25px;
        position: relative;
    }
    .but1{
        cursor: pointer;
        position: absolute;
        right: 0;
        padding: 2px;
        border-radius: 3px;
        border: 1px solid #004d40;
        background: #26a69a;
    }
    .but1:hover{
        background: #80cbc4;
    }
    .but2{
        cursor: pointer;
        position: absolute;
        left: 0;
        padding: 2px;
        border-radius: 3px;
        border: 1px solid #006064;
        background: #26c6da;
    }
    .but2:hover{
        background: #80deea;
    }
</style>
</head>
<?php
$base = $sqlBase;
$table = 'settings';
$hostname = $_SERVER['HTTP_HOST'];
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
if(!$sql){
    echo('SQL error');
}
if(isset($_GET['action'])){
    $defaultLocation = $_POST['defaultLocation'];
    $belongsTo = $_POST['belongsTo'];
    mkdir('upload');
    $path = 'upload/upload.txt';
    echo('<body onload="parsed()">');
    if(move_uploaded_file($_FILES['upfile']['tmp_name'], $path)) {
        include 'icalParser.php';
    }else{
        echo('error - retry later or retry another file');
    }
    $que = "UPDATE `".$base."`.`".$table."` SET value = '$defaultLocation' WHERE parameter='defaultLocation'";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
}else{
    $que = "SELECT * FROM ".$table." WHERE 1";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    $defaultLocation = false;
   while($row = mysqli_fetch_array($erg)){
        if($row['parameter'] == 'defaultLocation'){
            $defaultLocation = $row['value'];
        }
    }
    if($defaultLocation === false){
        $que = "INSERT INTO `".$base."`.`".$table."` (`parameter`, `value`) VALUES ('defaultLocation','Magazin Hardt');";
        $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        $defaultLocation = 'Magazin Hardt';
    }
    echo("<body onload='init()'>");
?>
<form action="upload.php?action=upload&lang=<?php echo($lang);?>&plugId=<?php echo($plugId);?>" method="post" enctype="multipart/form-data">
    <div class="importOptions hidden">
        Options:<br>
        <label>Default Location: <input name="defaultLocation" type="text" style="width:95px" value="<?php echo($defaultLocation);?>" /></label><br>
        <label>Repeat events till
            <select><option>end of year</option><option>two years</option></select>
        </label>
        <label>Belongs to
            <select name="belongsTo"><option>alle</option><option>aktive</option><option>jugend</option><option>Maschinisten</option><option>Atemschutz</option></select>
        </label>
    </div>
    <input type="hidden" name="MAX_FILE_SIZE" value="5000" />
    <img src="images/gear.png" style="position: absolute;cursor: pointer;left:250px" onclick="$('.importOptions').toggleClass('hidden')" />ICal File:<br>
    <input name="upfile" type="file" /><br>
    <input type="submit" value="parse file" />
</form>
<?php
}
?>
</body>
</html>
<?php
}