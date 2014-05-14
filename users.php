<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 19.01.14
 * Time: 16:07
 */
error_reporting(E_ERROR);
include('auth.php');

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

if(substr($authLevel,2,1) == '1'){
    include('access.php');
    $base = $sqlBase;
    $table = $_POST['table'];
    $function = $_POST['function'];
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:$sqlHost;
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
    $backup = mysqli_connect($host,$sqlUser,$sqlPass,$base.'_backup');
    if(!$sql){
        echo('connection failed');
    }else{
        if($function == ''){
            echo('<table border="1"><tr><td>User name</td><td>access rights</td><td>E-Mail</td><td>reg date</td><td>on date</td><td>action</td></tr>');
            $que = "SELECT * FROM ".$table." WHERE 1";
            $erg = mysqli_query($sql,$que);
            while($user = mysqli_fetch_array($erg)){
                $access = decrypt($user['access'],'C3zyK5Uu3zdmgE6pCFB8');
                $accessOut = '';
                if($access=='1111'){
                    $accessOut = 'admin';
                }else{
                    if(substr($access,0,1) == '1'){
                        $accessOut .= 'editor';
                    }
                    if(substr($access,1,1) == '1'){
                        $accessOut .= ',reporter';
                    }
                    if(substr($access,2,1) == '1'){
                        $accessOut .= ',sub admin';
                    }
                    if(substr($access,3,1) == '1'){
                        $accessOut .= ',coder';
                    }
                }
                $accessOut = $accessOut == ''?'no rights':$accessOut;
                $accessOut = substr($accessOut,0,1) == ','?substr($accessOut,1):$accessOut;
                $Auser = decrypt($user['user'],'C3zyK5Uu3zdmgE6pCFB8');
                echo('<tr><td>'.$Auser.'</td><td>'.$accessOut.'</td><td>'.decrypt($user['email'],'C3zyK5Uu3zdmgE6pCFB8').'</td>
                <td>'.$user['reg'].'</td><td>'.$user['ondate'].'</td>');
                if($accessOut == 'admin'){
                    echo('<td> </td>');
                }else{
                    echo('<td>&nbsp;<img src="images/key.png" title="set rights" onclick="showSetRights(\''.$Auser.'\',\''.$access.'\')" height="18" />&nbsp;
<img title="delete user" src="images/bin.png" onclick="deleteUser(\''.$Auser.'\')" height="18" />&nbsp;</td>');
                }
                echo('</tr>');
            }
            echo('</tr></table>');
        }
    }
}