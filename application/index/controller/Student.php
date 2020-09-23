<?php
namespace app\index\controller;

use think\Db;
use app\common\controller\base; //手机端页面基类 用来进行登录判断

class Student extends Base
{
    /*
    get_lesson() 获取学生首页课程导航的课程中动作的信息
    */

    public function get_lesson ()
    {
        $d = input();
        
        $r = Db::table('tp_video')->where('video_lession', $d['lesson'])->order('v_id', 'asc')->field('video_name, video, video_pic')->select();//此课程的所有动作

        if ($r)
        {
            return json(['code' => 200, 'data' => $r]);
        }else{
            return json(['code' => 100, 'msg' => '暂未查到动作数据~']);
        }
    }//get_lesson结束

    /*
    show_advice() 显示学生某动作的教师指导意见
    */

    public function show_advice()
    {
        $d = input();
        
        $r = Db::table('alldata')->where(['lesson' => $d['lesson'], 'stu_num' => $d['stu_num'], 'activity' => $d['activity']])
        ->order('id','desc')->field('advice')->find();

        if($r)
        {
            return json(['code' => 200, 'data' => $r]);
        }else{
            return json(['code' => 100, 'msg' => '未查到指导建议~']);
        }
    }//show_advice 结束

    /*
    exercise_page() 显示学生自己练习视频及图片的页面
    */

    public function exercise_page ()
    {
        $d = input();
        $d['stu_num'] = 889;//假数据
        $student_video_ip = config('student_video_ip');//学生练习视频及图片的服务器ip

        $r = Db::table('alldata')->where(['stu_num' => $d['stu_num'], 'lesson' => $d['lesson'], 'activity' => $d['activity']])
            ->order('id', 'desc')->find();
 
        if ($r)
        {
            //处理png_detail字段
            if ($r['png_detail'])
            {
                $t = explode('&', $r['png_detail']);//$t为多个图片的地址及指导意见：/GymData/001pan20200824175637action1.png|图片1各关节角度及误差

                $arr = [];

                foreach($t as $k => $v)
                {
                    $t1 = explode('|', $v);
                    $arr[$k]['pic']    = $t1[0];
                    $arr[$k]['advice'] = $t1[1];
                }

                $r['png_detail'] = $arr;
            }

            return view('index/exercise')->assign(['data' => $r, 'video_ip' => $student_video_ip]);
        }else{
            return view('index/exercise')->assign(['no_data' => 1]);
        }
        
    }//exercise_page 结束
}