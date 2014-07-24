$(function(){
    $(".social-sharing .shareBtn.sbMain").click(function(e){
        e.preventDefault();
        console.log("SHARE");
        
        var t = $(this).parents(".social-sharing").find(".shareBtn.animatable"), e = 0, i = function() {
            $(t[e++]).toggleClass("animate"), e < t.length && setTimeout(i, 50);
        };
        $(this).parents(".social-sharing").toggleClass("open"), i();
    });
});