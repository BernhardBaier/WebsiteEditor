<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 19.04.14
 * Time: 11:50
 */
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
function backupData(){
    //global $base,$table,$backup;
    //ToDO: write backup function!
}
function updateValueById($id,$index,$value,$sql){
    global $base,$table;
    if($index == "name" && $table == 'pages'){
        $que = "UPDATE `$base`.`$table` SET $index='".$value."' WHERE id=$id";
    }else{
        $que = "UPDATE `$base`.`$table` SET $index='".$value."' WHERE id=$id";
    }

    return mysqli_query($sql, $que) or (mysqli_error($sql));
}
function insertData($sql,$name,$parent,$rank=0,$child='NULL',$childCount=0,$extra=0){
    global $base,$table;
    $que = "INSERT INTO `".$base."`.`".$table."` (`name`, `rank`, `child`, `childCount`, `parent`, `extra`,`created`,`edit`) VALUES ('$name',$rank,$child,$childCount,$parent,$extra,'".date('d.m.Y')."','-');";
    return mysqli_query($sql, $que) or (mysqli_error($sql));
}
function deleteId($id,$sql){
    global $base,$table;
    $que = "DELETE FROM `".$base."`.`".$table."` WHERE id = $id";
    return mysqli_query($sql, $que) or (mysqli_error($sql));
}
function deleteTable($sql){
    global $base,$table;
    $que = "DROP TABLE `".$base."`.`".$table."`;";
    $res = mysqli_query($sql, $que) or (mysqli_error($sql));
    if($res == 1){
        return 'table deleted!';
    }else{
        return $res;
    }
}
function getValueById($id,$value,$sql){
    global $table;
    $que = "SELECT * FROM ".$table." WHERE id=$id";
    $erg = mysqli_query($sql,$que);
    while($row = mysqli_fetch_array($erg)){
        return($row[$value]);
    }
    return -1;
}
function addMainPage($name,$sql){
    global $table;
    $que = "SELECT * FROM ".$table." WHERE parent=0";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    $rank = 0;
    while($row = mysqli_fetch_array($erg)){
        $rank = $row['rank'] > $rank?$row['rank']:$rank;
    }
    mysqli_free_result($erg);
    $rank++;
    if(insertData($sql,$name,0,$rank)){
        $que = "SELECT * FROM ".$table." WHERE 1";
        $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
        $rank = 0;
        while($row = mysqli_fetch_array($erg)){
            $rank = $row['id'] > $rank?$row['id']:$rank;
        }

        mkdir('web-images/'.$rank.'/');
        mkdir('web-images/'.$rank.'/thumbs/');
        return $rank;
    }
    return 0;
}
function addSubPage($name,$parent,$sql){
    global $table;
    $que = "SELECT * FROM ".$table." WHERE parent=$parent";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    $rank=0;
    while($row = mysqli_fetch_array($erg)){
        $rank = $row['rank'] > $rank?$row['rank']:$rank;
    }
    mysqli_free_result($erg);
    $rank++;
    insertData($sql,$name,$parent,$rank);
    $que = "SELECT * FROM ".$table." WHERE 1";
    $erg = mysqli_query($sql,$que);
    $id=0;
    while($row = mysqli_fetch_array($erg)){
        $id = $row['id'] > $id?$row['id']:$id;
    }
    mysqli_free_result($erg);
    $childC = getValueById($parent,'childCount',$sql) + 1;
    $childs = unserialize(getValueById($parent,'child',$sql));
    if(empty($childs)){
        $childs = array($id);
    }else{
        array_push($childs,$id);
    }
    updateValueById($parent,'childCount',$childC,$sql);
    updateValueById($parent,'child',serialize($childs),$sql);
    mkdir('web-images/'.$id.'/');
    mkdir('web-images/'.$id.'/thumbs/');
    return $id;
}
function moveRank($id,$newRank,$sql){
    global $table;
    $rank = getValueById($id,'rank',$sql);
    if($newRank == $rank){
        return -1;
    }
    $parent = getValueById($id,'parent',$sql);
    $maxRank = 0;
    if($parent==0){
        $que = "SELECT * FROM ".$table." WHERE parent=0";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $maxRank = $row['rank'] > $maxRank?$row['rank']:$maxRank;
        }
    }else{
        $maxRank = getValueById($parent,'childCount',$sql);
    }
    if($maxRank < $newRank){
        return -2;
    }
    $que = "SELECT * FROM ".$table." WHERE parent=$parent";
    $erg = mysqli_query($sql,$que);
    $ranks= array();
    while($row = mysqli_fetch_array($erg)){
        $ranks[$row['rank']] = $row['id'];
    }
    mysqli_free_result($erg);
    if($newRank>$rank){
        for($i=$rank;$i<$newRank;$i++){
            updateValueById($ranks[$i+1],'rank',$i,$sql);
        }
        updateValueById($ranks[$rank],'rank',$newRank,$sql);
    }else{
        for($i=$newRank;$i<$rank;$i++){
            updateValueById($ranks[$i],'rank',$i+1,$sql);
        }
        updateValueById($ranks[$rank],'rank',$newRank,$sql);
    }
    return 1;
}
function movePage($id,$newParent,$sql){
    global $table;
    $parent = getValueById($id,'parent',$sql);
    if($parent == 0){
        //return -1;
    }
    //remove this child from old Parent
    $childC = getValueById($parent,'childCount',$sql);
    if($childC == 1){
        updateValueById($parent,'child',NULL,$sql);
        updateValueById($parent,'childCount',0,$sql);
    }else{
        //change rank in old parent
        $maxRank = 0;
        if($parent==0){
            $que = "SELECT * FROM ".$table." WHERE parent=0";
            $erg = mysqli_query($sql,$que);
            while($row = mysqli_fetch_array($erg)){
                $maxRank = $row['rank'] > $maxRank?$row['rank']:$maxRank;
            }
        }else{
            $maxRank = getValueById($parent,'childCount',$sql);
        }
        moveRank($id,$maxRank,$sql);
        //remove element in childs
        $childs = unserialize(getValueById($parent,'child',$sql));
        $pos = findInArray($childs,$id);
        array_splice($childs,$pos,1);
        updateValueById($parent,'child',serialize($childs),$sql);
        updateValueById($parent,'childCount',sizeof($childs),$sql);
    }
    //change parent of element
    updateValueById($id,'parent',$newParent,$sql);
    //set element to last rank
    $maxRank = 0;
    if($newParent==0){
        $que = "SELECT * FROM ".$table." WHERE parent=0";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $maxRank = $row['rank'] > $maxRank?$row['rank']:$maxRank;
        }
        updateValueById($id,'rank',$maxRank+1,$sql);
    }else{
        $maxRank = getValueById($newParent,'childCount',$sql) + 1;
        if($maxRank > 0){
            moveRank($id,$maxRank,$sql);
        }
        //add this element to childs of new parent
        $childC = getValueById($newParent,'childCount',$sql);
        if($childC == 0){
            updateValueById($newParent,'child',serialize(array($id)),$sql);
            updateValueById($newParent,'childCount',1,$sql);
            updateValueById($id,'rank',1,$sql);
        }else{
            $childs = unserialize(getValueById($newParent,'child',$sql));
            array_push($childs,$id);
            updateValueById($newParent,'child',serialize($childs),$sql);
            updateValueById($newParent,'childCount',sizeof($childs),$sql);
            updateValueById($id,'rank',sizeof($childs),$sql);
        }//*/
    }

    return 1;
}
function setVisibility($id,$visib,$sql){
    updateValueById($id,'extra',$visib,$sql);
}
function changeName($id,$name,$sql){
    updateValueById($id,'name',$name,$sql);
}
function deletePage($id,$sql){
    global $table;
    $parent = getValueById($id,'parent',$sql);
    //change rank in old parent
    $maxRank = 0;
    if($parent==0){
        $que = "SELECT * FROM ".$table." WHERE parent=0";
        $erg = mysqli_query($sql,$que);
        while($row = mysqli_fetch_array($erg)){
            $maxRank = $row['rank'] > $maxRank?$row['rank']:$maxRank;
        }
    }else{
        $maxRank = getValueById($parent,'childCount',$sql);
    }
    moveRank($id,$maxRank,$sql);
    if($parent != 0){
        //remove element in childs
        $childs = unserialize(getValueById($parent,'child',$sql));
        $pos = findInArray($childs,$id);
        array_splice($childs,$pos,1);
        updateValueById($parent,'child',serialize($childs),$sql);
        updateValueById($parent,'childCount',sizeof($childs),$sql);
    }
    deleteId($id,$sql);
}
$counter = 0;
if(!function_exists("printMenu")){
    function printMenu($sql,$n_parent=0){
        global $table,$counter,$lang;
        $que = "SELECT * FROM ".$table." WHERE parent=$n_parent";
        $erg = mysqli_query($sql,$que);
        $rows = array();
        while($help = mysqli_fetch_array($erg)){
            $rows[$help['rank']] = $help;
        }
        for($i=1;$i<=sizeof($rows);$i++){
            $row = $rows[$i];
            $pid = $row['id'];
            $parent = $row['parent'];
            $extra = $row['extra'];
            $rank = $row['rank'];
            $name = $row['name'];
            $childCount = $row['childCount'];
            $childsChildCount = getValueById($pid,"childCount",$sql);
            $option = $extra == 1?"<img src='images/eye.png' height='18' title='visible' onclick='setVisibility($pid,0)' />":"<img src='images/eyeHid.png' height='18' title='invisible' onclick='setVisibility($pid,1)' />";
            $option2 = $childsChildCount == 0?"<img src='images/listicon.png' height='18' class='menuItemImg' />":"<img src='images/minus.png' title='hide group' onclick='hideGroupe($pid)' />";
            if($childsChildCount != 0){
                echo("<ul id='menGrpIn$pid' class='hidden'><li><div class='menuItemHidden'><img src='images/plus.png' class='menuItemImg' onclick='showGroupe($pid)' title='show group' />&nbsp;&nbsp;$name</div></li></ul>");
            }
            echo("<ul id='menGrpCt$pid'><li>");
            echo("<div class='menuItem' id='menuItem".$counter++."' draggable='true' ondragenter='handleDragEnter(event)' ondragover='handleDragOver(event)' ondrop='handleDrop(event)'>before</div>
                        <div class='menuItem'>
                            <div class='menuItemLeft'>$option2</div>
                            <div class='menuItemInner' id='menuItem".$counter."' draggable='true' ondragstart='handleDragStart(event)' ondragenter='handleDragEnter(event)' ondragover='handleDragOver(event)'
                                 ondragend='handleDragEnd(event)'><a id='menuItem".$counter."' href='admin.php?id=$pid&lang=$lang'>$name</a><div id='menuItemInfo".$counter++."' class='hidden'>$pid;$parent;$rank;$name;</div><div class='menuOptions hidden'
                                 id='menuOptions$pid'></div></div>
                            <div class='menuItemRight'>$option<img src='images/menuOptions.png' height='18' title='options' onclick='$(this).toggleClass(\"imgRotated\");showOptions($pid,\"$name\",$parent,$rank)' class='imgRotate' /></div>
                        </div>
                        <div class='menuItem' id='menuItem$counter' draggable='true'>
                            <div id='menuItem".$counter."_1' class='menuItemLeftDrop' draggable='true' ondragenter='handleDragEnter(event)' ondragover='handleDragOver(event)' ondrop='handleDrop(event)'>after</div>
                            <div id='menuItem".$counter."_2' class='menuItemRightDrop' draggable='true' ondragenter='handleDragEnter(event)' ondragover='handleDragOver(event)' ondrop='handleDrop(event)'>as child</div>
                        </div></li>");
            $counter+=2;
            if($childCount > 0){
                printMenu($sql,$pid);
            }
            echo('</ul>');
        }
        mysqli_free_result($erg);
    }
}

function findInArray($array,$needle){
    for($i=0;$i<sizeof($array);$i++){
        if($array[$i] == $needle){
            return $i;
        }
    }
    return -1;
}
function replaceUml($text){
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $text = str_replace($olds,$news,$text);
    return $text;
}

function createUser($name,$pw,$mail,$access,$sql){
    global $table,$base;
    $que = "SELECT * FROM ".$table." WHERE 1";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    while($row = mysqli_fetch_array($erg)){
        if(decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8') == $name){
            return -1;
        }
    }
    $name = encrypt($name,'C3zyK5Uu3zdmgE6pCFB8');
    $pw = encrypt('access',$pw);
    $mail = encrypt($mail,'C3zyK5Uu3zdmgE6pCFB8');
    $access = encrypt($access,'C3zyK5Uu3zdmgE6pCFB8');
    $que = "INSERT INTO `".$base."`.`".$table."` (`user`, `pw`, `access`, `email`, `reg`, `ondate`) VALUES ('$name','$pw','$access','$mail','".date('d.m.Y')."','-');";
    return mysqli_query($sql, $que) or (mysqli_error($sql));
}
function deleteUser($name,$sql){
    global $table,$base;
    $que = "SELECT * FROM ".$table." WHERE 1";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    $user = '';
    while($row = mysqli_fetch_array($erg)){
        if(decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8') == $name){
            $user = $row['user'];
        }
    }
    mysqli_free_result($erg);
    $que = "DELETE FROM `".$base."`.`".$table."` WHERE user = '".$user."'";
    return mysqli_query($sql, $que) or (mysqli_error($sql));
}
function changeUserRights($name,$rights,$sql){
    global $table,$base;
    $que = "SELECT * FROM ".$table." WHERE 1";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    $user = '';
    while($row = mysqli_fetch_array($erg)){
        if(decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8') == $name){
            $user = $row['user'];
        }
    }
    mysqli_free_result($erg);
    $que = "UPDATE `".$base."`.`".$table."` SET access = '".encrypt($rights,'C3zyK5Uu3zdmgE6pCFB8')."' WHERE user='".$user."'";
    return mysqli_query($sql, $que) or (mysqli_error($sql));
}