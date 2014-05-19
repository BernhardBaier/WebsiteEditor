<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 15.02.14
 * Time: 21:17
 */
error_reporting(E_ERROR);
include 'access.php';
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
if(substr($authLevel,0,1) == "1"){
    $base = $sqlBase;
    $table = 'users';
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
    $name = $_SESSION['user'];
    if(!$sql){
        echo('connection failed');
    }else{
        $que = "SELECT * FROM ".$table." WHERE 1";
        $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        $user = '';
        while($row = mysqli_fetch_array($erg)){
            if(decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8') == $name){
                $user = $row['user'];
            }
        }
        mysqli_free_result($erg);
        $que = "UPDATE `".$base."`.`".$table."` SET extra='tour' WHERE user='".$user."'";
        $_SESSION['extra'] = 'tour';
        echo(mysqli_query($sql, $que) or mysqli_error($sql));
    }
}