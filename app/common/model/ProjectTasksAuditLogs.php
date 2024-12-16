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
use think\model\concern\SoftDelete;


/**
 * ProjectTasksAuditLogs模型
 * Class ProjectTasksAuditLogs
 * @package app\common\model
 */
class ProjectTasksAuditLogs extends BaseModel
{
    use SoftDelete;
    protected $name = 'project_tasks_audit_logs';
    protected $deleteTime = 'delete_time';

    /**
     * @notes 关联projectinfo
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function projectinfo()
    {
        return $this->hasOne(\app\common\model\task\ProjectTasks::class, 'id', 'project_id')->field('id,project_name');
    }

    /**
     * @notes 关联userinfo
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function userinfo()
    {
        return $this->hasOne(PersonalVerification::class, 'user_id', 'user_id');
    }

    /**
     * @notes contractinfo
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function contractinfo()
    {
        return $this->hasOne(EContract::class,  'project_audit_id', 'id');
    }
}
