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

namespace app\adminapi\controller;


use app\api\logic\SmsLogic;
use app\api\validate\SendSmsValidate;


/**
 * 短信
 * Class SmsController
 * @package app\api\controller
 */
class SmsController extends BaseAdminController
{

    public array $notNeedLogin = ['sendCode'];


    /**
     * @notes 发送短信验证码
     * @return \think\response\Json
     * @author 张晓科
     * @date 2023/9/15 16:17
     */
    public function sendCode()
    {
        $params = (new SendSmsValidate())->post()->goCheck();
        $result = SmsLogic::sendCode($params);
        if (true === $result) {
            return $this->success('发送成功', [], 1, 1);
        }
        return $this->fail(SmsLogic::getError());
    }
}
