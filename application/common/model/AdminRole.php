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
// 角色模型
//-------------------------

namespace app\common\model;

use think\Model;

class AdminRole extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
}