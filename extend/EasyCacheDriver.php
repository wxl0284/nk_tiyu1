<?php
use EasyWeChat\Factory;
use app\common\Entity\RedisEntity;
class EasyCacheDriver {
    public static function getOption($type='Account',$which = 'test'){

        $option = \think\Config::get("easywechat");

        switch ($type){
            case 'Account':
            default:
                $app = Factory::officialAccount($option[$which]);
                break;
            case 'miniProgram':
                $app = Factory::miniProgram($option[$which]);
                break;

        }

//        if(RedisEntity::$redis){
//            // 替换应用中的缓存
//            $app['cache'] = RedisEntity::$redis;
//        }

        return  $app;

    }
}