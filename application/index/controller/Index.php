<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
//use think\Validate;

class Index extends Controller
{
	
	/*
	 如果是移动设备访问显示移动端页面，否则就执行登录进后台
	*/
	
    //网站首页
    public function index()
    {
    	if ( $this->request->isMobile() )
    	{
    		return view();
    	}else{
    		$this->redirect('admin/pub/login');//登录进后台
    	}
        
    }
}
