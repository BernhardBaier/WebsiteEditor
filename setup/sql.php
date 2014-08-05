<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 01.08.14
 * Time: 13:37
 */
error_reporting(E_ERROR);
if(basename($_SERVER["SCRIPT_FILENAME"]) != 'setup.php' && !isset($_POST['base'])){
	header('Location: ../setup.php');
}
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
if(isset($_POST['base'])){
	$sqlHost = $_POST['server'];
	$sqlUser = $_POST['user'];
	$sqlPass = $_POST['pass'];
	$sqlBase = $_POST['base'];
	$hostname = $_SERVER['HTTP_HOST'];
	$host = $hostname == 'localhost'?$hostname:$sqlHost;
	$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
	if(!$sql){
		echo('0');
	}else{
		$output = encrypt("#base#$sqlBase#user#$sqlUser#pass#$sqlPass#host#$sqlHost#end#",'2t8yamSQupnBd47s2j4n');
		$file = file_exists('../access.crypt');
		unlink('../access.crypt');
		$datei = fopen('../access.crypt','w');
		fwrite($datei,$output);
		fclose($datei);
		chmod('../access.crypt',0600);
		if(!$file){
			$que = "CREATE TABLE `".$sqlBase."`.`settings`(`id` INT( 100 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,`parameter` VARCHAR( 150 ) NULL ,`value` VARCHAR( 150 ) NULL ,`extra` VARCHAR( 150 ) NULL);";
			mysqli_query($sql, $que);
			$sslPath = $_POST['ssl'];
			$que = "INSERT INTO $sqlBase.settings (parameter,value) VALUES ('sslPath','$sslPath')";
			mysqli_query($sql,$que);
		}
		echo('1');
	}
}else{
?>
<script>
function back(){
	location.href = "setup.php";
}
function testSQL(){
	var sql = document.sql;
	$.ajax({
		type: 'POST',
		url: 'setup/sql.php',
		data: 'base='+sql.base.value+"&user="+sql.user.value+"&pass="+sql.pass.value+"&server="+sql.server.value+"&ssl=<?php echo($path);?>",
		success: function(data) {
			window.setTimeout("hideNoteBox()",3000);
			if(data == '1'){
				document.getElementsByClassName('noteBox')[0].innerHTML = 'The SQL Data has been successfully changed.';
				document.getElementsByClassName('noteBox')[0].className = "noteBox";
				window.setTimeout('location.href = "setup.php?id=user";',1500);
			}else{
				document.getElementsByClassName('noteBox')[0].innerHTML = 'The SQL Data seems to be incorrect.';
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
<div class="pageNav"><div class="pageNav navCount"><div class="pageNav navCountInner"><div class="pageNav icon"></div><div class="pageNav icon active"></div><div class="pageNav icon"></div><div class="pageNav icon"></div></div></div></div>
<div class="pageTitle">First you need to enter the SQL-Data</div>
<div class="content" align="center">
	<form name="sql" action="javascript:testSQL()">
		<table>
			<tr>
				<td>SQL Base</td>
				<td><input type="text" name="base" required></td>
			</tr>
			<tr>
				<td>SQL User</td>
				<td><input type="text" name="user" required></td>
			</tr>
			<tr>
				<td>SQL Pass</td>
				<td><input type="password" name="pass" required></td>
			</tr>
			<tr>
				<td>SQL Server</td>
				<td><input type="text" name="server" required></td>
			</tr>
		</table>
		<input type="submit" style="display: none;" />
	</form>
</div>
<div class="buttonSet"><div class="buttonLeft" onclick="back()">back</div><div class="buttonRight" onclick="testSQL()">continue</div></div>
<?php
}
?>