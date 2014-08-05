<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 01.08.14
 * Time: 12:38
 */
error_reporting(E_ERROR);
if(basename($_SERVER["SCRIPT_FILENAME"]) != 'setup.php'){
	header('Location: ../setup.php');
}
?>
<script>
function start(){
	if(!document.getElementById('ssl')){
		location.href = "setup.php?id=sql&path=none";
		return;
	}
	var ssl = document.getElementById('ssl').value;
	if(ssl!=""){
		if(ssl.substr(-1) != '/'){
			ssl += '/';
		}
		if(ssl.substr(0,8) != "https://"){
			alert("The ssl path seems to be incorrect!");
		}else{
			location.href = ssl+location.href.replace("http://","")+"?id=sql&path="+ssl;
		}
	}else{
		location.href = "setup.php?id=sql&path=none";
	}
}
</script>
<div class="pageNav"><div class="pageNav navCount"><div class="pageNav navCountInner"><div class="pageNav icon active"></div><div class="pageNav icon"></div><div class="pageNav icon"></div><div class="pageNav icon"></div></div></div></div>
<div class="pageTitle">Welcome to Website Editor Version <?php echo($editorVersion);?></div>
<div class="content" align="center">
	This assistant will guide you through the basic setup of the website.<br>
	<?php
	if($_SERVER['SERVER_PORT'] != '443' && $_GET['ssl'] != 'false'){
	?>
		<form name="welcome" action="javascript:start();">
			you should enable ssl.<br>
			<label title="enter ssl prefix here (default is https://). Some pages need an other page to redirect over to get an SSL protection">
			<input style="width:65px;" value="<?php echo($path);?>" id="ssl" type="text" placeholder="https://" /> domain & location will come here<br>
			</label>
			<input type="submit" style="display: none;" />
		</form>
	<?php
	}
	?>
</div>
<div class="buttonSet"><div class="buttonRight" onclick="start()">start</div></div>