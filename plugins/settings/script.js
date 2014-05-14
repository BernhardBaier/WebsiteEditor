
function initPlugin_1(th){
    if(th != 0){
        resetAllPlugins();
        th.src = th.src.substring(0,th.src.lastIndexOf('/'))+'/active.png';
    }
    $.ajax({
        type: 'POST',
        url: 'plugins/settings/functions.php',
        data: 'function=searchPlugins',
        success: function(data) {
            $('.pluginInner').html(data);
        }
    });
}
function updatePlugin(id){
    $.ajax({
        type: 'POST',
        url: 'plugins/settings/functions.php',
        data: 'function=updatePlugin&id='+id,
        success: function(data) {
            if(data == '1'){
                reloadLocation('showPlugins');
            }else{
                alert(data);
            }
        }
    });
}
function removePlugin(id){
    $.ajax({
        type: 'POST',
        url: 'plugins/settings/functions.php',
        data: 'function=removePlugin&id='+id,
        success: function(data) {
            if(data == '1'){
                reloadLocation('showPlugins');
            }else{
                alert(data);
            }
        }
    });
}