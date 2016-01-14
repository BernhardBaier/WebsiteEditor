<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 28.05.14
 * Time: 16:24
 */
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
error_reporting(E_ERROR);
@session_start();
if($_POST['redirect'] == 'true'){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $cookie = $_POST['auth'];
    $sessId = $_POST['PHPSESSID'];
    setcookie('PHPSESSID',$sessId,time()+3600);
    $cookie = decrypt($cookie,$sessId);
    $cookie = decrypt($cookie,$ip);
    if($cookie){
        $info = unserialize($cookie);
    }
    if(sizeof($info) == 4){
        $_SESSION['authlevel'] = $info[0];
        $_SESSION['user'] = $info[1];
        $_SESSION['id'] = $info[2];
        $_SESSION['extra'] = $info[3];
        $_SESSION['ip'] = $ip;
        $_SESSION['redirect'] = 'redirected';
        $path = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $path = 'http://'.substr($path,0,strrpos($path,'/'));
        if($_POST['update'] == 'true'){
            $_SESSION['checkUpdates'] = "true";
        }
        header("Location: $path/admin.php");
        exit;
    }
}else{
    include "auth.php";
}
header('Location: logout.php');