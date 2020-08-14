<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 模拟tab产生空格
 * @param int $step
 * @param string $string
 * @param int $size
 * @return string
 */
function tab($step = 1, $string = ' ', $size = 4)
{
    return str_repeat($string, $size * $step);
}

// 应用公共文件
function curl_request($url,$body=[],$header=[],$type="GET"){
    //1.创建一个curl资源
    $ch = curl_init();
    //2.设置URL和相应的选项
    curl_setopt($ch,CURLOPT_URL,$url);//设置url
    //1)设置请求头
    //array_push($header,'Content-Type: application/json;charset=UTF-8');
    array_push($header,'Content-Type: application/x-www-form-urlencoded');
    //array_push($header, 'Accept:application/json');
    //array_push($header,'Content-Type:application/json');
    //array_push($header, 'http:multipart/form-data');
    //设置为false,只会获得响应的正文(true的话会连响应头一并获取到)
    curl_setopt($ch,CURLOPT_HEADER,0);
//		curl_setopt ( $ch, CURLOPT_TIMEOUT,5); // 设置超时限制防止死循环
    //设置发起连接前的等待时间，如果设置为0，则无限等待。
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
    //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //2)设备请求体
    if (count($body)>0) {
        $type = 'POST';
        //$body=http_build_query($body);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);//全部数据使用HTTP协议中的"POST"操作来发送。
    }
    //设置请求头
    if(count($header)>0){
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
    }
    //上传文件相关设置
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算

    //3)设置提交方式
    switch($type){
        case "GET":
            curl_setopt($ch,CURLOPT_HTTPGET,true);
            break;
        case "POST":
            curl_setopt($ch,CURLOPT_POST,true);
            break;
        case "PUT"://使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请求。这对于执行"DELETE" 或者其他更隐蔽的HTT
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
            break;
        case "DELETE":
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
            break;
    }


    //4)在HTTP请求中包含一个"User-Agent: "头的字符串。-----必设

//		curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
//		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
    //5)


    //3.抓取URL并把它传递给浏览器
    $res=curl_exec($ch);

    //4.关闭curl资源，并且释放系统资源
    curl_close($ch);
    return $res;

}

function set_log($msg){
    \think\Db::table('my_log')->insert([
        'msg'=> $msg,
    ]);
}

function json_api($code=0,$data = [],$extra = [])
{
    if(is_array($code) && !is_int($code)){
        $result = [
            'code'=>0,
            'msg'=>config("code.0"),
            'data'=>$code
        ];
    }else{
        $result = [
            'code'=>$code,
            'msg'=>config("code.".$code),
            'data'=>$data
        ];

        $result = array_merge($result,$extra);
    }
    die(json_encode($result,JSON_UNESCAPED_UNICODE));

}

/**
 * 将数组转换为xml
 * @param array $arr:数组
 * @param object $dom:Document对象，默认null即可
 * @param object $node:节点对象，默认null即可
 * @param string $root:根节点名称
 * @param string $cdata:是否加入CDATA标签，默认为false
 * @return string
 */
function arrayToXml($arr,$dom=null,$node=null,$root='xml',$cdata=false){
    if (!$dom){
        $dom = new DOMDocument('1.0','utf-8');
    }
    if(!$node){
        $node = $dom->createElement($root);
        $dom->appendChild($node);
    }
    foreach ($arr as $key=>$value){
        $child_node = $dom->createElement(is_string($key) ? $key : 'node');
        $node->appendChild($child_node);
        if (!is_array($value)){
            if (!$cdata) {
                $data = $dom->createTextNode($value);
            }else{
                $data = $dom->createCDATASection($value);
            }
            $child_node->appendChild($data);
        }else {
            arrayToXml($value,$dom,$child_node,$root,$cdata);
        }
    }
    return $dom->saveXML();
}


/**
 * 将xml转换为数组
 * @param string $xml:xml文件或字符串
 * @return array
 */
