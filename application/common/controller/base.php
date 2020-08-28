<?php
namespace app\common\controller;

use think\Controller;

/*
移动端页面控制器基类 
 */

class Base extends Controller
{
	protected $ajaxAuthErr = 0; //ajax请求时 （未登录时：not_login, 无权限时：not_auth)
	
	protected function initialize ()
	{
		//if ( 未登录 ) $ajaxAuthErr = 'not_login'
		//halt('ini'); $this->request->isMobile() 
	}
}
