<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 25.01.14
 * Time: 14:27
 */
error_reporting(E_ERROR);
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
$pw = $_POST['password'];
$pw2 = $_POST['password2'];
$name = $_POST['username'];
$mail = $_POST['mail'];
include('access.php');
$hostname = $_SERVER['HTTP_HOST'];
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);$que = "SELECT * FROM settings WHERE parameter='editorVersion'";
$erg = mysqli_query($sql,$que);
$editorVersion = '4.0';
while($row = mysqli_fetch_array($erg)){
    $editorVersion = $row['value'];
}
$access = '0000';
$errorString = "";
if($name != ""){
	$wrongLetter = false;
	$umlaute = Array('ä','ö','ü','Ä','Ö','Ü','ß');
	for($i=0;$i<7;$i++){
		if(strpos($pw,$umlaute[$i]) > -1){
			$wrongLetter = true;
		}else if(strpos($name,$umlaute[$i]) > -1){
			$wrongLetter = true;
		}else if(strpos($mail,$umlaute[$i]) > -1){
			$wrongLetter = true;
		}
	}
    if(strlen($pw) > 4 && strlen($name) > 4 && strlen($mail) > 4 && $pw == $pw2 && $wrongLetter == false){
        $path = dirname($_SERVER['PHP_SELF']);
        $que = "SELECT * FROM users WHERE 1";
        $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        while($row = mysqli_fetch_array($erg)){
            if(decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8') == $name){
                $errorString = 'User already existent';
                goto ende;
            }
        }
        mysqli_free_result($erg);
        $name = encrypt($name,'C3zyK5Uu3zdmgE6pCFB8');
        $pw = encrypt('access',$pw);
        $mail = encrypt($mail,'C3zyK5Uu3zdmgE6pCFB8');
        $access = encrypt($access,'C3zyK5Uu3zdmgE6pCFB8');
        $que = "INSERT INTO `$sqlBase`.`users` (`user`, `pw`, `access`, `email`, `reg`, `ondate`) VALUES ('$name','$pw','$access','$mail','".date('d.m.Y')."','".'-'."');";
        $out = mysqli_query($sql, $que) or die(mysqli_error($sql));
        if($out == '1'){
            header('Location: login.php');
        }
        ende:
    }else{
        $errorString = 'Error: ';
        $errorString .= strlen($name) < 5?'Name to short! ':'';
        $errorString .= strlen($pw) < 5?'Password to short! ':'';
        $errorString .= strlen($mail) < 5?'Mail to short! ':'';
        $errorString .= $pw!=$pw2?'Passwords inconsistent! ':'';
        if($wrongLetter == true ){
            $errorString .= 'use only english chars!';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="SHORTCUT ICON" href="images/logo.png"/>
    <link rel="stylesheet" href="styleLogin.css" />
</head>
<body>
<span class="loginTitle">Website editor Version <?php echo($editorVersion);?></br>Copyright &copy; 2014 Bernhard Baier</span>
<div class="login">
    Register
    <form action="register.php" method="post">
        <table width="100%">
            <tr>
                <td>Username:</td><td><input type="text" required name="username" value="<?php echo($name);?>" /></td>
            </tr>
            <tr>
                <td>Password:</td><td><input type="password" required name="password" /></td>
            </tr>
            <tr>
                <td>Repeat:</td><td><input type="password" required name="password2" /></td>
            </tr>
            <tr>
                <td>E-Mail:</td><td><input type="email" required name="mail" value="<?php echo($mail);?>" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><input type="submit" value="Register" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center"><?php echo($errorString);?></td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>