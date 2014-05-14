
function initPlugin_3(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    var href = document.getElementById('calendarViewMode');
    if(href){
        href = href.options[href.selectedIndex].value;
    }else{
        href = 'alle';
    }
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'year=2014&function=admin&href='+href+'&id=3&lang='+lang,
        success: function(data) {
            $('.pluginInner').html(data);
            if(document.getElementById('editable').innerHTML.search('calendar') > -1){
                $('.calendarOnPage').addClass('selected').html('remove from Page');
            }else{
                $('.calendarOnPage').removeClass('selected').html('add to page');
                $('.calendarUpdate').addClass('hidden');
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
}
function hideCalendarAddEvent(){
    $('.calendarAddEventOuter').addClass('hidden');
}
function hideCalendarEditEvent(){
    $('.calendarEditEventOuter').addClass('hidden');
}
function addEventNow(){
    var elem = document.calendarAddEvent;
    var date = elem.date.value;
    var start = replaceUml(elem.start.value);
    var end = replaceUml(elem.end.value);
    var name = replaceUml(elem.name.value);
    var place = replaceUml(elem.place.value);
    var href = elem.href.options[elem.href.selectedIndex].value;
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'function=insert:'+date+':'+start+':'+end+':'+name+':'+place+':'+href+'&lang='+lang,
        success: function(data) {
            if(data == '1'){
                hideCalendarAddEvent();
                initPlugin_3(0);
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
        url: 'plugins/calendar/calendar.php',
        data: 'function=update:'+date+':'+start+':'+end+':'+name+':'+place+':'+href+':'+elem.id.value+'&lang='+lang,
        success: function(data) {
            if(data == '1'){
                hideCalendarAddEvent();
                initPlugin_3(0);
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
        url: 'plugins/calendar/calendar.php',
        data: 'function=delete:'+document.delTerminForm.id.value+'&lang='+lang,
        success: function(data) {
            if(data == '1'){
                hideMessages();
                initPlugin_3(0);
            }else{
                alert(data);
            }
        }
    });
}
function editTermin(id){
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'function=edit:'+id+'&lang='+lang,
        success: function(data) {
            $('.calendarEditEventOuter').html(data).removeClass('hidden');
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
function updateCalendar(){
    var elem = $('.calendarPage');
    var href = document.getElementById('calendarViewMode');
    if(href){
        href = href.options[href.selectedIndex].value;
    }else{
        href = 'alle';
    }
    if(elem){
        $.ajax({
            type: 'POST',
            url: 'plugins/calendar/calendar.php',
            data: 'year=2014&function=page&href='+href+'&lang='+lang,
            success: function(data) {
                elem.html(data);
                window.setTimeout('saveText()',250);
            }
        });
    }
}
function togglePlugin(name){
    var html = document.getElementById('editable').innerHTML;
    var href = document.getElementById('calendarViewMode');
    if(href){
        href = href.options[href.selectedIndex].value;
    }else{
        href = 'alle';
    }
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'year=2014&function=page&href='+href+'&id=3&lang='+lang,
        success: function(data) {
            if(html.search('<div class="calendarPage">') > -1){
                var vor = html.substr(0,html.search('<div class="calendarPage">'));
                var rep = $('.calendarPage').html();
                html = html.substr(html.search('<div class="calendarPage">'));
                html = html.replace(rep,'');
                document.getElementById('editable').innerHTML = vor + html.substr(html.search('</div>') + 6);
                $('.calendarOnPage').removeClass('selected').html('add to Page');
                $('.calendarUpdate').addClass('hidden');
            }else{
                if(html.search('<h1>') < 5 && html.search('<h1>') > -1){
                    var title = html.substr(0,html.search('</h1>') + 5);
                    var text = html.substr(html.search('</h1>') + 5);
                    document.getElementById('editable').innerHTML = title + '<div class="calendarPage">'+data+'</div>' + text;
                }else{
                    document.getElementById('editable').innerHTML = '<div class="calendarPage">'+data+'</div>' + html;
                }
                $('.calendarOnPage').addClass('selected').html('remove from Page');
                $('.calendarUpdate').removeClass('hidden');
            }
            window.setTimeout('saveText()',250);
        }
    });
}