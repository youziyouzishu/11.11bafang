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

namespace app\common\model\auth;

use app\common\model\BaseModel;

class AdminDept extends BaseModel
{
    /**
     * @notes 删除用户关联部门
     * @param $adminId
     * @return bool
     * @author 张晓科
     * @date 2023/11/25 14:14
     */
    public static function delByUserId($adminId)
    {
        return self::where(['admin_id' => $adminId])->delete();
    }
}
