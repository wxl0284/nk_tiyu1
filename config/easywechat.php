<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23 0023
 * Time: 15:49
 */

return [
    'test'=>[

        'debug'  => false,

        'app_id'=>'wx85e448a026c6756d',
        'secret'=>'cabcf1d6ca8f99d9c8f63accec139d81',

        'token'   => 'ulsL6sTHlLp47jBPL7PzzLLh3ZPSTb2l',          // Token
        'aes_key' => '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'oauth'=>[
            'scopes'   => ['snsapi_userinfo'],
            'callback' => '/index/login/getUserInfo',
        ],

        'log' => [
            'level'      => 'debug',
            'permission' => 0777,
            'file'       => Env::get('root_path').'runtime/log/easywechat.log',
        ],
//        'payment' => [
//            'merchant_id'        => '1263438301',
//            'key'                => '698d51a19d8a121ce581499d7b701668',
//            'cert_path'          => $_SERVER["DOCUMENT_ROOT"]."/ssl/apiclient_cert.pem",
//            'key_path'           => $_SERVER["DOCUMENT_ROOT"]."/ssl/apiclient_key.pem",
//
//        ],
    ]
];
