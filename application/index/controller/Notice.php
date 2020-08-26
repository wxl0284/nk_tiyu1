<?php
namespace app\index\controller;

use think\Db;
use app\common\controller\base;

class Notice extends Base
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
	
	//显示公告添加页
    public function add_page()
    {
		return view('add');
    }
	
	//执行添加
    public function add()
    {
		$d = input();
		
		$msg = '';
		
		if ( !(isset($d['title']) && mb_strlen($d['title']) >= 5 && mb_strlen($d['title']) <= 20)  )
		{
			$msg .= '标题应为5~20字！';
		}
		
		if ( !(isset($d['content']) && mb_strlen($d['content']) >= 10 && mb_strlen($d['content']) <= 150)  )
		{
			$msg .= '内容应为5~20字！';
		}
		
		if ( $msg != '' )
		{
			return json(['code' =>100, 'msg' => $msg]);
		}
		
		$d['time'] = date('Y-m-d H:i:s', time());
		$d['adder'] = '管理员';//后期改为实际登录者 session
		
		$r = Db::table('tp_notice')->insert($d);
		
		if ( $r )
		{
			return json(['code' =>200, 'msg' => '发布成功']);
		}else{
			return json(['code' =>100, 'msg' => '发布失败']);
		}
    }
}