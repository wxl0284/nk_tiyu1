<?php
namespace app\index\controller;

use think\Db;
use app\common\controller\base; //手机端页面基类 用来进行登录判断

class Notice extends Base
{	
    //通知公告首页
    public function index()
    {   
    	$d = input();
    	$isAjax = $this->request->isAjax();
    	
    	if ( !isset($d['start']) )
    	{
    		$d['start'] = 0; //从第一条开始查
    	}
    	
    	if ( isset($d['id']) )
    	{//ajax 通知详情的请求
    		$r = Db::table('tp_notice')->where('id', $d['id'])->find();
			if ($r)
			{
				return json(['code' => 200, 'data' => $r]);
			}else{
				return json(['code' => 200, 'msg' => '读取通知失败~']);
			}
    	}
    
    	$r = Db::table('tp_notice')->order('id', 'desc')->limit($d['start'], 2)->select();
    	
    	if ($r)
    	{
    		foreach ($r as $k => $v)
    		{
    			$r[$k]['time'] = substr($v['time'], 0, -3);//把时间字段的秒去掉 
    		}
    		
    		if ($isAjax)
    		{
    			return json(['code' => 200, 'data' => $r]);
    		}else{
    			return view()->assign(['list' => $r, 'notice' => 1]);
    		}
    		
    	}else{
    		
    		if ($isAjax)
    		{
    			return json(['code' => 100, 'msg' => '未查到通知数据']);
    		}else{
    			return view()->assign(['notice' => 0]);
    		}
    	}
		
		//return view()->assign(['ip' => $ip, 'stu_num' => $stu_num, 'stu_name' => $stu_name, 'type' => $type]);
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
    
    //删除通知公告
    public function delete ()
    {
    	$d = input();
    	
    	$r = Db::table('tp_notice')->where('id', $d['id'])->delete();
    	
    	if ($r)
    	{
    		return json(['code' => 200, 'msg' => '删除OK~']);
    	}else{
    		return json(['code' => 100, 'msg' => '删除失败, 请重新操作~']);
    	}
    }
    
    //获取编辑的通知数据
    public function get_edit ()
    {
    	$d = input();
    	
    	$r = Db::table('tp_notice')->where('id', $d['id'])->field('title, content')->find();
    	
    	if ($r)
    	{
    		return view('edit')->assign(['title' => $r['title'], 'content' => $r['content'], 'id' => $d['id']]);
    	}else{
    		return view('edit')->assign(['msg' => '读取数据失败~', 'id' => $d['id']]);
    	}
    	
    }
    
    //编辑通知数据
    public function edit ()
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
    	
    	$r = Db::table('tp_notice')->where('id', $d['id'])->update($d);
    	
    	if ($r)
    	{
    		return json(['code' => 200, 'msg' => '编辑成功~']);
    	}else{
    		return json(['code' => 100, 'msg' => '编辑失败~']);;
    	}
    	
    }
}