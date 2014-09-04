<?php
error_reporting(E_ERROR);
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 01.08.14
 * Time: 19:53
 */
if(!function_exists('encrypt')){
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
}
if(!function_exists('decrypt')){
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
}
if(isset($_POST['pass'])){
	$datei = fopen('../access.crypt','r');
	$in = fread($datei,filesize('../access.crypt'));
	fclose($datei);
	$in = decrypt($in,'2t8yamSQupnBd47s2j4n');
	$in = substr($in,6);
	$sqlBase = substr($in,0,strpos($in,'#'));
	$in = substr($in,strpos($in,'#')+6);
	$sqlUser = substr($in,0,strpos($in,'#'));
	$in = substr($in,strpos($in,'#')+6);
	$sqlPass = substr($in,0,strpos($in,'#'));
	$in = substr($in,strpos($in,'#')+6);
	$sqlHost = substr($in,0,strpos($in,'#'));
	$in = '';
	$hostname = $_SERVER['HTTP_HOST'];
	$host = $hostname == 'localhost'?$hostname:$sqlHost;
	$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
	$que = "SELECT * FROM users WHERE 1";
	$erg = mysqli_query($sql,$que);
	$userExists = false;
	while($row = mysqli_fetch_array($erg)){
		if($row["name"] != ""){
			$userExists = true;
		}
	}
	if(!$userExists){
		$name = $_POST['name'];
		$pass = $_POST['pass'];
		$pass2 = $_POST['pass2'];
		$mail = $_POST['mail'];
		if($pass != $pass2 || strlen($pass) < 6 || strlen($name) < 4 || strlen($mail) < 6){
			echo('0');
			exit;
		}
		$name = encrypt($name,'C3zyK5Uu3zdmgE6pCFB8');
		$pass = encrypt('access',$pass);
		$mail = encrypt($mail,'C3zyK5Uu3zdmgE6pCFB8');
		$access = encrypt('1111','C3zyK5Uu3zdmgE6pCFB8');
		$hostname = $_SERVER['HTTP_HOST'];
		$host = $hostname == 'localhost'?$hostname:$sqlHost;
		$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
		$que = "CREATE TABLE `".$sqlBase."`.`users` (`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`user` VARCHAR( 150 ) NULL ,`pw` VARCHAR( 150 ) NULL ,`access` VARCHAR( 150 ) NULL ,`email` VARCHAR( 150 ) NULL ,`reg` VARCHAR( 150 ) NULL, `ondate` VARCHAR(150) NULL, `extra` VARCHAR(150) NULL);";
		mysqli_query($sql, $que);
		$que = "INSERT INTO `".$sqlBase."`.`users` (`user`,`pw`,`access`,`email`,`reg`,`ondate`) VALUES ('$name','$pass','$access','$mail','".date('d.m.Y')."','-')";
		mysqli_query($sql, $que) or die (mysqli_error($sql));
		echo('1');
	}else{
        echo('0');
    }
}else{
?>
<script>
function back(){
	location.href = "setup.php?id=sql";
}
function createUser(){
	var elm = document.user;
	$.ajax({
		type: 'POST',
		url: 'setup/user.php',
		data: 'name='+elm.name.value+"&mail="+elm.mail.value+"&pass="+elm.pass.value+"&pass2="+elm.pass2.value,
		success: function(data) {
			window.setTimeout("hideNoteBox()",3000);
			if(data == '1'){
				document.getElementsByClassName('noteBox')[0].innerHTML = 'The admin has been added.';
				document.getElementsByClassName('noteBox')[0].className = "noteBox";
				window.setTimeout('location.href = "setup.php?id=login";',1500);
			}else{
                if(data == '0'){
                    document.getElementsByClassName('noteBox')[0].innerHTML = 'A user is already existent!';
                }else{
                    alert(data);
                    document.getElementsByClassName('noteBox')[0].innerHTML = 'The user could not be added.';
                }
                document.getElementsByClassName('noteBox')[0].className = "noteBox error";
            }
		}
	});
}
function hideNoteBox(){
	$('.noteBox').addClass('opac0');
	window.setTimeout("$('.noteBox').addClass('hidden');",900);
}
</script>
<div class="noteBox opac0 hidden"></div>
<div class="pageNav"><div class="pageNav navCount"><div class="pageNav navCountInner"><div class="pageNav icon"></div><div class="pageNav icon"></div><div class="pageNav icon active"></div><div class="pageNav icon"></div></div></div></div>
<div class="pageTitle">Please create your main user.</div>
<div class="content" align="center">
	<form name="user" action="javascript:createUser()">
		<table>
			<tr>
				<td>Username</td>
				<td><input type="text" name="name" required></td>
			</tr>
			<tr>
				<td>E-Mail</td>
				<td><input type="email" name="mail" required></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="pass" required></td>
			</tr>
			<tr>
				<td>Repeat</td>
				<td><input type="password" name="pass2" required></td>
			</tr>
		</table>
		<input type="submit" style="display: none;" />
	</form>
</div>
<div class="buttonSet"><div class="buttonLeft" onclick="back()">back</div><div class="buttonRight" onclick="createUser()">continue</div></div>
<?php
}
?>