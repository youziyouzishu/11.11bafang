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

namespace app\common\command;

use app\common\model\auth\Admin;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;


class ClearImNumber extends Command
{
    protected function configure()
    {
        $this->setName('clear_im_number')
            ->setDescription('清楚im限制次数');
    }


    protected function execute(Input $input, Output $output)
    {
        try {
            // 查找退款中的退款记录(微信，支付宝支付)

            Admin::update(['im_list' => ''], 'im_list != ""');

            return true;
        } catch (\Exception $e) {
            Log::write('订单退款状态查询失败,失败原因:' . $e->getMessage());
            return false;
        }
    }
}
