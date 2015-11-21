<?php
error_reporting(E_ERROR);
$hostname = $_SERVER['HTTP_HOST'];
function decrypt($encrypted, $password, $salt='!kQm*fF3pXe1Kbm%9') {
    // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
    $key = hash('SHA256', $salt . $password, true);
    // Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
    $iv = base64_decode(substr($encrypted, 0, 22) . '==');
    // Remove $iv from $encrypted.
    $encrypted = substr($encrypted, 22);
    // Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
    $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
    // Retrieve $hash which is the last 32 characters of $decrypted.
    $hash = substr($decrypted, -32);
    // Remove the last 32 characters from $decrypted.
    $decrypted = substr($decrypted, 0, -32);
    // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
    if (md5($decrypted) != $hash) return false;
    // Yay!
    return $decrypted;
}
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
include "access.php";
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
$sslPath = '';
$que = "SELECT * FROM settings WHERE parameter='sslPath'";
$erg = mysqli_query($sql,$que);
$redirected = 'false';
while($row = mysqli_fetch_array($erg)){
    $sslPath = $row['value'];
}
if($_SERVER['SERVER_PORT'] != '443'){
    if($sslPath != 'none'){
        header('Location: '.$sslPath.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
    }
}else{
    if($sslPath == 'none'){
        header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
    }
}
$editorVersion = '4.0';
function checkTables(){
    global $sqlBase,$sqlUser,$sqlPass,$sqlHost,$editorVersion;
    $base = $sqlBase;
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);

    $que = "CREATE TABLE `".$base."`.`pages_de`(`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`name` VARCHAR( 150 ) NULL ,`rank` INT( 10 ) NULL ,`child` VARCHAR( 150 ) NULL ,
    `childCount` int( 10 ) NULL ,`parent` int( 10 ) NULL, `extra` VARCHAR(150) NULL, `created` VARCHAR(150) NULL, `edit` VARCHAR(150) NULL);";
	$tablesExistent = mysqli_query($sql, $que) or false;
	if($tablesExistent != false){
		$que = "INSERT INTO $base.pages_de (`name`, `parent`, `rank`,`created`,`edit`) VALUES ('Startseite','0','1','".date('d.m.Y')."','-');";
		mysqli_query($sql, $que) or die(mysqli_error($sql));
	}

    $que = "CREATE TABLE `".$base."`.`pages_en`(`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`name` VARCHAR( 150 ) NULL ,`rank` INT( 10 ) NULL ,`child` VARCHAR( 150 ) NULL ,
    `childCount` int( 10 ) NULL ,`parent` int( 10 ) NULL, `extra` VARCHAR(150) NULL, `created` VARCHAR(150) NULL, `edit` VARCHAR(150) NULL);";
	$tablesExistent = mysqli_query($sql, $que) or false;
	if($tablesExistent != false){
		$que = "INSERT INTO $base.pages_en (`name`, `parent`, `rank`,`created`,`edit`) VALUES ('Home','0','1','".date('d.m.Y')."','-');";
		mysqli_query($sql, $que) or die(mysqli_error($sql));
	}

    $que = "CREATE TABLE `".$base."`.`uploads`(`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`name` VARCHAR( 100 ) NULL ,`uploader` VARCHAR( 100 ) NULL ,`date` VARCHAR( 150 ) NULL ,`page` VARCHAR( 100 ) NULL ,
    `extra` VARCHAR(150) NULL);";
    mysqli_query($sql, $que);

    $que = "CREATE TABLE `".$base."`.`calendar_de`(`id` INT( 100 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`year` INT( 10 ) NULL ,`month` INT( 10 ) NULL ,`day` INT( 10 ) NULL ,`name` VARCHAR( 150 ) NULL ,
    `start` VARCHAR( 150 ) NULL,`end` VARCHAR( 150 ) NULL,`place` VARCHAR( 150 ) NULL,`href` VARCHAR( 150 ) NULL);";
    mysqli_query($sql, $que);

    $que = "CREATE TABLE `".$base."`.`calendar_en`(`id` INT( 100 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`year` INT( 10 ) NULL ,`month` INT( 10 ) NULL ,`day` INT( 10 ) NULL ,`name` VARCHAR( 150 ) NULL ,
    `start` VARCHAR( 150 ) NULL,`end` VARCHAR( 150 ) NULL,`place` VARCHAR( 150 ) NULL,`href` VARCHAR( 150 ) NULL);";
    mysqli_query($sql, $que);

    $que = "CREATE TABLE `".$base."`.`plugins`(`id` INT( 100 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`name` VARCHAR( 150 ) NULL ,`location` VARCHAR( 150 ) NULL ,`includes` VARCHAR( 150 ) NULL ,`relations` VARCHAR( 150 ) NULL ,`extra` VARCHAR( 150 ) NULL);";
	mysqli_query($sql, $que);

	$que = "CREATE TABLE `".$base."`.`toreplace`(`id` INT( 100 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`replace` VARCHAR( 150 ) NULL ,`url` VARCHAR( 150 ) NULL ,`extra` VARCHAR( 150 ) NULL);";
	mysqli_query($sql, $que);

    $que = "SELECT * FROM settings WHERE parameter='editorVersion'";
    $erg = mysqli_query($sql,$que);
    $editorVersion = false;
    while($row = mysqli_fetch_array($erg)){
        $editorVersion = $row['value'];
    }
    if($editorVersion == false){
        $que = "INSERT INTO settings (parameter, value) VALUES ('editorVersion', '4.1')";
        mysqli_query($sql,$que);
    }
    $que = "SELECT * FROM settings WHERE parameter='languages'";
    $erg = mysqli_query($sql,$que);
    $languages = false;
    while($row = mysqli_fetch_array($erg)){
        $languages = $row['value'];
    }
    if($languages == false){
        $languages = array('de','en');
        $longLanguages = array('deutsch','english');
        $que = "INSERT INTO settings (parameter, value) VALUES ('languages', '".serialize($languages)."')";
        mysqli_query($sql,$que);
        $que = "INSERT INTO settings (parameter, value) VALUES ('languagesLong', '".serialize($longLanguages)."')";
        mysqli_query($sql,$que);
        $que = "INSERT INTO settings (parameter, value) VALUES ('languageSupport', 'single')";
        mysqli_query($sql,$que);
    }
}
checkTables();
session_start();
if($_SESSION['authlevel'] != '0000' && $_SESSION['authlevel'] != '') {
    header('Location: admin.php');
}
if(!is_dir("web-images/")){
    mkdir("web-images/");
}
if(!is_dir("web-others/")){
    mkdir("web-others/");
}
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $passwort = $_POST['passwort'];
    $que = "SELECT * FROM users WHERE 1";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    if($erg){
        $aUser = '';
        while($row = mysqli_fetch_array($erg)){
            $out = decrypt($row['pw'],$passwort);
            $user = decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8');
            if($out == 'access' && $user == $username){
                $path = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
                if($sslPath == 'none' || $sslPath == ""){}else{
	                $path = str_replace($sslPath,'','https://'.$path);
                }
                $path = 'http://'.substr($path,0,strrpos($path,'/'));
                $aUser = $row['user'];
                $auth = decrypt($row['access'],'C3zyK5Uu3zdmgE6pCFB8');
                $info[0] = $auth;
                $info[1] = $user;
                $info[2] = $row['id'];
                $info[3] = $row['extra'];
                $cookie = encrypt(serialize($info),$ip);
                $cookie = encrypt($cookie,session_id());
                $sessId = session_id();
                $redirected = $_SESSION['redirect'];
                echo('Login succeed. redirecting you now.');
                echo("<form name='form1' action='$path/redirect.php' method='post'>");
                echo("<input type='hidden' name='redirect' value='true' />");
                echo("<input type='hidden' name='auth' value='$cookie' />");
                echo("<input type='hidden' name='PHPSESSID' value='$sessId' />");
                if ($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1') {
                    if (php_sapi_name() == 'cgi') {
                        header('Status: 303 See Other');
                    }
                    else {
                        header('HTTP/1.1 303 See Other');
                    }
                    $que = "UPDATE users SET ondate = '".date('d.m.Y')."' WHERE user='".$aUser."'";
                    $erg = mysqli_query($sql, $que) or die(mysqli_error($sql));
                    mysqli_free_result($erg);
                    $que = "SELECT * FROM settings WHERE parameter='autoUpdate'";
                    $erg = mysqli_query($sql,$que);
                    $autoUpdate = false;
                    while($row = mysqli_fetch_array($erg)){
                        $autoUpdate = $row['value'];
                    }
                    mysqli_free_result($erg);
                    if($autoUpdate !== false){
                        echo("<input type='hidden' name='update' value='true' />");
                    }
                    echo("<input type='submit' value='redirect' />
                    </form>
                    <script>document.form1.submit();</script>");
                }
            }
        }
        mysqli_free_result($erg);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Website Editor <?php echo($editorVersion);?></title>
    <link rel="SHORTCUT ICON" href="images/editorLogo.png"/>
    <link rel="stylesheet" href="styleLogin.min.css" />
</head>
<body>
<?php
echo('<span class="loginTitle">Website editor Version '.$editorVersion.'</br>Copyright &copy; 2014 - '.date('Y').' Bernhard Baier</span>');
if(isset($_POST['passwort']) && $redirected != 'true'){
    echo('<div class="loginBox">Wrong login data!</div>');
}
?>
<div class="login">
    Login
    <table width="100%">
        <form action="login.php" method="post">
            <tr><td>Username:</td><td><input type="text" name="username" /></td></tr><tr><td>Passwort:</td><td><input type="password" name="passwort" /></td></tr>
            <tr><td colspan="2" align="center"><input type="submit" value="Login" /></td></tr>
        </form>
        <?php
        if($_GET['register'] == 'true'){
            echo('<tr><td colspan="2" align="center"><a href="register.php">register</a></td></tr>');
        }
        ?>
    </table>
</div>
</body>
</html>