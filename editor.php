<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 06.08.14
 * Time: 19:20
 */
error_reporting(E_ERROR);
include('access.php');
if($authLevel == '1111'){
    if(isset($_POST['editor1'])){
        $text = $_POST['editor1'];
        $lang = $_POST['lang'];
        if($lang == ''){
            $lang = 'de';
        }
        $id = $_POST['id'];
        if(strlen($text)>0){
            $action = 'saved';
            if($_POST['forcePath'] == 'true'){
                $path = $id;
                if(substr($path,0,3) == '<p>'){
                    $text = substr($text,3);
                    $text = substr($text,0,strlen($text)-4);
                }
                $text = str_replace('&lt;','<',$text);
                $text = str_replace('&gt;','>',$text);
                $text = str_replace('<!--?php','<?php',$text);
                $text = str_replace('?-->','?>',$text);
                $action = 'saved&forcePath=true';
            }else{
                $path = "web-content/$lang/$id.php";
            }
            $file = fopen($path,'w');
            fwrite($file,$text);
            fclose($file);
            if($_POST['forcePath'] == 'true'){
                $path = $id;
            }else{
                $path = "content/$lang/$id.php";
            }
            $file = fopen($path,'w');
            fwrite($file,$text);
            fclose($file);
            header("Location: editor.php?lang=$lang&id=$id&action=$action");
            exit;
        }
    }else{
        $content = 'enter text here';
        $lang = $_GET['lang'];
        if($lang == ''){
            $lang = 'de';
        }
        $id = $_GET['id'];
        if($_GET['forcePath'] == 'true'){
            $path = $id;
        }else{
            $path = "content/$lang/$id.php";
        }
        if(file_exists($path)){
            $file = fopen($path,'r');
            $content = fread($file,filesize($path));
            fclose($file);
        }
        $pageTitle = $id;
        $note = '';
        if($_GET['action'] == 'saved'){
            $note = '<div class="note">saved.</div>';
        }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Text editor</title>
    <link rel="SHORTCUT ICON" href="images/editorLogo.png" />
    <link rel="stylesheet" href="commonStyle.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="ckeditor/ckeditor.js"></script>
    <style>
        .nav{
            position: relative;
            background: #1a237e;
            color: #fff;
            padding: 2px;
            text-align: center;
            font-size: 17px;
        }
        .navNote{
            position: absolute;
            right: 4px;
            top:2px;
            color: #c0c0c0;
            font-size: 14px;
        }
        .note{
            position: absolute;
            top:2px;
            left: 4px;
            color: #8c9eff;
        }
    </style>
</head>
<body>
<div class="nav"><?php echo($note);?>Enter Text for page <?php echo($pageTitle);?> here.<div class="navNote">Text editor Version 1.1</div></div>
<form action="editor.php" method="post">
    <textarea name="editor1" id="editor1"><?php if(isset($_POST['editor1'])){echo($_POST['editor1']);}else{echo($content);}?></textarea>
    <input type="hidden" name="lang" value="<?php echo($lang);?>"><input type="hidden" name="id" value="<?php echo($id);?>"><?php if($_GET['forcePath'] == 'true'){echo('<input type="hidden" name="forcePath" value="true">');}?>
    <input type="submit" value="Save" />
</form>
<script>
    CKEDITOR.replace( 'editor1' );
    CKEDITOR.config.language = 'de';
    <?php
    if($_GET['forcePath'] != 'true'){
        echo("CKEDITOR.config.height = '650px';");
    }
    ?>
</script>
<?php
if($_GET['forcePath'] != 'true'){
    echo("<a href='admin.php?lang=$lang'>go to admin panel</a>");
}
?>
</body>
<?php
    }
}