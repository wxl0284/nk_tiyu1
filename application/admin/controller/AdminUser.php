<?php
/**
 * tpAdmin [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://tpadmin.yuan1994.com/
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
// 用户控制器
//-------------------------

namespace app\admin\controller;



use app\admin\Controller;
use think\Exception;

/**
 * 用户控制器
 */
class AdminUser extends Controller
{
    use \app\admin\traits\controller\Controller;

//    protected static $blacklist = ['delete', 'clear', 'deleteforever', 'recyclebin', 'recycle'];

    protected function filter(&$map)
    {
        //不查询管理员
        $map['id'] = ['id',"gt", 1];

        if ($this->request->param('realname')) {
            $map['realname'] = ['realname',"like", "%" . $this->request->param('realname') . "%"];
        }
        if ($this->request->param('account')) {
            $map['account'] = ['account',"like", "%" . $this->request->param('account') . "%"];
        }
        if ($this->request->param('email')) {
            $map['email'] = ['email',"like", "%" . $this->request->param('email') . "%"];
        }
        if ($this->request->param('mobile')) {
            $map['mobile'] = ['mobile',"like", "%" . $this->request->param('mobile') . "%"];
        }
    }

    /**
     * 修改密码
     */
    public function password()
    {
        $id = $this->request->param('id/d');
        if ($this->request->isPost()) {
            //禁止修改管理员的密码，管理员id为1
            if ($id < 2) {
                return ajax_return_adv_error("缺少必要参数");
            }

            $password = $this->request->post('password');
            if (!$password) {
                return ajax_return_adv_error("密码不能为空");
            }
            if (false === \think\facade\App::model('AdminUser')->updatePassword($id, $password)) {
                return ajax_return_adv_error("密码修改失败");
            }
            return ajax_return_adv("密码已修改为{$password}", '');
        } else {
            // 禁止修改管理员的密码，管理员 id 为 1
            if ($id < 2) {
                throw new Exception("缺少必要参数");
            }

            return $this->view->fetch();
        }
    }

    /**
     * 禁用限制
     */
    protected function beforeForbid()
    {
        // 禁止禁用 Admin 模块,权限设置节点
        $this->filterId(1, '该用户不能被禁用', '=');
    }
}