<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 08.08.14
 * Time: 14:53
 */
include('access.php');
function invalid(){
    echo("This file is no valid vCal file!");
    exit;
}
function get_date($month, $year, $week, $day, $direction = 1) {
    if($direction > 0){
        $startday = 1;
    }
    else{
        $startday = date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    $start = mktime(0, 0, 0, $month, $startday, $year);
    $weekday = date('N', $start);

    if($direction * $day >= $direction * $weekday){
        $offset = -$direction * 7;
    }
    else{
        $offset = 0;
    }

    $offset += $direction * ($week * 7) + ($day - $weekday);
    $tag = ($startday + $offset);
    if($month < 10){
        $month = '0'.$month;
    }
    if($tag < 10){
        $tag = '0'.$tag;
    }
    return $tag.'.'.$month.'.'.$year;

}
function findInArray($arr,$needle){
    for($i=0;$i<sizeof($arr);$i++){
        if($arr[$i] == $needle){
            return $i;
        }
    }
}
if(substr($authLevel,0,1) == '1'){
    if(!file_exists($path)){
        echo('this file does not exist!');
        exit;
    }
    $file = fopen($path,'r');
    $content = fread($file,filesize($path));
    fclose($file);
    $content =  iconv("UTF-8", "ISO-8859-1//IGNORE", $content);
    $olds = ['<und>','<dpp>','ä','ö','ü','Ä','Ö','Ü','ß'];
    $news = ['&',':','&auml;','&ouml;','&uuml;','&Auml;','&Ouml;','&Uuml;','&szlig;'];
    $content = str_replace($olds,$news,$content);
    if(!(strpos($content,'BEGIN:VCALENDAR') > -1)){
        invalid();
    }
    $pos = strpos($content,'BEGIN:VEVENT');
    if(!($pos > -1)){
        invalid();
    }
    $days = ['MO','TU','WE','TH','FR','SA','SU'];
    $count = 0;
    while($pos > -1){
        $content = substr($content,$pos+9);
        $event = substr($content,0,strpos($content,"END:VEVENT"));
        $start[$count] = substr($event,strpos($event,"DTSTART")+7);
        $start[$count] = substr($start[$count],strpos($start[$count],":")+1);
        $start[$count] = substr($start[$count],0,strpos($start[$count],"T")+7);
        $date[$count] = substr($start[$count],0,strpos($start[$count],"T"));
        $date[$count] = substr($date[$count],6,2).'.'.substr($date[$count],4,2).'.'.substr($date[$count],0,4);
        $start[$count] = substr($start[$count],strpos($start[$count],"T")+1,4);
        $start[$count] = substr($start[$count],0,2).':'.substr($start[$count],2);
        $end[$count] = substr($event,strpos($event,"DTEND")+5);
        $end[$count] = substr($end[$count],strpos($end[$count],":")+1);
        $end[$count] = substr($end[$count],0,strpos($end[$count],"T")+7);
        $end[$count] = substr($end[$count],strpos($end[$count],"T")+1,4);
        $end[$count] = substr($end[$count],0,2).':'.substr($end[$count],2);
        $summary[$count] = substr($event,strpos($event,"SUMMARY")+8);
        $summary[$count] = substr($summary[$count],0,strpos($summary[$count],PHP_EOL));
        $loc[$count] = substr($event,strpos($event,"LOCATION")+9);
        $loc[$count] = substr($loc[$count],0,strpos($loc[$count],PHP_EOL));
        if(str_replace(array("\r\n", "\r", "\n", " "),'',$loc[$count]) == ''){
            $loc[$count] = $defaultLocation;
        }
        if(strpos($event,'RRULE') > -1){
            $rule[$count] = substr($event,strpos($event,"RULE")+5);
            $rule[$count] = substr($rule[$count],0,strpos($rule[$count],PHP_EOL));
            $rule[$count] = str_replace(array("\r\n", "\r", "\n", " "),'',$rule[$count]);
        }else{
            $rule[$count] = '';
        }

        $count++;
        $pos=strpos($content,'BEGIN:VEVENT');
    }
    $terminCount = 0;
    for($i=0;$i<$count;$i++){
        $termine[$terminCount] = str_replace(array("\r\n", "\r", "\n"), '','<div class="event">'.$date[$i].' '.$start[$i].' - '.$end[$i].' '.$summary[$i].' at '.$loc[$i].'<div class="addEvent" id="addEvent'.$terminCount.'" onclick="insertEvent(\''.$date[$i].'\',\''.$start[$i].'\',\''.$end[$i].'\',\''.$summary[$i].'\',\''.$loc[$i].'\',\''.$belongsTo.'\');">Add</div></div>');
        $terminCount++;
        if($rule[$i] != ''){
            if(strpos($rule[$i],'MONTHLY') > -1){
                $rule[$i] = substr($rule[$i],strpos($rule[$i],'MONTHLY')+8);
                if(strpos($rule[$i],'BYDAY') > -1){
                    $rule[$i] = substr($rule[$i],strpos($rule[$i],'BYDAY')+6);
                    $startYear = intval(substr($date[$i],6,4));
                    $startMonth = intval(substr($date[$i],3,2))+1;
                    $endMonth = 13;
                    $endYear = ($startYear + 1);
                    $dateCount = 0;
                    $day = substr($rule[$i],-2);
                    $weeks = str_replace($day,'',$rule[$i]);
                    $numOfDay = findInArray($days,$day)+1;
                    if(strpos($rule[$i],'-') > -1){
                        $weeks = substr($weeks,1);
                        for($j=$startYear;$j<$endYear;$j++){
                            for($k=$startMonth;$k<$endMonth;$k++){
                                $dates[$dateCount] = get_date($k,$j,$weeks,$numOfDay,-1);
                                $termine[$terminCount] = str_replace(array("\r\n", "\r", "\n"), '','<div class="event">'.$dates[$dateCount].' '.$start[$i].' - '.$end[$i].' '.$summary[$i].' at '.$loc[$i].'<div class="addEvent" id="addEvent'.$terminCount.'" onclick="insertEvent(\''.$dates[$dateCount].'\',\''.$start[$i].'\',\''.$end[$i].'\',\''.$summary[$i].'\',\''.$loc[$i].'\',\''.$belongsTo.'\')">Add</div></div>');
                                $dateCount++;
                                $terminCount++;
                            }
                            $startMonth = 1;
                        }
                    }else{
                        for($j=$startYear;$j<$endYear;$j++){
                            for($k=$startMonth;$k<$endMonth;$k++){
                                $dates[$dateCount] = get_date($k,$j,$weeks,$numOfDay);
                                $termine[$terminCount] = str_replace(array("\r\n", "\r", "\n"), '','<div class="event">'.$dates[$dateCount].' '.$start[$i].' - '.$end[$i].' '.$summary[$i].' at '.$loc[$i].'<div class="addEvent" id="addEvent'.$terminCount.'" onclick="insertEvent(\''.$dates[$dateCount].'\',\''.$start[$i].'\',\''.$end[$i].'\',\''.$summary[$i].'\',\''.$loc[$i].'\',\''.$belongsTo.'\')">Add</div></div>');
                                $dateCount++;
                                $terminCount++;
                            }
                            $startMonth = 1;
                        }
                    }
                }
            }else if(strpos($rule[$i],'WEEKLY') > -1){
                $rule[$i] = substr($rule[$i],strpos($rule[$i],'WEEKLY')+7);
                $endDate = false;
                if(strpos($rule[$i],'UNTIL') > -1){
                    $rule[$i] = substr($rule[$i],strpos($rule[$i],'UNTIL')+6);
                    $endDate = substr($rule[$i],0,strpos($rule[$i],';'));
                    $endDate = substr($endDate,0,8);
                    $rule[$i] = substr($rule[$i],strpos($rule[$i],';')+1);
                }
                $interval = intval(substr($rule[$i],strpos($rule[$i],'=')+1,strpos($rule[$i],';')-(strpos($rule[$i],'=')+1)));
                $startYear = intval(substr($date[$i],6,4));
                $startMonth = intval(substr($date[$i],3,2));
                $startDay = intval(substr($date[$i],0,2));
                if($endDate!==false){
                    $endYear = substr($endDate,0,4);
                    $endMonth = substr($endDate,4,2);
                    $endDay = substr($endDate,6,2);
                    echo($endDate);
                }else{
                    $endYear = $startYear;
                    $endMonth = 12;
                    $endDay = 31;
                }
                $continue = true;
                $daysAdded = 0;
                while($continue){
                    $daysAdded += $interval * 7;
                    $dateN = mktime(0, 0, 0, $startMonth, $startDay+$daysAdded, $startYear);
                    if(intval(date("Ymd",$dateN)) > intval($endYear.$endMonth.$endDay)){
                        $continue = false;
                    }else{
                        $dateN = date("d.m.Y",$dateN);
                        $termine[$terminCount] = str_replace(array("\r\n", "\r", "\n"), '','<div class="event">'.$dateN.' '.$start[$i].' - '.$end[$i].' '.$summary[$i].' at '.$loc[$i].'<div class="addEvent" id="addEvent'.$terminCount.'" onclick="insertEvent(\''.$dateN.'\',\''.$start[$i].'\',\''.$end[$i].'\',\''.$summary[$i].'\',\''.$loc[$i].'\',\''.$belongsTo.'\')">Add</div></div>');
                        $terminCount++;
                    }
                }
                $dateCount = 0;
            }
        }
    }
    echo('<div class="eventFooter"><div class="but1" onclick=addAll('.$terminCount.')>Add all</div><div class="but2" onclick="parent.showYear(0)">Update calendar</div></div>');
    for($i=0;$i<$terminCount;$i++){
        echo($termine[$i]);
    }
    unlink($path);
}