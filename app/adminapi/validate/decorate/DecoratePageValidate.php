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

namespace app\adminapi\validate\decorate;

use app\common\validate\BaseValidate;

/**
 * 装修页面验证
 * Class DecoratePageValidate
 * @package app\adminapi\validate\decorate
 */
class DecoratePageValidate extends BaseValidate
{

    protected $rule = [
        'id' => 'require',
        'type' => 'require',
        'data' => 'require',
    ];


    protected $message = [
        'id.require' => '参数缺失',
        'type.require' => '装修类型参数缺失',
        'data.require' => '装修信息参数缺失',
    ];
}
