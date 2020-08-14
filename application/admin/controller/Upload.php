<?php
/**
 * 测试1.0 [a web admin based ThinkPHP5]
 *
 *
 *
 *
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace app\admin\controller;

use app\admin\Controller;
use think\Db;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 * 文件上传
 */
class Upload extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 文件上传
     */
    public function upload_file()
    {
        $file = $this->request->file('file');


        $path = ROOT_PATH . 'public/tmp/uploads/';
        $info = $file->move($path);
        if (!is_object($info) || $info === false) {

            return ajax_return_error($file->getError());
        }
        $data = '/tmp/uploads/' . $info->getSaveName();
        $insert = [
            'cate'     => 3,
            'name'     => $data,
            'original' => $info->getInfo('name'),
            'domain'   => '',
            'type'     => $info->getInfo('type'),
            'size'     => $info->getInfo('size'),
            'mtime'    => time(),
        ];
        Db::name('File')->insert($insert);

        return ajax_return(['name' => $data]);
    }


    public function upload()
    {

        if(request()->isPost()){
            return $this->upload_file();

            $file = $this->request->file('file');

            // 要上传图片的本地路径
            $filePath = $file->getRealPath();
            $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀

            // 上传到七牛后保存的文件名
            $key =substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
            //require_once \think\facade\Env::get('app_path') . '/../vendor/qiniu/php-sdk/autoload.php';
            // 需要填写你的 Access Key 和 Secret Key

            // 要上传的空间
            $bucket = config('qiniu.bucket');
            $auth = new Auth(config('qiniu.accessKey'),config('qiniu.secretKey'));
            //队列名称
            $upToken = $auth->uploadToken($bucket);
            $domain = config('qiniu.domain');
            $token = $auth->uploadToken($bucket);
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

            if ($err !== null) {
                return ["err"=>1,"msg"=>$err->error,"data"=>""];
            } else {
                //返回图片的完整URL
                $insert = [
                    'cate'     => 3,
                    'name'     => $domain . $ret['key'],
                    'original' => $file->getInfo('name'),
                    'domain'   => '',
                    'type'     => $file->getInfo('type'),
                    'size'     => $file->getInfo('size'),
                    'mtime'    => time(),
                ];
                Db::name('File')->insert($insert);


                //返回图片的完整URL
                return ajax_return(['name' => $domain . $ret['key']]);

            }
        }


    }

    /**
     * 远程图片抓取
     */
    public function remote()
    {
        $url = $this->request->post('url');
        // validate
        $name = ROOT_PATH . 'public/tmp/uploads/' . get_random();
        $name = \File::downloadImage($url, $name);

        $ret = '/pubic/tmp/uploads/' . basename($name);

        return ajax_return(['url' => $ret], '抓取成功');
    }

    /**
     * 图片列表
     */
    public function listImage()
    {
        $page = $this->request->param('p', 1);
        if ($this->request->param('count')) {
            $ret['count'] = Db::name('File')->where('cate=3')->count();
        }
        $ret['list'] = Db::name('File')->where('cate=3')->field('id,name,original')->page($page, 10)->select();

        return ajax_return($ret);
    }
}