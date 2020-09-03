<?php
namespace app\index\controller;

use think\Db;
use app\common\controller\base; //手机端页面基类 用来进行登录判断

class Cartoon extends Base
{
	/*
	 index() 显示标准动画首页
	*/
	
	public function index ()
	{
		return view();
	}//index 结束
}