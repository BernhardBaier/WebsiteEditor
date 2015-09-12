<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 01.08.14
 * Time: 20:29
 */
error_reporting(E_ERROR);
function changeValue($param,$value){
	global $sql;
	$que = "SELECT * FROM settings WHERE parameter='$param'";
	$erg = mysqli_query($sql,$que);
	$search = false;
	while($row = mysqli_fetch_array($erg)){
		$search = $row['value'];
	}
	if($search === false){
		$que = "INSERT INTO settings (value,parameter) VALUES ('$value','$param')";
	}else{
		$que = "UPDATE settings set value='$value' WHERE parameter='$param'";
	}
	mysqli_query($sql,$que);
}
function resizeImage ($filepath_old, $filepath_new,$newHeight) {
	if (!(file_exists($filepath_old)) || file_exists($filepath_new)) return false;

	$image_attributes = getimagesize($filepath_old);
	$image_width_old = $image_attributes[0];
	$image_height_old = $image_attributes[1];
	$image_filetype = $image_attributes[2];

	if ($image_width_old <= 0 || $image_height_old <= 0) return false;
	$image_aspectratio = $image_width_old / $image_height_old;

	$image_height_new = $newHeight;
	$image_width_new = round($image_height_new * $image_aspectratio);
	switch ($image_filetype) {
		case 1:
			$image_old = imagecreatefromgif($filepath_old);
			$image_new = imagecreate($image_width_new, $image_height_new);
			imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old);
			imagegif($image_new, $filepath_new);
			break;

		case 2:
			$image_old = imagecreatefromjpeg($filepath_old);
			$image_new = imagecreatetruecolor($image_width_new, $image_height_new);
			imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old);
			imagejpeg($image_new, $filepath_new);
			break;

		case 3:
			$image_old = imagecreatefrompng($filepath_old);
			$image_colordepth = imagecolorstotal($image_old);

			if ($image_colordepth == 0 || $image_colordepth > 255) {
				$image_new = imagecreatetruecolor($image_width_new, $image_height_new);
			} else {
				$image_new = imagecreate($image_width_new, $image_height_new);
			}

			imagealphablending($image_new, false);
			imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old);
			imagesavealpha($image_new, true);
			imagepng($image_new, $filepath_new);
			break;

		default:
			return false;
	}
	imagedestroy($image_old);
	imagedestroy($image_new);
	return true;
}
error_reporting(E_ERROR);
$executingAlone = '';
if(!isset($authLevel)){
    $executingAlone = true;
    if(!file_exists("access.crypt")){
        copy("../access.crypt","access.crypt");
    }
	if(!is_dir('../upload')){
		mkdir('../upload');
	}
	include("../access.php");
    include('../auth.php');
}
if($authLevel == '1111'){
	$lang = $_GET['lang'];
	$lang = $lang == "" ? "de" : $lang;
	if($_GET['action'] == 'deleteLogo'){
		unlink('../images/logo.png');
		header('Location: ../setup.php?id=settings&lang='.$lang);
		exit;
	}else if($_GET['action'] == 'upload'){
		if(substr(basename($_FILES['upfile']['name']),-3) == 'png'){
			$uploadFile = '../upload/logo.png';
			if(move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadFile)) {
				header('Location: settings.php?action=moveFile&lang='.$lang);
				exit;
			}else{
                header('Location: ../setup.php?id=settings&lang='.$lang.'&upload=could not move file!');
                exit;
            }
		}else{
            header('Location: ../setup.php?id=settings&lang='.$lang.'&upload=only png files allowed here!');
            exit;
        }
	}else if($_GET['action'] == 'moveFile'){
		if(file_exists('../upload/logo.png')){
			$checkSize = getimagesize("../upload/logo.png");
			if($checkSize[1] > 90){
				if(resizeImage("../upload/logo.png","../images/logo.png",90)){
					unlink('../upload/logo.png');
					header('Location: ../setup.php?id=settings&lang='.$lang);
					exit;
				}
			}else{
				if(copy('../upload/logo.png','../images/logo.png')){
					unlink('../upload/logo.png');
					header('Location: ../setup.php?id=settings&lang='.$lang);
					exit;
				}
			}
		}
	}else if($_GET['action'] == 'change'){
		$pageTitle = $_POST['pageTitle'];
		$multiLang = $_POST['multiLang'];
		$multiLang = $multiLang=='on'?'multi':'single';
		$autoUpdate = $_POST['autoUp'];
		$pageTitle = $pageTitle == ''?'no Title':$pageTitle;
		$hostname = $_SERVER['HTTP_HOST'];
		$host = $hostname == 'localhost'?$hostname:$sqlHost;
		$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
		changeValue('pageTitle_'.$lang,$pageTitle);
		changeValue('languageSupport',$multiLang);
		changeValue('autoUpdate',$autoUpdate);
		header('Location: ../setup.php?id=settings&lang='.$lang);
		exit;
	}
	$hostname = $_SERVER['HTTP_HOST'];
	$host = $hostname == 'localhost'?$hostname:$sqlHost;
	$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
	if(!$sql){
		echo('sql error');
	}else{
		$que = "SELECT * FROM settings WHERE parameter='pageTitle_$lang'";
		$erg = mysqli_query($sql,$que);
		while($row = mysqli_fetch_array($erg)){
			$pageTitle = $row['value'];
		}
		$multiLang = "";
		$que = "SELECT * FROM settings WHERE parameter='languageSupport'";
		$erg = mysqli_query($sql,$que);
		while($row = mysqli_fetch_array($erg)){
			$multiLang = $row['value'];
		}
		$multiLang = $multiLang == 'multi'?' checked':'';
		$autoUpdate = "";
		$que = "SELECT * FROM settings WHERE parameter='autoUpdate'";
		$erg = mysqli_query($sql,$que);
		while($row = mysqli_fetch_array($erg)){
			$autoUpdate = $row['value'];
		}
		$autoUpdate = $autoUpdate=='on'?'checked':'';
	}
