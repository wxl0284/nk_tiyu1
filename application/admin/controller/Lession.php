<?php
/*
管理课程的控制器
*/

namespace app\admin\controller;

use app\admin\Controller;
use think\facade\App;
use think\facade\Session;
use think\Db;
//use think\Controller as think_Controller;

class Lession extends Controller
{
    public function index ()
    {
        return view();
    }
}