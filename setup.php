<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 09.03.14
 * Time: 18:34
 */
//error_reporting(E_ERROR);
function encrypt($decrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
    // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
    $key = hash('SHA256', $salt . $password, true);
    // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
    srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
    if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
    // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
    $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
    // We're done!
    return $iv_base64 . $encrypted;
}
if($_GET['action'] == 'deleteLogo'){
    unlink('images/logo.png');
    header('Location:setup.php');
    exit;
}
if($_GET['action'] == 'upload'){
    if(substr(basename($_FILES['upfile']['name']),-3) == 'png'){
        $uploadFile = 'upload/logo.png';
        if(move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadFile)) {
            header('Location: setup.php?action=moveFile');
            exit;
        }
    }
}
if($_GET['action'] == 'moveFile'){
    if(file_exists('upload/logo.png')){
        if(copy('upload/logo.png','images/logo.png')){
            unlink('upload/logo.png');
            header('Location: setup.php');
            exit;
        }
    }
}
$pageTitle = 'no title';
$editorVersion = '4.1';
if(file_exists('access.crypt')){
    include 'access.php';
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
    if(!$sql){
        echo('sql error');
        exit;
    }
    $que = "SELECT * FROM settings WHERE parameter='pageTitle'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $pageTitle = $row['value'];
    }
    $que = "SELECT * FROM settings WHERE parameter='editorVersion'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $editorVersion = $row['value'];
    }
    $multiLang = "";
    $que = "SELECT * FROM settings WHERE parameter='languageSupport'";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        $multiLang = $row['value'];
    }
    $multiLang = $multiLang == 'multi'?' checked':'';
}
if(isset($_POST['sql'])){
    if($_POST['sql'] != ''){
        $sqlHost = $_POST['host'];
        $sqlUser = $_POST['user'];
        $sqlPass = $_POST['pw'];
        $sqlBase = $_POST['base'];
        $hostname = $_SERVER['HTTP_HOST'];
        $host = $hostname == 'localhost'?$hostname:$sqlHost;
        $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
        if(!$sql){
            echo('<div class="noteBox error">SQL DATA wrong!</div>');
        }else{
            $output = encrypt("#base#$sqlBase#user#$sqlUser#pass#$sqlPass#host#$sqlHost#end#",'2t8yamSQupnBd47s2j4n');
            $file = file_exists('access.crypt');
            unlink('access.crypt');
            $datei = fopen('access.crypt','w');
            fwrite($datei,$output);
            fclose($datei);
            chmod('access.crypt',0600);
            if($file){
                echo('<div class="noteBox">SQL DATA successfully changed</div>');
            }else{
                echo('<div class="noteBox">SQL DATA successfully changed</br>now you have login then you can set all other parameters.<br/>the default login parameters are both admin</br>press leave to login.</div>');
            }
        }
    }
}
if(isset($_POST['title'])){
    $pageTitle = $_POST['title'];
    $multiLang = $_POST['multiLang'];
    $multiLang = $multiLang=='on'?'multi':'single';
    $autoUpdate = $_POST['autoUpdate'];
    $autoUpdate = $autoUpdate=='on'?'on':'off';
    if(file_exists('access.crypt')){
        if(!$sql){
            include 'access.php';
            $hostname = $_SERVER['HTTP_HOST'];
            $host = $hostname == 'localhost'?$hostname:$sqlHost;
            $sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
        }
        $que = "SELECT * FROM settings WHERE parameter='pageTitle'";
        $erg = mysqli_query($sql,$que);
        $out = false;
        while($row = mysqli_fetch_array($erg)){
            $out = $row['value'];
        }
        if($out !== false){
            $que = "UPDATE settings SET value='$pageTitle' WHERE parameter='pageTitle'";
        }else{
            $que = "INSERT INTO settings (parameter, value) VALUES ('pageTitle', '$pageTitle')";
        }
        $erg = mysqli_query($sql,$que);
        $que = "SELECT * FROM settings WHERE parameter='languageSupport'";
        $erg = mysqli_query($sql,$que);
        $out = false;
        while($row = mysqli_fetch_array($erg)){
            $out = $row['value'];
        }
        if($out !== false){
            $que = "UPDATE settings SET value='$multiLang' WHERE parameter='languageSupport'";
        }else{
            $que = "INSERT INTO settings (parameter, value) VALUES ('languageSupport', '$multiLang')";
        }
        $erg = mysqli_query($sql,$que);
        mysqli_free_result($erg);
        $que = "SELECT * FROM settings WHERE parameter='autoUpdate'";
        $erg = mysqli_query($sql,$que);
        mysqli_free_result($erg);
        $out = false;
        while($row = mysqli_fetch_array($erg)){
            $out = $row['value'];
        }
        if($out !== false){
            $que = "UPDATE settings SET value='$autoUpdate' WHERE parameter='autoUpdate'";
        }else{
            $que = "INSERT INTO settings (parameter, value) VALUES ('autoUpdate', '$autoUpdate')";
        }
        $autoUpdate = $out;
        $erg = mysqli_query($sql,$que);
        echo('<div class="noteBox">settings successfully changed</div>');
    }else{
        echo('<div class="noteBox error">Title could not be changed (insert SQL DATA first!)</div>');
    }
    $multiLang=$multiLang=='multi'?'checked':'';
    $autoUpdate=$autoUpdate=='on'?'checked':'';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Website setup</title>
    <link rel="SHORTCUT ICON" href="images/editorLogo.png"/>
    <style>
        .container{
            margin:45px calc(50% - 250px);
        }
        .page{
            width:500px;
            min-height:250px;
            background:#EEE;
            border-radius:5px;
            border:1px solid #DDD;
        }
        .pageTitle{
            width:100%;
            text-align:center;
            font-size:25px;
            font-weight:bold;
            color:#333;
        }
        .noteBox{
            transition:opacity 1s;
            -webkit-transition:opacity 1s;
            position:absolute;
            text-align:center;
            width:490px;
            padding:5px;
            border-radius:5px;
            border:1px solid #070;
            background:#568C0A;
            left:calc(50% - 250px);
            top:5px;
        }
        .noteBox.error{
            border:1px solid #A00;
            background:#F66;
        }
        .opac0{
            opacity:0;
        }
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
        function init(){
            window.setTimeout('$(".noteBox").addClass("opac0")',2500);
        }
    </script>
</head>
<body onload="init()">
<div class="container">
    <div class="page">
        <div class="pageTitle">Welcome to Website Editor Version <?php echo($editorVersion);?></div>
        <div class="content" align="center">
            <?php
            if($_SESSION['authlevel'] != ""){
                if(!file_exists('images/logo.png')){
                    echo('<form action="setup.php?action=upload" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                            Logo: <input name="upfile" type="file" />
                            <input type="submit" value="upload" />
                        </form>');
                }else{
                    echo('Logo: <img src="images/logo.png" height="45" /> <input type="button" onclick="location.href=\'setup.php?action=deleteLogo\'" value="change"/>');
                }
            ?>
            <form action="setup.php" name="form1" method="post">
                <table>
                    <tr>
                        <td>Title</td>
                        <td><input type="text" required name="title" size="50" placeholder="Title of the website" value="<?php echo($pageTitle);?>"/></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><label title="available languages: de, en">Enable multi language support <input type="checkbox" <?php echo($multiLang);?> name="multiLang" /></label></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><label title="check for updates after login">Enable auto update <input type="checkbox" <?php echo($autoUpdate);?> name="autoUpdate" /></label></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><input type="button" onclick="document.form1.submit()" value="change"/></td>
                    </tr>
                </table>
            </form>
            <?php
            }
            ?>
            <form action="setup.php" name="form2" method="post">
                <table>
                    <tr>
                        <td colspan="2" align="center">SQL login data:</td>
                    </tr>
                    <tr>
                        <td>Basename</td>
                        <td><input type="text" required name="base" placeholder="sql basename" value="<?php echo($sqlBase);?>"/></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><input type="text" required name="user" placeholder="sql username" value="<?php echo($sqlUser);?>"/></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td><input type="password" name="pw" required placeholder="sql password"/></td>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><input type="text" required name="host" placeholder="sql host" value="<?php echo($sqlHost);?>"/></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><input type="hidden" value="sql" name="sql" ><input type="button" onclick="document.form2.submit()" value="change"/></td>
                    </tr>
                </table>
            </form>
            <input type="button" value="leave" onclick="location.href='login.php'"/>
        </div>
    </div>
</div>
</body>
</html>