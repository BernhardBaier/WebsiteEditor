/**
 * Created by Bernhard on 10.04.14.
 */
function addPlugin(th){
    resetAllPlugins();
    browsePlugins('../plugins/');
    th.src = th.src.substr(0,th.src.lastIndexOf('/'))+'/active.png';
}
function browsePlugins(path){
    $.ajax({
        type: 'POST',
        url: 'plugins/browse.php',
        data: 'path='+path,
        success: function(data) {
            $('.pluginInner').html('browse to the dir in which the plugin is. Availiable valide plugins will be listed.</br>'+data);
        }
    });
}
function addPluginPath(path){
    $.ajax({
        type: 'POST',
        url: 'plugins/add.php',
        data: 'path='+path.substr(11),
        success: function(data) {
            if(data == '1'){
                if(window.location.href != 'admin.php?id='+pageId+'&showPlugins=true'){
                    reloadLocation('showPlugins');
                }else{
                    window.location.reload();
                }
            }else{
                $('.pluginInner').html(data);
            }
        }
    });
}
function resetAllPlugins(){
    try{
        var imgs = document.getElementsByClassName('pluginNavImg');
        var max = imgs.length;
        for(i=0;i<max;i++){
            var loc = window.location.toString();
            var src = imgs[i].src.replace(loc.substr(0,loc.lastIndexOf('/')+1),'');
            imgs[i].src=src.substring(0,src.lastIndexOf('/'))+'/logo.png';
            imgs[i].className = 'pluginNavImg';
        }
    }catch (ex){}
}