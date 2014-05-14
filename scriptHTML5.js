/**
 * Created by Bernhard on 20.01.14.
 */
function init(){
    if(correctRightBar){
        $('.rightBar').height($('.content').height());
    }
    loadCalendarSide(3);
    window.setTimeout('postInit()',250);
}
function loadCalendarSide(count){
    $.ajax({
        type: 'POST',
        url: 'plugins/calendar/calendar.php',
        data: 'maxCount='+count+'&function=side',
        success: function(data) {
            $('.calendarSide').html(data);
        }
    });
}
function postInit(){
    window.setTimeout('initPicViewer()',25);
}