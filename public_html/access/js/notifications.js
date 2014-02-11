$(document).ready(function(){
    window.add_js ("/access/js/jquery.timeago.min.js");
    window.pull_notification ();
    setInterval ("window.pull_notification ();", 10000);
});
window.pull_notification = function(){
    if(typeof window.last_pull  === "undefined")
            window.last_pull = 0;
    window.pull_internal_in_effect = 0;
    url = "/notifications/pull/since/"+last_pull;
    console.log(url);
    $.ajax({
        url: url,
        type: "POST",
        data: { ajax: "", since: last_pull },
        dataType: "json",
        success: function(data) {
            window.last_pull = (new Date()).getTime() / 1000;
            window.pulls = window.pulls || [];
            data = data.concat(window.pulls);
            data.sort(window.custom_sort);
            console.log(data);
            for(var $i = data.length-1;$i>0;$i--)
            {
                var i = data[$i];
                switch(i.item_type)
                {
                    case "note":
                    case "link":
                    case "folder":                                        
                        var txt = window.build_notification_txt(i);
                        if(!txt)
                            continue;
                        $("div#feed-reader-section .feed-container").append(txt);
                        break;
                    default:
                        //alert(i.item_type);
                }
                if(window.pull_internal_in_effect !== 1)
                {
                    window.pull_internal_in_effect = 1;
                    $('time.timeago').timeago();
                    setInterval ("$('time.timeago').timeago();", 15000);
                }
            }  
        }
    });
};
window.custom_sort = function(a, b) {
    return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
}
/**
 * Changes the first letter to upper case and leave the others as is
 * @returns string
 */
String.toFirstLetterUpperCase = function(string) { return string[0].toUpperCase()+string.substr(1,string.length); };
window.build_notification_txt = function(i)
{
    var txt = "";
    var link = "";
    var detail = "";
    var icon = "";
    switch(i.item_type)
    {
        case "folder":
            link = "/u/"+i.user_id+"/directory/"+i.item_id;
            icon = "glyphicon glyphicon-folder-close";
            break;
        case "note":
            link = "/view/note/"+i.item_id;
            detail = "<div style='font-size:small;margin-top:1%;padding:0;padding:10px;border-left:5px solid #eeeeee' class='text-muted'>"+i.item.item_body.substr(0, 130)+" <small>[ <a href='"+link+"' target='__blank'>Read more</a> ]</small></div>";
            icon = "glyphicon glyphicon-file";
            break;
        case "link":
            link = i.item.item_body;
            icon = "glyphicon glyphicon-bookmark";
            break;
    }
    avatar = i.user.profile.avatar.thumbnail;
    // we use UTC time zone
    localtime = i.created_at;
    switch(i.notification_type)
    {
        case 0:
            txt ="<img src='"+avatar+"' height='40' width='40' class='pull-left img-rounded' onerror='this.src=\"/access/img/anonymous-male.jpg\"' /> "+
                    "<div class='text-muted pull-left' style='margin:2.5%;margin-left:3%'><b><a href='/profile/"+i.user.user_id+"' target='__blank'>"+String.toFirstLetterUpperCase(i.user.username)+"</a></b> Shared:</div><div class='clearfix'></div>"+
                    "<div style='margin-left:12%'><span class='"+icon+"'></span> " +
                    "<a style='' href='"+link+"' target='__blank'>"+i.item.item_title+"</a>" + detail + 
                    "<div class='clearfix'></div></div>"+
                    "<time class='text-muted pull-left' datetime="+localtime+">"+(i.created_at)+"</time>"+
                    "<time class='text-muted pull-right timeago' datetime="+localtime+">"+window.format_date (i.created_at)+"</time>";
            break;
        default:
            console.log ("Undefined notification type ID# "+i.notification_type);
            return false;
    }
    return txt+"<hr />";
};
window.add_js = function(jsfile){
    var js = document.createElement("script");
    js.type = "text/javascript";
    js.src = jsfile;
    document.body.appendChild(js);
};
window.format_date = function(date_string){
    obj_date = new Date(Date.parse(date_string));
    date = obj_date.toLocaleString ();
    split = date.split(" ");
    time = split[1].split(":");
    if(split[2] === "PM") 
        time[0] = (parseInt (time[0]) + 12).toString();
    var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];
    month = monthNames[obj_date.getMonth ()];
    split[0] = split[0].split("/");
    split[0][1] = split[0][0];
    split[0][0] = month;
    split[0] = split[0].join("-");
    split[1] = "@";
    split[2] = time.join(":");
    return split.join(" ");
};