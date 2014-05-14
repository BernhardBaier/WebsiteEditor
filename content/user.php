<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 06.03.14
 * Time: 21:18
 */
if(!function_exists(encrypt)){
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
include 'access.php';
$username = $_SESSION['user'];
echo('<div class="ownUserControlOuter hidden">
    <img style="float:right;cursor:pointer" onclick="$(\'.ownUserControlOuter\').addClass(\'hidden\')" height="25" title="close" src="images/close.png" ><h1>'.$username.'</h1>');
echo("<script>
        function initUserOptions(){
            $('.userPageContent').removeClass('hidden');
            $('.userPageLoading').addClass('hidden');
            window.setTimeout('document.getElementById(\"oldPass\").value = \"\";',150);
        }
    </script>");
if($_GET['showError'] == 'true'){
    echo('<div class="youShallNotUpload">you shall not upload here!</div>');
}
$pw = "";
if(isset($_POST['pass'])){
    $pw = $_POST['pass'];
}
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$sqlBase);
$que = "SELECT * FROM users WHERE 1";
$erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
if($erg){
    $aUser = '';
    while($row = mysqli_fetch_array($erg)){
        $out = decrypt($row['pw'],$pw);
        $user = decrypt($row['user'],'C3zyK5Uu3zdmgE6pCFB8');
        if($user == $username){
            $aUser = $row['user'];
            $access = decrypt($row['access'],'C3zyK5Uu3zdmgE6pCFB8');
            echo('<div class="userPageLoading">Loading user data...</div><div class="userPageContent hidden">');
            echo('Registered: '.$row['reg']);
            echo('<div>Change userdata <img src="images/info.png" height="20" onmouseover="$(\'.userMessage\').removeClass(\'hidden\')" onmouseout="$(\'.userMessage\').addClass(\'hidden\')"/>
            <div class="userMessage hidden">Type in your wished username and enter your password. If you wish you can change your password, too.</div></div>');
            echo('<form method="post" action="admin.php?id='.$id.'&action=showUsers"><input type="text" name="user" value="'.$user.'" placeholder="username"/>');
            echo('</br><input id="oldPass" type="password" name="pass" value="" placeholder="old password" required/></br>
            <input type="password" name="npass1" placeholder="new password"/></br><input type="password" name="npass2" placeholder="repeat"/><br/><input type="submit" value="change"/></form>');
            if($out == 'access'){
                $changedName = false;
                if($user!=$_POST['user']){
                    $nUser = $_POST['user'];
                    $wrongLetter = false;
                    $umlaute = array('ä','ö','ü','Ä','Ö','Ü','ß');
                    for($j=0;$j<7;$j++){
                        if(strpos($nUser,$umlaute[$j]) > -1){
                            $wrongLetter = true;
                        }
                    }
                    if(!$wrongLetter){
                        $cantChange = false;
                        $que = "SELECT * FROM users WHERE 1";
                        $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
                        while($tester = mysqli_fetch_array($erg)){
                            $userToTest = decrypt($tester['user'],'C3zyK5Uu3zdmgE6pCFB8');
                            if($userToTest == $nUser){
                                $cantChange = true;
                            }
                        }
                        if(!$cantChange){
                            $que = "UPDATE users SET user = '".encrypt($nUser,'C3zyK5Uu3zdmgE6pCFB8')."' WHERE user = '$aUser'";
                            $erg = mysqli_query($sql,$que);
                            $que = "UPDATE uploads SET uploader='$nUser' WHERE uploader='$user'";
                            mysqli_query($sql,$que);
                            $aUser = encrypt($nUser,'C3zyK5Uu3zdmgE6pCFB8');
                            $_SESSION['user'] = $nUser;
                            echo('<div class="youShallUpload">username Changed. Refreshing page soon...</div><script>window.setTimeout("reloadLocation(\'showUsers\');",1500);</script>');
                        }else{
                            echo('<div class="youShallNotUpload">username already existent!</div>');
                        }
                    }else{
                        echo('<div class="youShallNotUpload">Use only english chars!</div>');
                    }
                    $changedName = true;
                }
                $npass1 = $_POST['npass1'];
                $npass2 = $_POST['npass2'];
                if($npass1 != $npass2){
                    if(strlen($npass1) < 4 && $changedName = false){
                        echo('<div class="youShallNotUpload">new passwords inconsistent!</div>');
                    }
                }elseif(strlen($npass1) > 3){
                    $wrongLetter = false;
                    $umlaute = array('ä','ö','ü','Ä','Ö','Ü','ß');
                    for($j=0;$j<7;$j++){
                        if(strpos($npass1,$umlaute[$j]) > -1){
                            $wrongLetter = true;
                        }
                    }
                    if(!$wrongLetter){
                        $que = "UPDATE users SET pw = '".encrypt('access',$npass1)."' WHERE user = '$aUser'";
                        $erg = mysqli_query($sql,$que);
                        echo('<div class="youShallUpload">password successfully changed</div>');
                    }else{
                        echo('<div class="youShallNotUpload">Use only english chars!</div>');
                    }
                }
            }elseif(strlen($pw) > 3){
                echo('<div class="youShallNotUpload">old password incorrect</div>');
            }
            $que = "SELECT * FROM pages_$lang WHERE 1";
            $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
            $pages = [];
            while($file = mysqli_fetch_array($erg)){
                $pages[$file['id']] = $file['name'];
            }
            $que = "SELECT * FROM uploads WHERE uploader='$user'";
            $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
            echo('<div class="fileStatistics">Uploaded Files: ');
            $files = [];
            $pageNames = [];
            $groupedFiles = [];
            $i=0;
            while($file = mysqli_fetch_array($erg)){
                $pageName = $file['page'];
                $pageName = substr($pageName,strpos($pageName,'/')+1);
                $pageId = substr($pageName,0,strpos($pageName,'/'));
                $pageName = $pages[$pageId];
                if(!in_array($pageName,$pageNames)){
                    array_push($pageNames,$pageName);
                }
                $groupedFiles[$pageName][sizeof($groupedFiles[$pageName])] = [$file['name'],$pageId,$file['page'],$file['date']];
                $i++;
            }
            $max = sizeof($pageNames);
            echo($i.'<div class="groupedItemGroupOuter">');
            $count = 0;
            for($i=0;$i<$max;$i++){
                $max2 = sizeof($groupedFiles[$pageNames[$i]]);
                echo('<div class="groupedItemGroup" title="page name">'.$pageNames[$i].' ');
                echo('<img src="images/galOptions.png" height="17" class="userImg'.$count.'" onclick="$(\'.groupedItemGroup'.$count.'\').toggleClass(\'hidden\');$(\'.userImg'.$count.'\').toggleClass(\'imgRotated\')"/>');
                echo(' <span style="color:#666;font-weight:normal;">('.$max2.' files)</span><div class="groupedItemGroup'.$count++.' hidden">');
                echo('<table><tr><td align="center">Name</td><td align="center">Date</td><td align="center">Page</td></tr>');
                for($j=0;$j<$max2;$j++){
                    echo('<tr><td><a href="'.$groupedFiles[$pageNames[$i]][$j][2].$groupedFiles[$pageNames[$i]][$j][0].'" target="_blank" title="open picture in new tab">'.$groupedFiles[$pageNames[$i]][$j][0]);
                    echo('</a></td><td title="upload date">'.$groupedFiles[$pageNames[$i]][$j][3].'</td><td><a title="enter pageId '.$groupedFiles[$pageNames[$i]][$j][1].'" href="admin.php?id=');
                    echo($groupedFiles[$pageNames[$i]][$j][1].'">'.$pageNames[$i].'</a></td></tr>');
                }
                echo('</table></div></div>');
            }
            echo('</div></div></div>');
        }
    }
    mysqli_free_result($erg);
}
echo("</div>");