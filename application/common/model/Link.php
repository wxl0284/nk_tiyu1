<?php
namespace app\common\model;

use think\Model;

class Link extends Model
{
    // 指定表名,不含前缀
    protected $name = 'link';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    protected $type = [
        'update_time'=>'timestamp:Y/m/d H:i:s',
        'create_time'  =>  'timestamp:Y/m/d H:i:s'
    ];
}
