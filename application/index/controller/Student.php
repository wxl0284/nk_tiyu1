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
}