<?php

namespace app\common\http\esign\comm;

use app\common\http\esign\Config;

//定义DEMO相关目录，不要随意修改
define("ESIGN_ROOT", __DIR__);
//define("ESIGN_CLASS_PATH", ESIGN_ROOT . "/core/");

//调试模式，false：不打印相关日志；true、请设置日志文件目录以及读写权限
define('ESIGN_DEBUGE', true);


//日志文件目录
define("ESIGN_LOG_DIR", ESIGN_ROOT . '/logs/');
if (ESIGN_DEBUGE && !is_dir(ESIGN_LOG_DIR)) mkdir(ESIGN_LOG_DIR, 0777);

/**
 * esign日志类
 * @author  澄泓
 * @date  2022/08/18 15:10
 */
class EsignLogHelper
{
    static function writeLog($text, $prefix = "req")
    {
        if (is_array($text) || is_object($text)) {
            $text = json_encode($text);
        }
        $logDir = ESIGN_ROOT . '/logs/';

        file_put_contents($logDir . "{$prefix}_" . date("Y-m-d") . ".log", date("Y-m-d H:i:s") . "  " . $text . "\r\n", FILE_APPEND);
    }

    static function  printMsg($msg)
    {
        echo "<pre/>";
        if (is_array($msg) || is_object($msg)) {
            var_dump($msg);
        } else {
            echo $msg . "\n";
        }
    }
}
