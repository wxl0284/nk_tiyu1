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
    		//$ip = input('address');//扫码获得的 训练终端的ip
    		$ip = '192.168.8.2';//扫码获得的 训练终端的ip
    		$stu_grade = '2019';//年级
    		$stu_class = '天文1班';//班级
    		$stu_num = '888';//学生号
    		$stu_name = 'li'; //学生姓名		
    		$type = 1; //1表示学生 2 表示教师
			
			//查询最新一条通知信息
			$notice = Db::table('tp_notice')->order('id', 'desc')->field('content')->find();
			$content = '';//通知内容

			if ($notice)
			{
				$content = $notice['content'];
			}

			if ($type == 1)
			{//学生登录
				//查询tp_video表里的每门课程包括的所有动作
				$r = Db::table('tp_video')->group('video_lession')->column('video_lession');//所有的课程

				if ($r)
				{//有课程信息
					//然后查video_lession字段==$r[0]的所有视频  例如网球
					$video = Db::table('tp_video')->where('video_lession', $r[0])->field('video_name,video_pic,video')->order('v_id', 'asc')->select();
					
					if ($video)
					{//有视频信息
						//return view()->assign(['list' => $r, 'videos' => $video, 'lession' => 1]);
						return view('stu_page')->assign([
							'ip' => $ip,
							'stu_grade' => $stu_grade,
							'stu_class' => $stu_class,
							'stu_num' => $stu_num,
							'stu_name' => $stu_name,
							'type' => $type,
							'list' => $r,
							'videos' => $video,
							'lession' => 1,
							'notice' => $content
							]);
					}else
					{//无视频信息
						//return view()->assign(['list' => $r, 'lession' => 1]);
						return view('stu_page')->assign([
							'ip' => $ip,
							'stu_grade' => $stu_grade,
							'stu_class' => $stu_class,
							'stu_num' => $stu_num,
							'stu_name' => $stu_name,
							'type' => $type,
							'list' => $r,
							'lession' => 1,
							'notice' => $content
							]);
					}					
					
				}else{//无课程信息
					
					return view('stu_page')->assign([
						'ip' => $ip,
						'stu_grade' => $stu_grade,
						'stu_class' => $stu_class,
						'stu_num' => $stu_num,
						'stu_name' => $stu_name,
						'type' => $type,
						'notice' => $content
						]);
				}

			}else if($type == 2)
			{//是老师登录
				return view()->assign([
					'ip' => $ip,
					'stu_grade' => $stu_grade,
					'stu_class' => $stu_class,
					'stu_num' => $stu_num,
					'stu_name' => $stu_name,
					'type' => $type,
					'notice' => $content
					]);
			}
    		
    	}else{
    		$this->redirect('admin/pub/login');//登录进后台
    	}
        
    }
}
