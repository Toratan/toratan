/** inline injection of jquery.timeago.min.js **/
(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a)}else{a(jQuery)}}(function(d){d.timeago=function(h){if(h instanceof Date){return a(h)}else{if(typeof h==="string"){return a(d.timeago.parse(h))}else{if(typeof h==="number"){return a(new Date(h))}else{return a(d.timeago.datetime(h))}}}};var g=d.timeago;d.extend(d.timeago,{settings:{refreshMillis:60000,allowFuture:false,localeTitle:false,cutoff:0,strings:{prefixAgo:null,prefixFromNow:null,suffixAgo:"ago",suffixFromNow:"from now",seconds:"Less than a minute",minute:"About a minute",minutes:"%d minutes",hour:"About an hour",hours:"About %d hours",day:"a day",days:"%d days",month:"About a month",months:"%d months",year:"About a year",years:"%d years",wordSeparator:" ",numbers:[]}},inWords:function(n){var o=this.settings.strings;var k=o.prefixAgo;var s=o.suffixAgo;if(this.settings.allowFuture){if(n<0){k=o.prefixFromNow;s=o.suffixFromNow}}var q=Math.abs(n)/1000;var h=q/60;var p=h/60;var r=p/24;var l=r/365;function j(t,v){var u=d.isFunction(t)?t(v,n):t;var w=(o.numbers&&o.numbers[v])||v;return u.replace(/%d/i,w)}var m=q<45&&j(o.seconds,Math.round(q))||q<90&&j(o.minute,1)||h<45&&j(o.minutes,Math.round(h))||h<90&&j(o.hour,1)||p<24&&j(o.hours,Math.round(p))||p<42&&j(o.day,1)||r<30&&j(o.days,Math.round(r))||r<45&&j(o.month,1)||r<365&&j(o.months,Math.round(r/30))||l<1.5&&j(o.year,1)||j(o.years,Math.round(l));var i=o.wordSeparator||"";if(o.wordSeparator===undefined){i=" "}return d.trim([k,m,s].join(i))},parse:function(i){var h=d.trim(i);h=h.replace(/\.\d+/,"");h=h.replace(/-/,"/").replace(/-/,"/");h=h.replace(/T/," ").replace(/Z/," UTC");h=h.replace(/([\+\-]\d\d)\:?(\d\d)/," $1$2");h=h.replace(/([\+\-]\d\d)$/," $100");return new Date(h)},datetime:function(i){var h=g.isTime(i)?d(i).attr("datetime"):d(i).attr("title");return g.parse(h)},isTime:function(h){return d(h).get(0).tagName.toLowerCase()==="time"}});var e={init:function(){var i=d.proxy(c,this);i();var h=g.settings;if(h.refreshMillis>0){this._timeagoInterval=setInterval(i,h.refreshMillis)}},update:function(h){var i=g.parse(h);d(this).data("timeago",{datetime:i});if(g.settings.localeTitle){d(this).attr("title",i.toLocaleString())}c.apply(this)},updateFromDOM:function(){d(this).data("timeago",{datetime:g.parse(g.isTime(this)?d(this).attr("datetime"):d(this).attr("title"))});c.apply(this)},dispose:function(){if(this._timeagoInterval){window.clearInterval(this._timeagoInterval);this._timeagoInterval=null}}};d.fn.timeago=function(j,h){var i=j?e[j]:e.init;if(!i){throw new Error("Unknown function name '"+j+"' for timeago")}this.each(function(){i.call(this,h)});return this};function c(){var i=b(this);var h=g.settings;if(!isNaN(i.datetime)){if(h.cutoff==0||f(i.datetime)<h.cutoff){d(this).text(a(i.datetime))}}return this}function b(h){h=d(h);if(!h.data("timeago")){h.data("timeago",{datetime:g.datetime(h)});var i=d.trim(h.text());if(g.settings.localeTitle){h.attr("title",h.data("timeago").datetime.toLocaleString())}else{if(i.length>0&&!(g.isTime(h)&&h.attr("title"))){h.attr("title",i)}}}return h.data("timeago")}function a(h){return g.inWords(f(h))}function f(h){return(new Date().getTime()-h.getTime())}document.createElement("abbr");document.createElement("time")}));
/** inline inject of jquery.insert_sorted.js **/
(function($){
    /**
     * add a new item into a parent node in a sorted manner using binary sort strategy
     * @param jQuery $new the new node's jquery object
     * @param function $fetch_val the function for making decision when sorting them
     * @returns jQuery the inserted item
     **/
    $.fn.insert_sorted = function($new, $validate)
    {
        $parent = this;
        // if this is the first of its siblings
        if(($parent.children()).length === 0)
        {
            // just append it
            $parent.prepend($new);
            // return the appened item
            return $new;
        }
        // fetch the children
        children = $parent.children ();
        // fetch the value of the first child
        // if the new is greater than first child's value
        if($validate($new, $(children[0])) > 0)
        {
            // just append the new one before the first child
            ($new).insertBefore($(children[0]));
            // return the appened item
            return $new;
        }
        // if the new is less than last child's value
        if($validate($new, $(children[children.length - 1])) < 0)
        {
            // just append the new one before the first child
            ($new).insertAfter($(children[children.length - 1]));
            // return the appened item
            return  $new;
        }
       /**
        * init the binery search values
        */
        // the bs' top value
        itop = children.length;
        // the bs' bottom value
        ibot = 0;
        // a fail-safe for making bs recursively decision-able
        counter = 0;
        // here the bs goes
        do
        {
            // validate the medium value of { bottom, top }
            imid = Math.floor((itop + ibot) / 2);
            /**
             * Validate the new child's position from the medium child
             */
            if($validate($new, $(children[imid])) > 0)
                itop = imid;
            else
                ibot = imid;
            // the recursive's fail-safe
            if(counter++ > children.length)
                break;
            // the condition for terminating the recursive
        }while(Math.abs(ibot - itop) > 1);
        /**
         * now that bs' terminated
         */
        // if the new child's value is greater than the bs' last fetched child's value
        if($validate($new, $(children[imid])) > 0)
            // insert the new child after it
            ($new).insertAfter($(children[ibot]));
        // if the new child's value is less or equal than the bs' last fetched child's value
        else
            // insert the new child before it
            ($new).insertBefore($(children[ibot]));
        // return the inserted value
        return $new;
    };
})(jQuery);
$(document).ready(function(){
    window.add_css("/access/css/notification.css");
    window.pull_fetching_txt = '<div class="text-center text-muted" style="font-variant: small-caps"><img src="/access/img/ajax-loader.gif"/> Fetching feeds ....</div>';
    $('<div id=\'feed-reader-section\' class=\'pull-left col-lg-3 col-md-3 col-sm-2 hidden-sm hidden-xs\'>\n\
                <h3><span class=\'glyphicon glyphicon-flash\'></span> Feeds</h3>\n\
                <div class=\'feed-container\'>'+window.pull_fetching_txt+'</div>\n\
            </div>').appendTo ("div.container");
    window.pull_notification ();
    setInterval ("window.pull_notification ();", 15000);
});
window.pull_notification = function(){
    if(typeof window.last_pull  === "undefined")
    {
            window.last_pull = 0;
            window.pull_internal_in_effect = 0;
            window.pull_limit = 10;
            window.pull_offset = 0;
    }
    url = "/notifications/pull/l/"+window.pull_limit+"/o/"+window.pull_offset+"/since/"+last_pull;
    console.log(url);
    $.ajax({
        url: url,
        type: "POST",
        data: { ajax: "", since: last_pull },
        dataType: "json",
        success: function(data) {
            if(window.pull_internal_in_effect === 0)
                $("div#feed-reader-section .feed-container").html("");
            window.last_pull = (new Date()).getTime() / 1000;
            window.last_pull_was_empty = (data.length === 0);
            window.pulls = window.pulls || [];
            window.pulls = data.concat(window.pulls);
            for(var $i = data.length-1;$i>=0;$i--)
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
                        $("div#feed-reader-section .feed-container").insert_sorted($(txt),window.fetch_id).fadeIn(1000);
                        $('time.timeago').timeago();
                        break;
                    default:
                        throw (i.item_type);
                }
                if(window.pull_internal_in_effect !== 1)
                {
                    window.pull_internal_in_effect = 1;
                    setInterval ("$('time.timeago').timeago();", 15000);
                }
            }
            $("div#feed-reader-section #read-more").remove ();
            $("div#feed-reader-section .feed-container").parent ().append('<div id="read-more" class="block  alert-inafo text-center text-muted"  style="cursor:pointer" onclick=\'window.pull_fetch_more(this);\'><a><span class="glyphicon glyphicon-plus"></span> Read More</a></div>');
        }
    });
};
window.fetch_id = function($node1, $node2){
    if(!($node1).hasClass ("notif-block") || $node1.children("div.timestamp").children("time.timeago").length === 0) { console.log($node1); throw "The previous log item has not a valid fomat"; }
    if(!($node2).hasClass ("notif-block") || $node2.children("div.timestamp").children("time.timeago").length === 0) { console.log($node2); throw "The previous log item has not a valid fomat"; }
    return new Date($node1.children("div.timestamp").children("time.timeago").attr ("datetime")) -
        new Date($node2.children("div.timestamp").children("time.timeago").attr ("datetime")).getTime ();
};
window.pull_fetch_more = function($this)
{
    window.pull_offset = (window.pull_offset + window.pull_limit);
    last_pull=window.last_pull;
    window.last_pull=0;
    $($this).html(window.pull_fetching_txt);
    window.pull_notification ();
    window.last_pull=last_pull;
};
window.custom_sort = function(a, b) {
    return new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
};
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
            txt ="<div class='notif-block' id='notif-"+i.item_id.toString ()+"' style='display:none'><img src='"+avatar+"' height='40' width='40' class='pull-left img-rounded' onerror='this.src=\"/access/img/anonymous-male.jpg\"' /> "+
                    "<div class='text-muted pull-left' style='margin:2.5%;margin-left:3%'><b><a href='/profile/"+i.user.user_id+"' target='__blank'>"+String.toFirstLetterUpperCase(i.user.username)+"</a></b> Shared:</div><div class='clearfix'></div>"+
                    "<div style='margin-left:12%'><span class='"+icon+"'></span> " +
                    "<a style='' href='"+link+"' target='__blank'>"+i.item.item_title+"</a>" + detail +
                    "<div class='clearfix'></div></div>"+
                    "<div class='text-muted timestamp'>"+
                        "<time class='pull-left' datetime="+localtime+">"+window.format_date(i.created_at).replace("@", "")+"</time>"+
