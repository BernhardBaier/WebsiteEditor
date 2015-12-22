<?php
/**
 * Created by PhpStorm.
 * User: Bernhard
 * Date: 29.03.14
 * Time: 11:54
 */
$que2 = "SELECT * FROM plugins WHERE name='calendar';";
$erg2 = mysqli_query($sql,$que2);
while($row = mysqli_fetch_array($erg2)){
    $location = $row['location'];
    $location = substr($location,0,strlen($location)-1);
    $name = $row['name'];
    $plugId = $row['id'];
}
echo("<img src='$location/images/logo.png' title='$name' class='pluginNavImg' onclick='initPlugin_$plugId(this);$(this).addClass(\"active\")'/>");
mysqli_free_result($erg2);
if(!file_exists("$location/script.js")){
    $output="
function initPlugin_$plugId(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    var jetzt = new Date();
    showYear(jetzt.getFullYear());
}
var pluginCalendarCurrentYear = 0;
function showYear(year){
    year = year == 0?getCookie('year'):year;
    if(parseInt(year)<200){
        year = '2'+year.toString();
    }
    var jetzt = new Date();
    year = year == 'NULL'?jetzt.getFullYear():year;
    pluginCalendarCurrentYear = year;
    setCookie('year',year.toString(),1);
    var href = document.getElementById('calendarViewMode');
    if(href){
        href = href.options[href.selectedIndex].value;
    }else{
        href = 'alle';
    }
    $.ajax({
        type: 'POST',
        url: '$location/calendar.php',
        data: 'year='+year+'&function=admin&href='+href+'&id=$plugId&lang='+lang,
        success: function(data) {
            $('.pluginInner').html('<div class=\"overlayCalendar hidden\" onclick=\"calendarHideAll()\"></div>'+data);
            if(getCurrentHTML().search('{#insertPluginCalendar') > -1){
                $('.calendarOnPage').addClass('selected').html('remove from Page');
            }else{
                $('.calendarOnPage').removeClass('selected').html('add to page');
            }
            $('#datetimepicker1').datetimepicker({
                lang:'de',
                i18n:{
                    de:{
                        months:[
                            'Januar','Februar','März','April',
                            'Mai','Juni','Juli','August',
                            'September','Oktober','November','Dezember',
                        ],
                        dayOfWeek:[
                            'So.', 'Mo', 'Di', 'Mi',
                            'Do', 'Fr', 'Sa.',
                        ]
                    }
                },
                timepicker:false,
                format:'d.m.Y'
            });
            $('#datetimepicker2').datetimepicker({
                datepicker:false,
                format:'H:i'
            });
            $('#datetimepicker3').datetimepicker({
                datepicker:false,
                format:'H:i'
            });

        }
    });
}
function showCalendarAddEvent(){
    $('.calendarAddEventOuter').removeClass('hidden');
    positionCalendarBox();
}
function pluginCalendarAddEvent(month){
    document.calendarAddEvent.date.value = '1.'+month+'.'+pluginCalendarCurrentYear;
    showCalendarAddEvent();
}
function positionCalendarBox(){
    $('.overlayCalendar').removeClass('hidden').css('top',document.getElementsByClassName('pluginInner')[0].scrollTop);
    $('.calendarEditEventOuter').css('top',document.getElementsByClassName('pluginInner')[0].scrollTop + 35);
    $('.calendarAddEventOuter').css('top',document.getElementsByClassName('pluginInner')[0].scrollTop + 35);
}
function calendarHideAll(){
     $('.calendarAddEventOuter').addClass('hidden');
     $('.calendarEditEventOuter').addClass('hidden');
     $('.overlayCalendar').addClass('hidden');
}
function addEventNow(){
    var elem = document.calendarAddEvent;
    var date = elem.date.value;
    var start = replaceUml(elem.start.value);
    var end = replaceUml(elem.end.value);
    var name = replaceUml(elem.name.value);
    var place = replaceUml(elem.place.value);
    var href = elem.href.options[elem.href.selectedIndex].value;
    var day = date.substr(0,date.indexOf('.'));
    date = date.substr(date.indexOf('.')+1);
    var month = date.substr(0,date.indexOf('.'));
    date = date.substr(date.indexOf('.')+1);
    var year = date;
    day = day.length == 1?'0'+day:day;
    month = month.length == 1?'0'+month:month;
    year = year.length == 2?'20'+year:year;
    date = day+'.'+month+'.'+year;
    $.ajax({
        type: 'POST',
        url: '$location/calendar.php',
        data: 'function=insert:'+date+':'+start+':'+end+':'+name+':'+place+':'+href+'&lang='+lang,
        success: function(data) {
            if(data == '1'){
                calendarHideAll();
                showYear(date.substring(6));
            }else{
                alert(data);
            }
        }
    });
}
function editEventNow(){
    var elem = document.calendarEditEvent;
    var date = elem.date.value;
    var start = replaceUml(elem.start.value);
    var end = replaceUml(elem.end.value);
    var name = replaceUml(elem.name.value);
    var place = replaceUml(elem.place.value);
    var href = elem.href.options[elem.href.selectedIndex].value;
    $.ajax({
        type: 'POST',
        url: '$location/calendar.php',
        data: 'function=update:'+date+':'+start+':'+end+':'+name+':'+place+':'+href+':'+elem.id.value+'&lang='+lang,
        success: function(data) {
            if(data == '1'){
                calendarHideAll();
                showYear(date.substring(6));
            }else{
                alert(data);
            }
        }
    });
}
function delTermin(id){
    document.delTerminForm.id.value = id;
    $('.deleteTermin').removeClass('hidden');
    $('.overlay').removeClass('hidden');
    positionMessage();
}
function delTerminNow(){
    $.ajax({
        type: 'POST',
        url: '$location/calendar.php',
        data: 'function=delete:'+document.delTerminForm.id.value+'&lang='+lang,
        success: function(data) {
            if(data == '1'){
                hideMessages();
                showYear(0);
            }else{
                alert(data);
            }
        }
    });
}
function editTermin(id){
    $.ajax({
        type: 'POST',
        url: '$location/calendar.php',
        data: 'function=edit:'+id+'&lang='+lang,
        success: function(data) {
            $('.calendarEditEventOuter').html(data).removeClass('hidden');
            positionCalendarBox();
            $('#datetimepicker4').datetimepicker({
                lang:'de',
                i18n:{
                    de:{
                        months:[
                            'Januar','Februar','März','April',
                            'Mai','Juni','Juli','August',
                            'September','Oktober','November','Dezember',
                        ],
                        dayOfWeek:[
                            'So.', 'Mo', 'Di', 'Mi',
                            'Do', 'Fr', 'Sa.',
                        ]
                    }
                },
                timepicker:false,
                format:'d.m.Y'
            });
            $('#datetimepicker5').datetimepicker({
                datepicker:false,
                format:'H:i'
            });
            $('#datetimepicker6').datetimepicker({
                datepicker:false,
                format:'H:i'
            });
        }
    });
}
function togglePlugin(name){
    var html = getCurrentHTML();
    var href = document.getElementById('calendarViewMode');
    if(href){
        href = href.options[href.selectedIndex].value;
    }else{
        href = 'alle';
    }
    var replace = '{#insertPluginCalendar_'+href+'_'+lang+'#}';
    if(html.search(replace) > -1){
        html = html.replace(replace,'');
        setEditorHTML(html);
        $('.calendarOnPage').removeClass('selected').html('add to page');
    }else{
        $('.calendarOnPage').addClass('selected').html('remove from Page');
        insertHTMLatTheEnd(replace);
    }
    saveText('content',false);
}
function parsed(){
    $('.calendarImport').addClass('big');
}";
    $file = fopen("$location/script.js",'w');
    fwrite($file,$output);
    fclose($file);
}
echo("
<script src='$location/script.js'></script>
<link rel='stylesheet' href='$location/stylePluginCalendar.min.css' />
<link rel='stylesheet' href='$location/style.min.css' />
");