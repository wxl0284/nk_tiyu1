<?php
/*
标准动作视频 控制器
*/

namespace app\admin\controller;

use app\admin\Controller;
use think\facade\App;
use think\facade\Session;
use think\Db;
//use think\Controller as think_Controller;

class Video extends Controller
{
    /*
    显示标准视频列表
    */
    public function index ()
    {
        $d = input();
        //halt($d);
        /*if ( !isset($d['lession']) || $d['lession'] == '0' )
        {
            $w = [];
        }else{
            $w = [ 'video_lession' => $d['lession'] ];
        }*/

        if ( !isset($d['lession']) || $d['lession'] == '0' )
        {
            $w = [];
            cookie('lession', '');
        }else{
            $w = [ 'video_lession' => $d['lession'] ];
            cookie('lession', $d['lession']);
        }

        if ( cookie('lession') == '' )
        {
            $w = [];
        }else{
            $w = [ 'video_lession' => cookie('lession') ];
        }

        $r = Db::table('tp_video')->where($w)->order('v_id', 'desc')->paginate(5);

        $num = count($r);

        if ( $num > 0 )
        {
            return view()->assign(['list' => $r]);
        }else{
            return view()->assign(['list' => 'no video']);
        }
        
    }

    /*
    添加标准视频
    */
    public function add_video ()
    {
        $d = input();

        $d['video_uploader'] = session('real_name');
        $d['uploaded_time'] = date('Y-m-d H:i:s', time());

        $r = Db::table('tp_video')->insert($d);

        if ($r)
        {
            return json(['code'=>200, 'msg'=>'添加成功~']);
        }else{
            return json(['code'=>100, 'msg'=>'添加失败~']);
        }
    }
}