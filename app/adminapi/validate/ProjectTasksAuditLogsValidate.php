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

namespace app\adminapi\validate;


use app\common\validate\BaseValidate;


/**
 * ProjectTasksAuditLogs验证器
 * Class ProjectTasksAuditLogsValidate
 * @package app\adminapi\validate
 */
class ProjectTasksAuditLogsValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'audit_id' => 'require',
        'recruit_user_id' => 'require',
        'onsite_user_id' => 'require',
        'work_status' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'audit_id' => '审计ID',
        'recruit_user_id' => '招聘专员ID',
        'onsite_user_id' => '驻场经理ID',
        'work_status' => '工作状态 - 1 待上岗',
    ];


    /**
     * @notes 添加场景
     * @return ProjectTasksAuditLogsValidate
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function sceneAdd()
    {
        return $this->only(['audit_id', 'recruit_user_id', 'onsite_user_id', 'work_status']);
    }


    /**
     * @notes 编辑场景
     * @return ProjectTasksAuditLogsValidate
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'audit_id', 'recruit_user_id', 'onsite_user_id', 'work_status']);
    }


    /**
     * @notes 删除场景
     * @return ProjectTasksAuditLogsValidate
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return ProjectTasksAuditLogsValidate
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
