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

namespace app\api\validate;

use app\common\cache\WebScanLoginCache;
use app\common\validate\BaseValidate;

/**
 * 网站扫码登录验证
 * Class WebScanLoginValidate
 * @package app\api\validate
 */
class WebScanLoginValidate extends BaseValidate
{

    protected $rule = [
        'code' => 'require',
        'state' => 'require|checkState',
    ];

    protected $message = [
        'code.require' => '参数缺失',
        'state.require' => '昵称缺少',
    ];


    /**
     * @notes 校验登录状态标记
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 张晓科
     * @date 2023/10/21 9:47
     */
    protected function checkState($value, $rule, $data)
    {
        $check = (new WebScanLoginCache())->getScanLoginState($value);

        if (empty($check)) {
            return '二维码已失效或不存在,请重新扫码';
        }

        return true;
    }
}
