<?php
namespace app\index\controller;

use think\Db;
use think\Controller;

class Notice extends Controller
{	
    //通知公告首页
    public function index()
    {
    	if ( $this->request->isMobile() )
    	{
    		
    		return view();
    		//return view()->assign(['ip' => $ip, 'stu_num' => $stu_num, 'stu_name' => $stu_name, 'type' => $type]);
    	}        
    }
}