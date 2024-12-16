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

namespace app\common\model;


use app\common\model\BaseModel;



/**
 * EContract模型
 * Class EContract
 * @package app\common\model
 */
class EContract extends BaseModel
{

    protected $name = 'e_contract';


    public function personalVerification()
    {
        return $this->hasOne(\app\common\model\PersonalVerification::class, 'psn_id', 'accept_psn_id');
    }

    public function enterpriseVerification()
    {
        return $this->hasOne(\app\common\model\EnterpriseVerification::class, 'user_id', 'user_id')->field("user_id,org_name");
    }

    /**
     * @notes 关联projectinfo
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function projectinfo()
    {
        return $this->hasOne(\app\common\model\task\ProjectTasks::class, 'id', 'project_id')->field("id,project_name");
    }
}
