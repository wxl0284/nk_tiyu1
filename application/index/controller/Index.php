<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
//use think\Validate;

class Index extends Controller
{

    //网站首页
    public function index()
    {
        $this->redirect('admin/pub/login');//
    }
}
