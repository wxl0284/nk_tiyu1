<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
//use think\Validate;

class Index extends Controller
{
	
	/*
	 如果是移动设备访问显示移动端页面,在此方法里执行统一身份认证，并且在前端显示的页面里用WebSocket发送数据给训练终端
	 否则就执行登录进后台
	*/
	
    //网站首页
    public function index()
    {
    	if ( $this->request->isMobile() )
    	{
    		$ip = input('address');//训练终端的ip
    		$stu_grade = '2019';//年级
    		$stu_class = '天文1班';//班级
    		$stu_num = '888';//学生号
    		$stu_name = 'li'; //学生姓名		
    		$type = 1; //1表示学生 2 表示教师
    		
    		return view()->assign(['ip' => $ip, 'stu_grade' => $stu_grade, 'stu_class' => $stu_class, 'stu_num' => $stu_num, 'stu_name' => $stu_name, 'type' => $type]);
    	}else{
    		$this->redirect('admin/pub/login');//登录进后台
    	}
        
    }
}
