$('.qiandao').click(function(){
    if(!isLogin){
        login();
        return false;
    }
    $('#mengcheng').show();
    $('.prize').show();
    $.ajax({
        url:'/index/lottery/add_integra',
        data:{type:3},
        dataType:'json',
        success:function (rs) {
            $.ajax({
                url:'/index/lottery/checkSign',
                type:'post',
                dataType:'json',
                success:function (rs) {
                    if(rs.qiandao == 0){
                        $('.prize .clickgo').hide()
                        $('.prize .todaygo').show()
                    }

                    $('.prize .gocj .blue').text(rs.integra)


                    var i = rs.day * 3

                    $('.prize .pro div:lt('+i+')').addClass('gou')
                    $('.prize .pro div:lt('+i+')').children('.num').hide()
                }
            })
        }
    })

})
//签到
$('.clickgo').click(function () {
    $.ajax({
        url:'/index/lottery/sign',
        type:'post',
        dataType:'json',
        success:function (rs) {

            $('.prize .gocj .blue').text(rs.integra)
            $('.prize .clickgo').hide()
            $('.prize .todaygo').show()

            var i = rs.day * 3

            $('.prize .pro div:lt('+i+')').addClass('gou')
            $('.prize .pro div:lt('+i+')').children('.num').hide()

            if(rs.type == 1){
                $('.alertjf').show().css('z-index',120)
                $('.alertjf .core').text(rs.jf)
                $('.alertjf .yellow').text(rs.txt)
            }else {
                $('.alertvip').show().css('z-index',120)
                $('.alertjf .blue').text(rs.txt)
            }

            $('.prize').css('z-index',100)

            var t = 5;
            var timer = setInterval(function(){
                if(t == 0){
                    clearInterval(timer);
                    $('.alertcredit').hide()
                    $('.prize').css('z-index',101)
                }else{
                    --t;
                }
            },1000);
        }
    })
})
//抽奖记录
$('.gotocj').click(function () {
    $("#mengcheng").show()
    $.ajax({
        url:'/index/lottery/record',
        type:'post',
        data:{type:2},
        dataType:'json',
        success:function (rs) {
            $('.records').show()
            $('.records .jl .sign').removeClass('r-chose')
            $('.records .jl .gotocj').addClass('r-chose')

            var str = "";
            if(rs.code == 1){
                $.each(rs.data,function (i,v) {
                    str += "<div class='transverse'><span class='tran-left'>"+v.addtime+"</span>"
                    str += "<span class='tran-right'>"+v.record+"</span></div>"
                })
            }else {
                str += "<div><img src='"+rs.src+"' style='margin:75px;'></div>"
            }
            $('.records .recordtime').html(str)

        }
    })
})
//签到记录
$('.sign').click(function () {
    $("#mengcheng").show()
    $.ajax({
        url:'/index/lottery/record',
        type:'post',
        data:{type:1},
        dataType:'json',
        success:function (rs) {
            $('.records').show()
            $('.records .jl .gotocj').removeClass('r-chose')
            $('.records .jl .sign').addClass('r-chose')
            var str = "";
            if(rs.code == 1){
                $.each(rs.data,function (i,v) {
                    str += "<div class='transverse'><span class='tran-left'>"+v.addtime+"</span><span style='float:left;margin-left: 130px;'>"+v.txt+"</span>"
                    str += "<span class='tran-right'>"+v.record+"</span></div>"
                })
            }else {
                str += "<div><img src='"+rs.src+"' style='margin:75px;'></div>"
            }
            $('.records .recordtime').html(str)
        }
    })
})
$(document).on('click','.alertcredit .close',function(){
    $('.prize').css('z-index',101)
    $('#mengcheng').show()
})
$(document).on('click','.records .close',function(){
    var isIndex = $('#lottery').val()
    $('.prize').css('z-index',101)
    if(isIndex != 1){
        $('#mengcheng').show()
    }
})