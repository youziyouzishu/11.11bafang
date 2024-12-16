<?php

namespace app\common\http\esign;



class Config
{
    public static $config = [
        #'eSignAppId' => '7439018062', //app id

        'eSignAppId' => '5111951915', //app id 线上
        #'eSignAppSecret' => '7e76b50ec56e3704fc87e226e9e04a2e', // app key
        'eSignAppSecret' => '311a8621709a2971e96a9f01feadc395', // app key 线上

        #'eSignHost' => 'https://smlopenapi.esign.cn', //模拟环境
        'eSignHost' => 'https://openapi.esign.cn', //模拟环境
        //     'url'=>'https://openapi.esign.cn' //正式环境
    ];

    public static function getDomain()
    {
        $domain = [
            "dev" => "https://dev.sf0000.com.cn",
            "prod" => "https://bf.sf0000.com.cn"
        ];

        //判断host
        $host = $_SERVER['HTTP_HOST'];
        if (strpos($host, 'bf.sf0000') !== false) {
            return $domain['prod'];
        } else {
            return $domain['dev'];
        }
    }
}
