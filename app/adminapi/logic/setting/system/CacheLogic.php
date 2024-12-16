<?php
// +----------------------------------------------------------------------
// | 快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：
// | github下载：

// | imadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: imadmin
// +----------------------------------------------------------------------

namespace app\adminapi\logic\setting\system;

use app\common\logic\BaseLogic;
use think\facade\Cache;

/**
 * 系统缓存逻辑
 * Class CacheLogic
 * @package app\adminapi\logic\setting\system
 */
class CacheLogic extends BaseLogic
{
    /**
     * @notes 清楚系统缓存
     * @author 张晓科
     * @date 2023/4/8 16:29
     */
    public static function clear()
    {
        Cache::clear();
        del_target_dir(app()->getRootPath() . 'runtime/file', true);
    }
}
