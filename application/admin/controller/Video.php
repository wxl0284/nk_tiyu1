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
        if ( !isset($d['lession']) || $d['lession'] == '0' )
        {
            if ( isset($d['lession']) )
            {
                if ( $d['lession'] == '0' )
                {
                    $w = [];
                }else{
                    $w = [ 'video_lession' => $d['lession'] ];
                }                    

                cookie('lession', $d['lession']);
            }else{
                if ( cookie('lession') )
                {
                    $w = [ 'video_lession' => cookie('lession')];
                }else{
                    $w = [];
                }
                
                cookie('lession', '');
            }

        }else{
            $w = [ 'video_lession' => $d['lession'] ];
            cookie('lession', $d['lession']);            
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

    /*
    获取被编辑的视频数据
    */
    public function edit_data ()
    {
        $d = input();

        $r = Db::table('tp_video')->where('v_id', $d['v_id'])
            ->field('v_id,video_lession,video,video_pic,video_info,video_name')->find();

        if ($r)
        {
            return json(['code'=>200, 'data'=>$r]);
        }else{
            return json(['code'=>100, 'msg'=>'未查到此视频数据~']);
        }
    }

    /*
    保存编辑的视频数据
    */
    public function edit_video ()
    {
        $d = input();

        $d['video_uploader'] = session('real_name');
        $d['uploaded_time'] = date('Y-m-d H:i:s', time());

        $r = Db::table('tp_video')->where('v_id', $d['v_id'])->update($d);

        if ($r)
        {
            return json(['code'=>200, 'msg'=>'编辑成功~']);
        }else{
            return json(['code'=>100, 'msg'=>'编辑失败~']);
        }
    }

    /*
              删除视频数据
    */
    public function del_video ()
    {
        $d = input();
        
		$r = Db::table('tp_video')->where('v_id', $d['v_id'])->field('video_pic, video')->find();
		
		if ($r)
		{
			//删除视频的封面图片和视频内容
			unlink($r['video_pic']);
			unlink($r['video']);
		}
		
        $r1 = Db::table('tp_video')->where('v_id', $d['v_id'])->delete();

        if ($r1)
        {
            return json(['code'=>200, 'msg'=>'删除成功~']);
        }else{
            return json(['code'=>100, 'msg'=>'删除失败~']);
        }
    }
}