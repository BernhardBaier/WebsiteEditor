<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 04.07.14
 * Time: 11:13
 */
function addHTMLToReplace($html,$path){
	global $sqlBase,$sql;
	$que2 = "SELECT * FROM $sqlBase.toreplace WHERE replace='$html'";
	$erg = mysqli_query($sql,$que2);
    $found = false;
	while($row = mysqli_fetch_array($erg)){
        $found = $row['url'];
	}
	if($found === false){
		$que2 = "INSERT INTO $sqlBase.toreplace (`replace`,`url`) VALUES ('$html','$path')";
	}else{
		$que2 = "UPDATE $sqlBase.toreplace set url='$path' WHERE replace='$html'";
	}
	mysqli_query($sql,$que2) or die(mysqli_error($sql));
}
function removeHTMLFromReplace($html){
	global $sqlBase,$sql;
	$que2 = "DELETE FROM $sqlBase.toreplace WHERE `replace`='$html'";
	if(!(mysqli_query($sql,$que2))){
		echo("couldn't delete: $que2");
	}
}