<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 22.01.14
 * Time: 18:23
 */
error_reporting(E_ERROR);
include('access.php');
include('../../functionsPlugins.php');
$lang = $_POST['lang'];
$lang = $lang==''?'de':$lang;
$table = "calendar_$lang";
$year = $_POST['year'];
$func = $_POST['function'];
if($func != 'page' && $func != 'side'){
    include "auth.php";
}
$href = $_POST['href'];
$plugId = $_POST['id'];
$year = $year == ''?date('Y'):$year;
$base = $sqlBase;
$hostname = $_SERVER['HTTP_HOST'];
$host = $hostname == 'localhost'?$hostname:$sqlHost;
$sql = mysqli_connect($host,$sqlUser,$sqlPass,$base);
if(!$sql){
    echo('sql error');
    exit;
}
function replaceUml($text){
    $text = str_replace('<und>','&',$text);
    $text = str_replace('<dpp>',':',$text);
    return $text;
}
if(substr($func,0,6) == 'insert' && substr($authLevel,0,1) == '1'){
    if(strpos($func,':')>-1){
        $option = substr($func,strpos($func,':') + 1);
        $func = substr($func,0,strpos($func,':'));
        $i=0;
        while(strpos($option,':') > -1){
            $options[$i] =  substr($option,0,strpos($option,':'));
            $option = substr($option,strpos($option,':')+1);
            $i++;
        }
        $options[$i] = $option;
        if(!empty($options) && sizeof($options)>=6){
            $day = substr($options[0],0,strpos($options[0],'.'));
            $options[0] = substr($options[0],strpos($options[0],'.') + 1);
            $month = substr($options[0],0,strpos($options[0],'.'));
            $options[0] = substr($options[0],strpos($options[0],'.') + 1);
            $year =$options[0];
            $options[1] = replaceUml($options[1]);
            $options[2] = replaceUml($options[2]);
            $options[3] = replaceUml($options[3]);
            $options[4] = replaceUml($options[4]);
            $options[5] = replaceUml($options[5]);
            $que = "INSERT INTO `".$base."`.`".$table."` (`id`, `year`, `month`, `day`, `name`, `start`, `end`, `place`, `href`) VALUES (NULL,'$year','$month','$day','$options[3]','$options[1]','$options[2]','$options[4]','$options[5]');";
            echo(mysqli_query($sql, $que) or mysqli_error($sql));
        }else{
            echo('missing Options for function!');
        }
    }else{
        echo('error');
    }
}else if(substr($func,0,6) == 'delete' && substr($authLevel,0,1) == '1'){
    $que = "DELETE FROM `".$base."`.`".$table."` WHERE id=".substr($func,7).";";
    echo(mysqli_query($sql, $que) or mysqli_error($sql));
}else if(substr($func,0,4) == 'edit' && substr($authLevel,0,1) == '1'){
    $id = substr($func,5);
    $que = "SELECT * FROM `".$base."`.`".$table."` WHERE id=$id;";
    $erg = mysqli_query($sql, $que) or die(mysqli_error($sql));
    while($row = mysqli_fetch_array($erg)){
        $date = $row['day'].'.'.$row['month'].'.'.$row['year'];
        $start = $row['start'];
        $end = $row['end'];
        $name = $row['name'];
        $place = $row['place'];
        $href = $row['href'];
    }
    switch($href){
        case 'aktive':
            $href = '<option>alle</option><option selected>aktive</option><option>jugend</option><option>Maschinisten</option><option>Atemschutz</option>';
            break;
        case 'jugend':
            $href = '<option>alle</option><option>aktive</option><option selected>jugend</option><option>Maschinisten</option><option>Atemschutz</option>';
            break;
        case 'Maschinisten':
            $href = '<option>alle</option><option>aktive</option><option>jugend</option><option selected>Maschinisten</option><option>Atemschutz</option>';
            break;
        case 'Atemschutz':
            $href = '<option>alle</option><option>aktive</option><option>jugend</option><option>Maschinisten</option><option selected>Atemschutz</option>';
            break;
        default:
            $href = '<option>alle</option><option>aktive</option><option>jugend</option><option>Maschinisten</option><option>Atemschutz</option>';
            break;
    }
    echo('            <div class="calendarAddEventInner">
                <img src="images/close.png" style="float:right;" onclick="hideCalendarEditEvent()" height="20" />Edit event:
                <form name="calendarEditEvent" action="javascript:editEventNow()">
                    <table>
                        <tr>
                            <td>Date</td><td><input required name="date" id="datetimepicker4" type="text" placeholder="date" value="'.$date.'" /></td>
                        </tr>
                        <tr>
                            <td>Start</td><td><input required name="start" id="datetimepicker5" type="text" placeholder="time" value="'.$start.'" /></td>
                        </tr>
                        <tr>
                            <td>End</td><td><input required name="end" id="datetimepicker6" type="text" placeholder="time" value="'.$end.'" /></td>
                        </tr>
                        <tr>
                            <td>Name</td><td><textarea required name="name" placeholder="Name of the event">'.$name.'</textarea></td>
                        </tr>
                        <tr>
                            <td>Place</td><td><input required name="place" type="text" placeholder="place" value="'.$place.'" /></td>
                        </tr>
                        <tr>
                            <td>belongs to</td><td><select name="href">'.$href.'</select></td>
                        </tr>
                    </table>
                    <input type="submit" value=" change " /><input type="hidden" name="id" value="'.$id.'" />
                </form>');
}else if(substr($func,0,6) == 'update' && substr($authLevel,0,1) == '1'){
    if(strpos($func,':')>-1){
        $option = substr($func,strpos($func,':') + 1);
        $func = substr($func,0,strpos($func,':'));
        $i=0;
        while(strpos($option,':') > -1){
            $options[$i] =  substr($option,0,strpos($option,':'));
            $option = substr($option,strpos($option,':')+1);
            $i++;
        }
        $options[$i] = $option;
        if(!empty($options) && sizeof($options)>=7){
            $day = substr($options[0],0,strpos($options[0],'.'));
            $options[0] = substr($options[0],strpos($options[0],'.') + 1);
            $month = substr($options[0],0,strpos($options[0],'.'));
            $options[0] = substr($options[0],strpos($options[0],'.') + 1);
            $year = $options[0];
            $start = replaceUml($options[1]);
            $end = replaceUml($options[2]);
            $name = replaceUml($options[3]);
            $place = replaceUml($options[4]);
            $href = replaceUml($options[5]);
            $id = $options[6];
            $que = "UPDATE `".$base."`.`".$table."` SET year='$year',month='$month',day='$day',name='$name',start='$start',end='$end',place='$place',href='$href' WHERE id=$id;";
            echo(mysqli_query($sql, $que) or mysqli_error($sql));
        }else{
            echo('missing Options for function!');
        }
    }else{
        echo('error');
    }
}else if(substr($func,0,4) == 'edit'){
    $que = "DELETE FROM `".$base."`.`".$table."` WHERE id=".substr($func,7).";";
    echo(mysqli_query($sql, $que) or mysqli_error($sql));
}else{
    $que = "SELECT * FROM ".$table." WHERE year=$year";
    $erg = mysqli_query($sql,$que) or die(mysqli_error($sql));
    $event = Array();
    $i=1;
    while($row = mysqli_fetch_array($erg)){
        $eventCount = sizeof($event[$row['year']][$row['month']][$row['day']]);
        $eventCount = $eventCount == ''?0:$eventCount;
        $event[$row['year']][$row['month']][$row['day']][$eventCount]['id'] = $row['id'];
        $event[$row['year']][$row['month']][$row['day']][$eventCount]['name'] = $row['name'];
        $event[$row['year']][$row['month']][$row['day']][$eventCount]['start'] = $row['start'];
        $event[$row['year']][$row['month']][$row['day']][$eventCount]['end'] = $row['end'];
        $event[$row['year']][$row['month']][$row['day']][$eventCount]['place'] = $row['place'];
        $event[$row['year']][$row['month']][$row['day']][$eventCount]['href'] = $row['href'];
    }
    mysqli_free_result($erg);

    $months = array('0','Januar','Februar','M&auml;rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
    if($func == 'admin' && substr($authLevel,0,1) == '1'){
        echo('<div class="calendarAdmin"><div class="calendarEditEventOuter hidden"></div><div class="calendarAddEventOuter hidden">
            <div class="calendarAddEventInner">
                <img src="images/close.png" style="float:right;" onclick="hideCalendarAddEvent()" height="20" />Add event:
                <form name="calendarAddEvent" action="javascript:addEventNow()">
                    <table>
                        <tr>
                            <td>Date</td><td><input required name="date" id="datetimepicker1" type="text" placeholder="date" /></td>
                        </tr>
                        <tr>
                            <td>Start</td><td><input required name="start" id="datetimepicker2" type="text" placeholder="time" /></td>
                        </tr>
                        <tr>
                            <td>End</td><td><input required name="end" id="datetimepicker3" type="text" placeholder="time" /></td>
                        </tr>
                        <tr>
                            <td>Name</td><td><textarea required name="name" placeholder="Name of the event"></textarea></td>
                        </tr>
                        <tr>
                            <td>Place</td><td><input required name="place" type="text" placeholder="place" /></td>
                        </tr>
                        <tr>
                            <td>belongs to</td><td><select name="href"><option>alle</option><option>aktive</option><option>jugend</option><option>Maschinisten</option><option>Atemschutz</option></select></td>
                        </tr>
                    </table>
                    <input type="submit" value=" create " />
                </form>
            </div>
        </div><div class="calendarOnPage" onclick="togglePlugin(\'calendar\')">add to page</div>Calendar:</div><div class="calendarUpdate" onclick="updateCalendar()">update on page</div>
        <div class="calendarAdmin">
            <div class="calendarAddEvent" onclick="showCalendarAddEvent()">Add event</div><div class="calendarViewMode">view mode:');
        echo("<select id='calendarViewMode'onchange='initPlugin_$plugId(0)'>");
        if($href=='alle'){
            echo('<option selected>alle</option>');
        }else{
            echo('<option>alle</option>');
        }
        if($href=='aktive'){
            echo('<option selected>aktive</option>');
        }else{
            echo('<option>aktive</option>');
        }
        if($href=='jugend'){
            echo('<option selected>jugend</option>');
        }else{
            echo('<option>jugend</option>');
        }
        if($href=='Maschinisten'){
            echo('<option selected>Maschinisten</option>');
        }else{
            echo('<option>Maschinisten</option>');
        }
        if($href=='Atemschutz'){
            echo('<option selected>Atemschutz</option>');
        }else{
            echo('<option>Atemschutz</option>');
        }
        echo('</select>
        </div>
        </div>');
        echo('<div class="calendar">');
        for($month=1;$month<13;$month++){
            echo("<div class='calendarTitle'>$months[$month]");
            for($day=1;$day<32;$day++){
                if(sizeof($event[$year][$month][$day]) > 0){
                    for($i = 0;$i<sizeof($event[$year][$month][$day]);$i++){
                        $dayO = $day < 10?'0'.$day:$day;
                        $monthO = $month < 10?'0'.$month:$month;
                        if($href == $event[$year][$month][$day][$i]['href'] || $href == 'alle'){
                            echo("<div class='calendarOuter'><div class='calendarTermin'><div class='calendarDate'>$dayO.$monthO.$year</div>".$event[$year][$month][$day][$i]['name'].'
    <div class="calendarTerminAdmin"><img src="images/bin.png" title="delete" height="18" onclick="delTermin('.$event[$year][$month][$day][$i]['id'].')" />
    <img src="images/pencil.png" title="edit" height="18" onclick="editTermin('.$event[$year][$month][$day][$i]['id'].')" /></div>
    <div class="calendarTime">'.$event[$year][$month][$day][$i]['start'].' Uhr</div><div class="calendarHref">'.$event[$year][$month][$day][$i]['href'].'</div></div></div>');
                        }
                    }
                }
            }
            echo("</div>");
        }
        echo('</div>');
    }else if($func == 'page'){
        echo('<div class="calendar">');
        for($month=1;$month<13;$month++){
            echo("<div class='calendarTitle'>$months[$month] <img src='images/menuOptions.png' height='18' class='imgRotate imgRotated' onclick='$(this).toggleClass(\"imgRotated\");$(\"#calendarGroup$month\").toggleClass(\"invisible\")' />");
            echo("<div id='calendarGroup$month' class='calendarGroup'>");
            for($day=1;$day<32;$day++){
                if(sizeof($event[$year][$month][$day]) > 0){
                    for($i = 0;$i<sizeof($event[$year][$month][$day]);$i++){
                        $dayO = $day < 10?'0'.$day:$day;
                        $monthO = $month < 10?'0'.$month:$month;
                        if($href == $event[$year][$month][$day][$i]['href'] || $href == 'alle'){
                            echo("<div class='calendarOuter'><div class='calendarTermin'><div class='calendarDate'>$dayO.$monthO.$year</div>".$event[$year][$month][$day][$i]['name'].'
    <div class="calendarTime">'.$event[$year][$month][$day][$i]['start'].' Uhr</div><div class="calendarHref">'.$event[$year][$month][$day][$i]['href'].'</div></div></div>');
                        }
                    }
                }
            }
            echo("</div></div>");
        }
        echo('</div>');
    }else if($func == 'side'){
        $year = date('Y');
        $count = 1;
        $maxCount = $_POST['maxCount'];
        $maxCount = $maxCount<3?3:$maxCount;
        echo('<div class="calendarSiteHeader">Termin&uuml;bersicht</div>');
        for($month=date('n');$month<13;$month++){
            for($day=1;$day<32;$day++){
                if(sizeof($event[$year][$month][$day]) > 0){
                    if($month == date('n') && $day < date('j')){
                    }else{
                        for($i = 0;$i<sizeof($event[$year][$month][$day]);$i++){
                            $dayO = $day < 10?'0'.$day:$day;
                            $monthO = $month < 10?'0'.$month:$month;
                            echo("<div class='calendarSiteTitle'>$dayO.$monthO.$year</div><div class='calendarSiteInner'>".$event[$year][$month][$day][$i]['name'].'</div>
    <div class="calendarSiteTime">'.$event[$year][$month][$day][$i]['start'].' Uhr</div>');
                            if(++$count > $maxCount){
                                $month = 99;
                                $day = 99;
                            }
                        }
                    }
                }
            }
        }
        $maxCount+=3;
        echo("<div class='calendarSiteFooter' onclick='loadCalendarSide($maxCount)'>mehr</div>");
    }
}