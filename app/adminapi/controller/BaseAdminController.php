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

declare(strict_types=1);

namespace app\adminapi\controller;

use think\App;
use app\common\controller\BaseLikeAdminController;

/**
 * 管理元控制器基类
 * Class BaseAdminController
 * @package app\adminapi\controller
 */
class BaseAdminController extends BaseLikeAdminController
{
    protected int $adminId = 0;
    protected array $adminInfo = [];

    public function initialize()
    {
        if (isset($this->request->adminInfo) && $this->request->adminInfo) {
            $this->adminInfo = $this->request->adminInfo;
            $this->adminId = $this->request->adminInfo['admin_id'];
        }
    }
}
