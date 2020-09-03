<?php
namespace app\index\controller;

use think\Db;
use app\common\controller\base; //手机端页面基类 用来进行登录判断

class Score extends Base
{
	/*
	 index() 显示学生成绩首页
	*/
	
	public function index ()
	{
		//看是否有成绩数据
		$r = Db::table('alldata')->order('id', 'asc')->find();
		
		if ($r)
		{
			return view()->assign(['have_score' => 1]);
		}else{
			return view();
		}
		
	}//index 结束
	
	/*
	get_select_data() 每次点击页面的年级、班级、课程、动作4个下拉框时就请求此方法获取被点击的下拉框的option值
	*/
	
	public function get_select_data ()
	{
		$field = input();
		//halt($field);
		switch ($field['param'])
		{
		    case 'grade'://查寻共有几个年级		    		     
		        return $this->get_data($field['param'], '未查到年级数据~', $field['other']);
		        break;
		    case 'class':
		        return $this->get_data($field['param'], '未查到班级数据~', $field['other']);   
		        break;
		    case 'lesson':
		        return $this->get_data($field['param'], '未查到课程数据~', $field['other']);
		        break;
		    case 'activity':
		        return $this->get_data($field['param'], '未查到动作数据~', $field['other']);
		        break;
		    default:
		    	return;
		}
	}//get_select_data 结束
	
	/*
	get_data() 根据select下拉框查询其option值
	*/
	
	protected function get_data ($field, $msg, $other)
	{      	
    	//如果已年级或课程 即$other有值
    	if ( $other !== '0' )
    	{
    		$where = [];
    		
    		if ($field == 'class')
    		{
    			$where = ['grade' => $other];
    		}else if($field == 'activity')
    		{
    			$where = ['lesson' => $other];
    		}
    		
    		$r = Db::table('alldata')->where($where)->group($field)->column($field);
    	}else{
    		$r = Db::table('alldata')->group($field)->column($field);
    	}
    	
    
    	if ($r)
    	{
    		return json(['code' => 200, 'data' => $r]);
    	}else{
    		return json(['code' => 100, 'msg' => $msg]);
    	}
        	
	}//get_data 结束
	
	/*
	get_student_data() 根据select下拉框查询其option值
	*/
	
	public function get_student_data ()
	{
		$d = input();

		$where = [];
		//根据提交的查询选项 组装查询条件
		if ($d['grade'] !== '0')
		{
			$where['grade'] = $d['grade'];
		}
		
		if ($d['class'] !== '0')
		{
			$where['class'] = $d['class'];
		}
		
		if ($d['lesson'] !== '0')
		{
			$where['lesson'] = $d['lesson'];
		}
		
		if ($d['activity'] !== '0')
		{
			$where['activity'] = $d['activity'];
		}
		//根据提交的查询选项 组装查询条件 结束
		
		if ( !isset($d['start']) )
    	{
    		$d['start'] = 0; //点击查询按钮 ，还没有上划屏幕时，从第一条开始查
    	}
		
		$r = Db::table('alldata')->where($where)
		   ->order('id', 'desc')->limit($d['start'], 10)->field('id,stu_name,grade,class,lesson,activity')->select();//每次上划屏幕加载10条
		   
		if ($r)
		{
			return json(['code' => 200, 'data' => $r]);
		}else{
			return json(['code' => 100, 'msg' => '暂未查到数据，稍后再试~']);
		}
	}
}