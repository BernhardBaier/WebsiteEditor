<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 01.08.14
 * Time: 04:56
 */
error_reporting(E_ERROR);
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
$path = $_GET['path'];
if(isset($_COOKIE['sslPath'])){
	if(strlen($path) < 6){
		$path = $_COOKIE['sslPath'];
	}
}
if(file_exists('access.crypt')){
	if($_GET['id'] == 'user'){
        $datei = fopen('access.crypt','r');
        $in = fread($datei,filesize('access.crypt'));
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
		if($userExists){
			include("auth.php");
		}
	}else if($_GET['id'] == 'login'){

	}else{
        include "access.php";
	}
}
$path = str_replace('%2F','/',$path);
setcookie('sslPath',$path,time()+999);
$editorVersion = '4.1';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Website setup</title>
	<link rel="SHORTCUT ICON" href="images/editorLogo.png"/>
	<style>
		.container{
			margin:45px calc(50% - 250px) 0 calc(50% - 250px);
		}
		.page{
			position: relative;
			width:500px;
			background:#EEE;
			border-radius:5px;
			border:1px solid #DDD;
		}
		.copyright{
			width:100%;
			font-size: 12px;
			color: #555;
			text-align: center;
		}
		.pageTitle{
			width:100%;
			text-align:center;
			font-size:25px;
			font-weight:bold;
			color:#333;
		}
		.pageNav{
			border-radius: 5px 5px 0 0;
			background: #e0e0e0;
			height:17px;
			width:100%;
			position: relative;
		}
		.pageNav.navCount{
			position: absolute;
			width: 58px;
			height: 13px;
			top:1px;
			left:45%;
			left:calc(50% - 29px);
			border-radius: 2px;
			border:1px solid #9e9e9e;
		}
		.pageNav.navCountInner{
			position: absolute;
			width: 58px;
			background: transparent;
			top:-4px;
			left:45%;
			left:calc(50% - 25px);
		}
		.pageNav.icon{
			display: inline-flex;
			height:9px;
			width:9px;
			border-radius: 5px;
			background: #FFF;
			margin: 1px;
			border: 1px solid #757575;
		}
		.pageNav.icon.active{
			background: #757575;
			border:1px solid #616161;
		}
		.buttonSet{
			 position: relative;
			 margin: 10px 0 0 0;
			 height: 25px;
			 background: #e0e0e0;
		 }
		.buttonRight{
			position: absolute;
			cursor: pointer;
			display: flex;
			right: 2px;
			top:2px;
			height: 16px;
			padding: 0 2px 4px 2px;
			background: #4db6ac;
			border: 1px solid #009688;
			border-radius: 3px;
		}
		.buttonRight:hover{
			background: #26a69a;
		}
		.buttonLeft{
			position: absolute;
			cursor: pointer;
			display: flex;
			left: 2px;
			top:2px;
			height: 16px;
			padding: 0 2px 4px 2px;
			background: #ffcc80;
			border: 1px solid #ffa726;
			border-radius: 3px;
		}
		.buttonLeft:hover{
			background: #ffb74d;
		}
		.noteBox{
			transition:opacity 1s;
			-webkit-transition:opacity 1s;
			position:absolute;
            z-index: 99;
			text-align:center;
			width:490px;
			padding:5px;
			border-radius:5px;
			border:1px solid #070;
			background:#568C0A;
			left:calc(50% - 250px);
			top:-35px;
		}
		.noteBox.error{
			border:1px solid #A00;
			background:#F66;
		}
		.opac0{
			opacity:0;
		}
		.hidden{
			display:none;
		}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
<div class="container">
	<div class="page">
		<?php
		if($_GET['id'] == "sql"){
			include("setup/sql.php");
		}else if($_GET['id'] == "user"){
			include("setup/user.php");
		}else if($_GET['id'] == "login"){
			include("setup/login.php");
		}else if($_GET['id'] == "settings"){
			include("setup/settings.php");
		}else if(!file_exists('access.crypt')){
			include("setup/welcome.php");
		}else{
            echo("<a href='admin.php'>an error occurred leave this page!</a>");
        }
		?>
	</div>
</div>
<div class="copyright">Copyright &copy; 2013 - <?php echo(date('Y'));?> Bernhard Baier</div>
</body>
</html>