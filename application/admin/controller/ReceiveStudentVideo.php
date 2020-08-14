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
        //$file_info = $file->getInfo();//文件名：$file_info['name'] 临时文件名：$file_info['tmp_name']  文件大小：$file_info['size']

        $info = $file->move($student_video_dir, '');//移动到此目录，且使用原文件名

        if($info)
        {
            // 成功上传后 获取上传信息
            //echo $info->getExtension();// 输出 jpg            
            //echo $info->getSaveName();// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            //echo $info->getFilename();// 输出 42a79759f284b767dfcb2a0197904287.jpg
            echo 'ok';
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }

    }//receive_video 结束
}