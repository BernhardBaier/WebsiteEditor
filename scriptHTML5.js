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
function searchNow(){
    var que = document.search.searchInput.value;
    if(que != ''){
        $.ajax({
            type: 'POST',
            url: 'search.php',
            data: 'lang='+lang+'&que='+que+'&path='+preview,
            success: function(data) {
	            data = data == 'Search results:'?data+'<br>No results found!':data;
                $('.searchResultsInner').html(data);
	            $('.searchOuter').removeClass('hidden');
            }
        });
    }
}
function navigateToPageById(id){
	location.href = 'index.php?id='+id+'&lang='+lang;
}