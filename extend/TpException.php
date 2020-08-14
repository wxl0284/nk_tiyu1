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
// 自定义异常处理
//-------------------------

use think\exception\Handle;
use think\exception\HttpException;
use think\facade\Request;
use think\facade\Config;

class TpException extends Handle
{
    public function render(\Exception $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            http_response_code($statusCode);
        }
        //可以在此交由系统处理
        if (Request::instance()->isAjax()) {
            $error_code = $this->getCode($e) ?: 1;
            return ajax_return_adv_error($this->getMessage($e), $error_code);
        }
        return parent::render($e);
    }

}
