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
        
        $r = Db::table('alldata')->where(['lesson' => $d['lesson'], 'stu_num' => $d['stu_num']])->order('id','desc')->field('advice')->find();

        if($r)
        {
            return json(['code' => 200, 'data' => $r]);
        }else{
            return json(['code' => 100, 'msg' => '未查到指导建议~']);
        }
    }//show_advice 结束
}