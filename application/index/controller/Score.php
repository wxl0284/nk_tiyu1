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
	get_student_data() 根据select下拉框查询其option值,每个学生的同一个动作仅查一条
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
		
		if ($d['clas'] !== '0')
		{
			$where['class'] = $d['clas'];
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
		
		//对stu_num和id两个字段按降序排列
		$r = Db::table('alldata')->where($where)
		   ->order(['stu_num', 'id' =>'desc'])->limit($d['start'], 10)->field('id,stu_num,stu_name,grade,class,lesson,activity')->select();//每次上划屏幕加载10条
	
		if ($r)
		{
			//根据学号或登录号--课程--动作将同一学生某课程的同一动作多条记录只保留一条在页面显示
			$t = '';
			
			foreach($r as $k => $v)
			{
				$t1 = $v['stu_num'] . $v['lesson'] . $v['activity'];//学号课程动作
				
				if ($t1 === $t)
				{
					unset($r[$k]);
				}
				
				$t = $v['stu_num'] . $v['lesson'] . $v['activity'];//学号课程动作			
			}
			
			$r = array_column($r,null);//将$r的索引重新按0 1 2进行编排， 否则其索引为 0 2 5前端js无法循环输出
			
			return json(['code' => 200, 'data' => $r]);
		}else{
			return json(['code' => 100, 'msg' => '暂未查到数据，稍后再试~']);
		}
	}
	
	/*
	 get_10_item(): 获取页面中一个学生的同一个动作的最新10条记录
	*/
	
	public function get_10_item ()
	{
		$d = input('post.param'); //"889|01班|正手击球"
		
		$d = explode('|', $d);
		
		$d = ['stu_num' => $d[0], 'lesson' => $d[1], 'activity' => $d[2]];//组装查询的条件
		
		$r = Db::table('alldata')->where($d)->order('id', 'desc')->limit(10)->field('id,stu_name,grade,class,lesson,activity,time,score,video')->select();
		
		if ($r)
		{
			//处理时间 20200910121212, 处理为20-09-18 11:12	
			foreach($r as $k => $v)
			{
				$temp = $v['time'];
				$temp = substr($temp, 2, 10);//$temp:2009021112
				$temp = str_split($temp, 2);//把$temp按2个字符等分
				$temp = $temp[0] . '-' . $temp[1] . '-' . $temp[2] . ' ' . $temp[3] . ':' . $temp[4];
				
				$r[$k]['time'] = $temp;
			}
			
			return json(['code' => 200, 'data' => $r, 'student_video_ip' => config('student_video_ip')]);
		}else{
			return json(['code' => 100, 'msg' => '暂未查到数据~']);
		}
	}
}