?>
<style>
	.notification{
		transition:opacity 1s;
		-webkit-transition:opacity 1s;
		position: absolute;
		z-index: 99;
		border-radius: 5px;
		padding: 3px;
		border: 1px solid #e65100;
		background: #ffa726;
		top:-5px;
		width: 200px;
		left:45%;
		left:calc(50% - 100px);
	}
	.buttonSet.note{
		background:#ff9800;
	}
	.content{
		text-align: left;
		margin: 0 5px;
	}
    .buttonCenter{
        text-align: center;
    }
    .buttonCenter a{
        text-decoration: none;
        color: #fff;
        padding: 4px;
        border: 1px solid #1a237e;
        border-radius: 5px;
        background: #5c6bc0;
    }
    .buttonCenter a:hover{
        background: #3f51b5;
    }
</style>
<script>
function showNotification(){
	document.getElementsByClassName('notification')[0].className = 'notification';
}
function hideNotification(){
	$('.notification').addClass('opac0');
	window.setTimeout("$('.notification').addClass('hidden');",900);
}
function resetWebsite(){

}
</script>
<div class="notification opac0 hidden">
	<strong>Warning!</strong> this operation can not be undone or canceled afterwards!<br>you might loos all your data!<br>you have to reenter your SQL Data!
	<div class="buttonSet note"><div class="buttonLeft" onclick="resetWebsite()">Proceed</div><div class="buttonRight" onclick="hideNotification()">cancel</div></div>
</div>
<?php
if(isset($_GET['upload'])){
    echo('<div class="noteBox error">'.$_GET['upload'].'</div>');
}
?>
<div class="pageTitle">Settings</div>
<div class="content" align="center">
	<?php
	if(!file_exists('images/logo.png')){
		echo('<form action="setup/settings.php?action=upload" method="post" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
				Logo: <input name="upfile" type="file" />
				<input type="submit" value="upload" />
			</form>');
	}else{
		echo('Logo: <img src="images/logo.png" height="45" /> <input type="button" onclick="location.href=\'setup/settings.php?action=deleteLogo\'" value="change logo"/>');
	}
	?><br>
	<form action="setup/settings.php?action=change&lang=<?php echo($lang);?>" method="post">
		<label>Title of the website: <input type="text" name="pageTitle" placeholder="title" value="<?php echo($pageTitle);?>" /></label><br>
		<label><input type="checkbox" name="multiLang" <?php echo($multiLang);?> /> multi language support</label><br>
		<label><input type="checkbox" name="autoUp" <?php echo($autoUpdate);?> /> automatic updates</label><br>
		<input type="submit" value="change" />
	</form>
    <div class="buttonCenter"><a href="editor.php?lang=<?php echo($lang);?>&id=impress">edit impress</a></div>
</div>
<div class="buttonSet"><div class="buttonLeft" onclick="showNotification()">Change SQL Data</div><div class="buttonRight" onclick="location.href='admin.php'">go back to admin panel</div></div>
<?php
}else{
    if($executingAlone === true){
        $executingAlone = '../';
    }
	echo('This operation can only be done by an admin!<div class="buttonSet"><div class="buttonRight" onclick="location.href=\''.$executingAlone.'admin.php\'">go back to admin panel</div></div>');
}
?>