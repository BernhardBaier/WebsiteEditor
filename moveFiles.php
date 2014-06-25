<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 05.02.14
 * Time: 14:51
 */
include('auth.php');
error_reporting(E_ERROR);
if($authLevel != '' && $authLevel != '0000'){
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
    $id=$_POST['id'];
    $pfad=$_POST['path'];
    $user = $_SESSION['user'];
    $picTypes = Array('gif','png','jpg','jpeg','svg');
    $mucTypes = Array('mp3','ogg','wav');
    $vidTypes = Array('mp4','ogg');
    $olds = Array(' ','%20','ä','ö','ü','Ä','Ö','Ü','ß');
    $news = Array('_','_','ae','oe','ue','AE','OE','UE','SS');
    include('access.php');
    $base = $sqlBase;
    $hostname = $_SERVER['HTTP_HOST'];
    $host = $hostname == 'localhost'?$hostname:'rdbms.strato.de';
    $sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
    $table = 'uploads';
    function insertFileToDatabase($name,$path){
        global $base,$table,$sql,$user;
        $fileExists = 0;
        $que = "SELECT * FROM ".$table." WHERE name='".$name."'";
        $erg = mysqli_query($sql,$que) or mysqli_error($sql);
        while($in = mysqli_fetch_array($erg)){
            if($in['uploader'] == $user){
                $fileExists = $in['id'];
            }
        }
        mysqli_free_result($erg);
        if($fileExists > 0){
            $que = "UPDATE `".$base."`.`".$table."` SET date = '".date('d.m.Y')."',page = '".$path."' WHERE id=".$fileExists;
            $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        }else{
            $que = "INSERT INTO `".$base."`.`".$table."` (`id`, `name`, `uploader`, `date`, `page`) VALUES (NULL,'$name','$user','".date('d.m.Y')."','$path');";
            $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        }
    }
    if($id != ""){
        if(!is_dir("web-images/$id/")){
            mkdir("web-images/$id/");
        }
        if(!is_dir("web-others/$id/")){
            mkdir("web-others/$id/");
        }
        if(!is_dir("web-images/$id/thumbs/")){
            mkdir("web-images/$id/thumbs");
        }
        @session_start();
        $path = "fileUpload/server/php/files/".session_id()."/";
        if($pfad!=""){
	        if(!is_dir("web-images/$id/$pfad/")){
		        mkdir("web-images/$id/$pfad/");
	        }
	        if(!is_dir("web-images/$id/$pfad/thumbs/")){
		        mkdir("web-images/$id/$pfad/thumbs/");
	        }
            $output_dir = "web-images/$id/$pfad/";
        }else{
            $output_dir = "web-images/$id/";
        }
        $output = '<div class="gallery">
        <div class="picsClickAble">';
        $handler = opendir($path);
        while($file = readdir($handler)){
            $fileCorr = str_replace($olds,$news,$file);
            $fileName = strtolower(substr($file,strrpos($file,'.') + 1));
            if(in_array($fileName,$picTypes)){
                $checkSize = getimagesize($path.$file);
                if($checkSize[1]>1080){
                    if(resizeImage($path.$file,$output_dir.$fileCorr,1080)){
                        resizeImage($output_dir.$fileCorr,$output_dir.'thumbs/'.$fileCorr,135);
                    }
                }else{
                    if(copy($path.$file,$output_dir.$fileCorr)) {
                        resizeImage($output_dir.$fileCorr,$output_dir.'thumbs/'.$fileCorr,135);
                    }
                }
                insertFileToDatabase($file,$output_dir);
                unlink($path.$file);
                unlink($path.'thumbnail/'.$file);
            }else if($file != '.' && $file != '..' && $file != '.htaccess' && !is_dir($path.$file)){
                $output_dir = "web-others/$id/";
                copy($path.$file,$output_dir.$file);
                insertFileToDatabase($file,$output_dir);
                unlink($path.$file);
                if(file_exists($path.'thumbnail/'.$file)){
                    unlink($path.'thumbnail/'.$file);
                }
            }
        }
        echo('1');
    }
}