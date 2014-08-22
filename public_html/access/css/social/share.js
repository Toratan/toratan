$(function(){
    $("ul.social-sharing a").click(function(e){
        e.preventDefault();
        window.open($(this).attr("href"), 'newwindow', 'width=600, height=400');
    });
});
//$(function(){
//    $("ul.social-sharing:not(.open) li.shareBtn:not(.sbMain)").removeClass("animated").click(function(e){
//        e.preventDefault();
//        window.open($(this).find("a").attr("href"), 'newwindow', 'width=600, height=400');
//    });
//    $(".social-sharing .shareBtn.sbMain").click(function(e){
//        e.preventDefault();
//        var t = $(this).parents(".social-sharing").find(".shareBtn.animatable"), e = 0, i = function() {
//            var $e = $(t[e++]);
//            if(!$e.hasClass("animated")) {
//                $e.stop().animate({margin: "+=10px", "margin-top": "+=20px"}, 100, function(){$(this).animate({margin: "-=10px", "margin-top": "-=20px"});});
//            }
//            $e.toggleClass("animated"), e < t.length && setTimeout(i, 50);
//        };
//        $(this).parents(".social-sharing").toggleClass("open"), i();
//    });
//});