$(function(){ 

    $('.box-new-hot li').mouseenter(function(){
        $(this).addClass('on').siblings().removeClass('on');
        $('.boxinfo .box-inner').eq($(this).index()).show().siblings().hide();
    });

    
    $('#typedown li a').click(function(){
        var name = $(this).html();
        var kid = $(this).attr('kid');
        console.log(name);
        $('#keyword').attr('key-type',kid);
        $('.search-btn').attr('key-type',kid);
        $('#keyword-float').attr('key-type',kid);
        $('.searchtype span').text(name);
        $('#typedown').hide();
        $('#typedown li').show();
        $('.allkid'+kid).hide();
        var searchname = '搜索'+name;
        console.log(searchname);
        $('#keyword-float').attr('placeholder',searchname);
    });

    $('.searchtype').mouseover(function(){
        $('#typedown').show();
    });
    $('#typedown').mouseleave(function(){
        $('#typedown').hide();
    })
     $('.searchtype').mouseleave(function(){
        $('#typedown').hide();
    })
    $('#typedown').mouseover(function(){
        $('#typedown').show();
    });
    function chview(){
        $('.gengduo').each(function(i,e){
            var t = $(e).siblings('.uli');
            var c = '44px';
            var l = t.find('a:last');
            if (l.position().top > 0) {
                if (c==t.css('height') && t.find('.current').position().top > 0) {
                    t.css({height:'auto'});
                    $(e).html('收起<i></i>');
                    $(e).find('i').css({backgroundPosition:'-68px -735px'});
                }
                $(e).show();
            } else {
                $(e).hide();
            }
        });
    }
    chview();
    $('.gengduo').on('click', function() {
        var t = $(this).siblings('.uli');
        var c = h = '44px';
        var p = '-68px -720px';
        var m = '更多';
        if (c==t.css('height')) {
            h = 'auto';
            p = ' -68px -735px';
            m = '收起';
        }
        t.css({height:h});
        $(this).html(m+'<i></i>');
        $(this).find('i').css({backgroundPosition:p});
    });
    $(window).scroll(function() {
        var scrollheight =  parseInt($(document).scrollTop());
        if (scrollheight>=400) { 
            $('.header').css({'position': 'fixed','top':"0px"});
            $('.header').show();
        } else {  
            $('.header').css({'position': 'unset'});
            
        } 

    });
     
});