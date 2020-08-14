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
// 节点快速导入控制器
//-------------------------

namespace app\admin\controller;



use app\admin\Controller;

/**
 * 节点快速导入控制器
 */
class AdminNodeLoad extends Controller
{
    use \app\admin\traits\controller\Controller;

    protected static $isdelete = false;

    protected static $blacklist = ['delete', 'recycle'];

    protected function filter(&$map)
    {
        if ($this->request->param('title')) {
            $map['title'] = ["like", "%" . $this->request->param('title') . "%"];
        }
        if ($this->request->param('name')) {
            $map['name'] = ["like", "%" . $this->request->param('name') . "%"];
        }
    }
}
