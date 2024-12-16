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


namespace app\common\enum;

/**
 * 通过枚举类，枚举只有两个值的时候使用
 * Class YesNoEnum
 * @package app\common\enum
 */
class RoleEnum
{
    const QIUZHIZHE = 4;
    const ZHUCHANG = 3;
    const ZHAOPING = 2;

    /**
     * @notes 获取禁用状态
     * @param bool $value
     * @return string|string[]
     * @author 令狐冲
     * @date 2021/7/8 19:02
     */
    public static function getDisableDesc($value = true)
    {
        $data = [
            self::ZHAOPING => '招聘专员',
            self::ZHUCHANG => '驻场经理',
            self::QIUZHIZHE => '求职者',
        ];
        if ($value === true) {
            return $data;
        }
        return $data[$value];
    }
}
