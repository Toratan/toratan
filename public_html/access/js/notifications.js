$(document).ready(function(){
    window.add_js ("/access/js/jquery.relative.js");
    var last_pull = 0; 
    $.ajax({
        url: "/notifications/pull",
        type: "POST",
        data: { ajax: "", since: last_pull },
        dataType: "json",
        success: function(data) {
            last_pull = new Date();
            console.log(data);
            for(var $i = 0;$i<data.length;$i++)
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
                        alert(i.item_type);
                }
            }  
        }
    });
});
/**
 * Changes the first letter to upper case and leave the others as is
 * @returns string
 */
String.toFirstLetterUpperCase = function(string) { return string[0].toUpperCase()+string.substr(1,string.length); };
window.build_notification_txt = function(i)
{
    console.log(i);
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
    localtime = (window.format_date(i.created_at));//));
    console.log(localtime);
    switch(i.notification_type)
    {
        case 0:
            txt ="<img src='"+avatar+"' height='40' width='40' class='pull-left img-rounded' onerror='this.src=\"/access/img/anonymous-male.jpg\"' /> "+
                    "<div class='text-muted pull-left' style='margin:2.5%;margin-left:3%'><b><a href='/profile/"+i.user.user_id+"' target='__blank'>"+String.toFirstLetterUpperCase(i.user.username)+"</a></b> Shared:</div><div class='clearfix'></div>"+
                    "<div style='margin-left:12%'><span class='"+icon+"'></span> " +
                    "<a style='' href='"+link+"' target='__blank'>"+i.item.item_title+"</a>" + detail + 
                    "<div class='clearfix'></div></div>"+
                    "<time class='text-muted pull-right timeago' datetime="+localtime+">"+localtime+"</time>";
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
    return date = (new Date(Date.parse(date_string))).toLocaleString();
    split = date.split(" ");
    time = split[1].split(":");
    if(split[2] === "PM") 
    {
        time[0] = (parseInt (time[0]) + 12).toString();
    }
    split[1] = time.join(":");
    split.splice(2);
    return split.join(" ");
};