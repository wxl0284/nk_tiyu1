<?php
/**
 * 测试1.0 [a web admin based ThinkPHP5]
 *
 * @author yuan1994 <tianpian0805@gmail.com>
 * @link http://测试1.0.yuan1994.com/
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace app\common\model;

use think\Model;

class WebLog extends Model
{
    protected $name = 'web_log_all';

    public function user()
    {
        return $this->hasOne('AdminUser', "id", "uid")->setAlias(["id" => "uuid"]);
    }

    public function map()
    {
        return $this->hasOne('NodeMap', "map", "map")->setAlias(["id" => "map_id"]);
    }
}