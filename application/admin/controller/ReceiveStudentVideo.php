<?php
/**
 *此控制器接收训练终端在学生练习后终端自动上传的练习视频，保存在本地服务器 
*/
namespace app\admin\controller;

use app\admin\Controller;
use think\facade\App;
use think\facade\Session;
use think\Db;

class ReceiveStudentVideo extends Controller
{
    /*
    接收c#post过来的video信息，并保存至本地服务器
    */
    public function receive_video ()
    {//c#是模拟表单提交
        //$d = file_get_contents("php://input");//不是页面的表单提交，而是c#通过http协议post过来的数据

        //创建目录
        $student_video_dir = config('student_video_dir');

        if ( !(file_exists($student_video_dir) && is_dir($student_video_dir)) )
        {
            if ( !mkdir($student_video_dir) )//创建目录
            {
                echo 'mkdir error'; return;
            }
        }

        $file = request()->file('file');
        $file_info = $file->getInfo();//原文件名：$file_info['name'] 临时文件名：$file_info['tmp_name']  文件大小：$file_info['size']
		$file_name = $file_info['name']; //原文件名
		$file_name = $explode('_', $file_name); //解析出：学生号_姓名_课程名_动作名_成绩_时间
		
		$d = [];
		
		$d['stu_num'] = $file_name[0]; //学号
		$d['stu_name'] = $file_name[1]; //学生姓名
		$d['lession'] = $file_name[2]; //课程
		$d['activity'] = $file_name[3]; //动作
		$d['grade'] = $file_name[4]; //成绩
		$d['time'] = $file_name[5]; //时间
		$d['video'] = $file_info['name']; //视频名称
		
        $info = $file->move($student_video_dir, '');//移动到此目录，且使用原文件名

        if($info)
        {
            // 成功上传后 获取上传信息
            //echo $info->getExtension();// 输出 jpg            
            //echo $info->getSaveName();// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getFilename();// 输出 42a79759f284b767dfcb2a0197904287.jpg
			//往云端数据库插入数据
			
			Db::connect('mysql://root:1234@47.111.14.134:3306/nk_tiyu#utf8')//nk_tiyu是数据库名
			->table('tp_stu_video_grade')
			->insert($d);
			
            echo 'ok';
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }

    }//receive_video 结束
}