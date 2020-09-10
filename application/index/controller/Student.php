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
        halt($d);
    }//get_lesson结束
}