//                        "<time class='pull-left' datetime="+localtime+">"+i.created_at+"</time>"+
                        "<time class='pull-right timeago' datetime='"+localtime+"' local-datetime='"+new Date(Date.parse(i.created_at)).toLocaleString()+"'>"+window.format_date (i.created_at)+"</time>"+
                    "</div><div class='clearfix'></div></div>";
            $('div#notif-'+i.item_id.toString ()).remove ();
            break;
        default:
            throw ("Undefined notification type ID# "+i.notification_type);
            return false;
    }
    return txt;
};
window.add_js = function(jsfile){
    var js = document.createElement("script");
    js.type = "text/javascript";
    js.src = jsfile;
    document.body.appendChild(js);
};
window.add_css = function(cssfile){
    var css = document.createElement("link");
    css.rel = "stylesheet";
    css.href = cssfile;
    document.body.appendChild(css);
};
window.format_date = function(date_string){
    obj_date = new Date(Date.parse(date_string));
    date = obj_date.toLocaleString ();
    split = date.split(" ");
    time = split[1].split(":");
    if(split[2] === "PM" && time[0] !== "12")
        time[0] = (parseInt (time[0]) + 12).toString();
    if(split[2] === "AM" && time[0] === "12")
        time[0] = "00";
    var monthNames = [ "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];
    month = monthNames[obj_date.getMonth ()];
    split[0] = split[0].split("/");
    split[0][0] = month;
    split[0] = split[0].join("-");
    split[1] = "@";
    split[2] = time.join(":");
    return split.join(" ");
};