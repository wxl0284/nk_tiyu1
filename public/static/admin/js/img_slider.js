//1个是父级名。调用方式如下
$(function(){
	imgscrool('#ban2');
})

//这是函数
function imgscrool(obj){
	
	var moving = false;		
	var width = $(obj+" .banner .img img").width();
	var i=0;
	var clone=$(obj+" .banner .img li").first().clone();
	$(obj+" .banner .img").append(clone);	
	var size=$(obj+" .banner .img li").size();	
	for(var j=0;j<size-1;j++){
		$(obj+" .banner .num").append("<li></li>");
	}
	$(obj+" .banner .num li").first().addClass("on");
	
	/*鼠标划入圆点*/
	if ($(obj+" .banner .num li")) {

	$(obj+" .banner .num li").hover(function(){
		var index=$(this).index();
		i=index;
		$(obj+" .banner .img").stop().animate({left:-index*width + 'vw'},800)	
		$(this).addClass("on").siblings().removeClass("on");
	})
	};
	//处理轮播初始宽度问题	
	var a2= $(obj+" .banner img").width();
	var a3= $(obj+" .img li").length
	$(obj+" .banner .img").width(a2*a3+'vw');	

	if ($(obj+" .banner .btn_l")) {

	/*向左的按钮*/
	$(obj+" .banner .btn_l").stop(true).click(function(){
	if(moving){
	return;
	};
	moving=true;
		i--
		move();	
	})
	
	/*向右的按钮*/
	$(obj+" .banner .btn_r").stop(true).click(function(){
	if(moving){
	return;
	}
	moving=true;
		i++
		move()				
	})

	};
	
	function move(){
		
		if(i==size){
			$(obj+" .banner  .img").css({left:0})			
			i=1;
		}
		
		// 修改轮播每屏宽度
		if(i==-1){
			$(obj+" .banner .img").css({left:-(size-1)*width + 'vw'})
			i=size-2;
		}	

		$(obj+" .banner .img").stop(true).animate({left:-i*width + 'vw'},800,function(){
			moving = false;
		})
		
		if(i==size-1){
			$(obj+" .banner .num li").eq(0).addClass("on").siblings().removeClass("on")
		}else{		
			$(obj+" .banner .num li").eq(i).addClass("on").siblings().removeClass("on")
		}
	}//move() 结束
}//imgscrool() 结束