function xmlToArray($xml){
    //考虑到xml文档中可能会包含<![CDATA[]]>标签，第三个参数设置为LIBXML_NOCDATA
    if (file_exists($xml)) {
        libxml_disable_entity_loader(false);
        $xml_string = simplexml_load_file($xml,'SimpleXMLElement', LIBXML_NOCDATA);
    }else{
        libxml_disable_entity_loader(true);
        $xml_string = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
    }
    $result = json_decode(json_encode($xml_string),true);
    return $result;
}

/**
 * 对ID加密
 * @param null|int $length
 * @param null|string $salt
 * @param null|string $alphabet
 * @return Hashids\Hashids static
 */
function hashids($length = null, $salt = null, $alphabet = null)
{
    return \Hashids\Hashids::instance($length, $salt, $alphabet);
}

/**
 * 一键导出Excel 2007格式
 * @param array $header     Excel头部 ["COL1","COL2","COL3",...]
 * @param array $body       和头部长度相等字段查询出的数据就可以直接导出
 * @param null|string $name 文件名，不包含扩展名，为空默认为当前时间
 * @param string|int $version 版本 2007|2003|ods|pdf
 * @return string
 */
function export_excel($header, $body, $name = null, $version = '2007')
{
    return \Excel::export($header, $body, $name, $version);
}

/**
 * 获取七牛上传token
 * @return mixed
 */
function qiniu_token()
{
    return \Qiniu::token();
}

/**
 * 检查指定节点是否有权限
 * @param null $action
 * @param null $controller
 * @param null $module
 * @return bool
 */
function check_access($action = null, $controller = null, $module = null)
{
    return \Rbac::AccessCheck($action, $controller, $module);
}

/**
 * 文件下载
 * @param $file_path
 * @param string $file_name
 * @param string $file_size
 * @param string $ext
 * @return string
 */
function download($file_path, $file_name = '', $file_size = '', $ext = '')
{
    return \File::download($file_path, $file_name, $file_size, $ext);
}


function jsformat($str)
{
    $str = trim($str);
    $str = str_replace('\\s\\s', '\\s', $str);
    $str = str_replace(chr(10), '', $str);
    $str = str_replace(chr(13), '', $str);
    $str = str_replace('  ', '', $str);
    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace('"', '\\"', $str);
    $str = str_replace('\\\'', '\\\\\'', $str);
    $str = str_replace("'", "\'", $str);
    return $str;
}

/**
 * Notes:jwt解密
 * @param $token
 * @return bool
 * User: sww
 * Date: 2018/11/12
 * Time: 15:23
 */
function jwt_decode($token){
    $key = config('app.jwt_key');
    try {
        return (array)Firebase\JWT\JWT::decode($token, $key, array('HS256'));
    }
    catch (Exception $e){
        return false;
    }
}

/**
 * Notes:jwt解密
 * @param $token
 * @return bool
 * User: sww
 * Date: 2018/11/12
 * Time: 15:23
 */
function jwt_encode($arr){

    $arr = array_merge([
        "iat"   => $_SERVER['REQUEST_TIME'],
        "exp"   => $_SERVER['REQUEST_TIME'] + 3600*24*14,
        "jti"   => config('app.app_name'),
    ],$arr);

    return Firebase\JWT\JWT::encode($arr,config('app.jwt_key'));
}

/**
 * Notes:数组转数组串
 * @param $arr
 * @return string
 * User: sww
 * Date: 2019/3/1
 * Time: 16:55
 */
function arrayToStr($arr){
    $str = '['.PHP_EOL;
    foreach ($arr as $k=>$v){
        if(is_string($v)){
            $str .= '\''.$k.'\'=>\''.$v.'\','.PHP_EOL;
        }

        if(is_array($v)){
            $str .= '\''.$k.'\'=>'.PHP_EOL;
            $str .= arrayToStr($v).','.PHP_EOL;
        }

    }
    $str .= ']';
    return $str;

}