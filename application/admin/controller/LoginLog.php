<?php
/**
 * 测试1.0 [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://测试1.0.yuan1994.com/
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

//------------------------
// 登录日志控制器
//-------------------------

namespace app\admin\controller;



use app\admin\Controller;
use app\common\model\LoginLog as ModelLoginLog;
use app\common\model\AdminUser as ModelAdminUser;
use think\Exception;

/**
 * 登录日志控制器
 */
class LoginLog extends Controller
{
    use \app\admin\traits\controller\Controller;

    protected static $isdelete = false; //禁用该字段

    protected static $blacklist = ['add', 'edit', 'delete', 'deleteforever', 'forbid', 'resume', 'recycle', 'recyclebin', 'clear'];

    public function index()
    {
        // 列表过滤器，生成查询Map对象
        $map = [];

        if ($this->request->param('login_location')) {
            $map['w.login_location'] = ["like", "%" . $this->request->param('login_location') . "%"];
        }

        // 关联筛选
        if ($this->request->param('account')) {
            $map['u.account'] = ["like", "%" . $this->request->param('account') . "%"];
        }
        if ($this->request->param('name')) {
            $map['u.realname'] = ["like", "%" . $this->request->param('name') . "%"];
        }


        if($map){
            foreach ($map as $k=>$v){
                $arr[] = $k;
                foreach ($v as $v2){
                    $arr[] = $v2;
                }
                $map[] = [$arr];
                unset($map[$k]);
            }
        }

        // 分页查询
        $model = new ModelLoginLog();
        $listRows = 20;
        $list = $model->alias('w')
            ->join([
                ['tp_admin_user u', 'u.id=w.uid', 'LEFT'],
            ])
            ->where($map)->order('w.id desc')->paginate($listRows, false, ['query' => $this->request->get()]);


        // 模板赋值显示
        $this->view->assign('list', $list);
        $this->view->assign("page", $list->render());
        $this->view->assign("count", $list->total());
        $this->view->assign('numPerPage', $listRows);

        return $this->view->fetch();
//        $map['_func'] = function (ModelLoginLog $model) use ($map) {
//            $model->alias($map['_table'])->join(
//                [ModelAdminUser::getTable() . ' user', 'login_log.uid = user.id']);
//        };
    }
}
