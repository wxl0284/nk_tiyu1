<?php
namespace app\index\controller;

use think\Db;
use app\common\controller\base; //手机端页面基类 用来进行登录判断

class Video extends Base
{
	//显示标准视频首页
		
	public function index ()
	{
		$r = Db::table('tp_video')->group('video_lession')->column('video_lession');//所有的课程

		if ($r)
		{
			//然后查video_lession字段==$r[0]的所有视频  例如网球
			$video = Db::table('tp_video')->where('video_lession', $r[0])->select();
			
			if ($video)
			{
				return view()->assign(['list' => $r, 'videos' => $video, 'lession' => 1]);
			}else
			{
				return view()->assign(['list' => $r, 'lession' => 1]);
			}
			
			
		}else{
			return view();
		}
	}//index 结束
	
	//显示导航栏某个课程的视频
		
	public function show_lession_video ()
	{
		$d = input();
		
		
		$r = Db::table('tp_video')->where('video_lession', $d['video_lession'])->select();

		if ($r)
		{
			return json(['code' => 200, 'data' => $r]);
		}else{
			return json(['code' => 100, 'msg' => '此课程暂无视频~']);
		}
	}//index 结束
}
