<?php
use \think\facade\Request;

$basename = Request::instance()->root();
if (pathinfo($basename, PATHINFO_EXTENSION) == 'php') {
    $basename = dirname($basename);
}
return [
    // traits 目录
    'traits_path'      => env('root_path') . 'admin' . DIRECTORY_SEPARATOR . 'traits' . DIRECTORY_SEPARATOR,

    // 异常处理 handle 类 留空使用 \think\exception\Handle
    'exception_handle' => '\\TpException',

];