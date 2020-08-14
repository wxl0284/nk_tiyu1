<?php
namespace app\common\Entity;

use \Firebase\JWT\JWT; //导入JWT
/**
 * User: sww
 * Class JwtEntity
 * @package app\common\Entity
 * jwt 静态类
 * Date: 2018/11/22
 * Time: 11:03
 */
class JwtEntity{
    const KEY = 'jwt_sparrww';
    const ISS = 'http://www.ww01.net';
    const AUD = 'http://www.ww01.net';

    /**
     * Notes:签发Token
     * @param array $param
     * @return string
     * User: sww
     * Date: 2018/11/22
     * Time: 11:20
     */
    public static function encode(array $param)
    {
        $key = self::KEY; //key
        $time = time(); //当前时间
        $token = [
            'iss' => self::ISS, //签发者 可选
            'aud' => self::AUD, //接收该JWT的一方，可选
            'iat' => $time, //签发时间
            'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' =>$param
//                [ //自定义信息，不要定义敏感信息
//                    'userid' => 1,
//                    'username' => 'test'
//                ]
        ];

        //Notes:如需注销，可自定义缓存或数据库机制

        return JWT::encode($token, $key); //输出Token
    }


    /**
     * Notes:解密
     * @param $token
     * @return array|string
     * User: sww
     * Date: 2018/11/22
     * Time: 11:26
     */
    public static function decode($token)
    {
        $key = self::KEY; //key要和签发的时候一样

        $jwt = $token; //签发的Token
        try {
            JWT::$leeway = 10;//当前时间减去10，把时间留点余地
            $decoded = JWT::decode($jwt, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
            $arr = (array)$decoded;
            return $arr;
        } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
            return $e->getMessage();
        }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
            return $e->getMessage();
        }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
            return $e->getMessage();
        }catch(\Exception $e) {  //其他错误
            return $e->getMessage();
        }
        //Firebase定义了多个 throw new，我们可以捕获多个catch来定义问题，catch加入自己的业务，比如token过期可以用当前Token刷新一个新Token
    }
}