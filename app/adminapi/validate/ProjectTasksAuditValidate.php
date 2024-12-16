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
 * ProjectTasksAudit验证器
 * Class ProjectTasksAuditValidate
 * @package app\adminapi\validate
 */
class ProjectTasksAuditValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'project_id' => 'require',
        'user_id' => 'require',
        'type' => 'require',
        'status' => 'require',
        'remarks' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'project_id' => '项目ID',
        'user_id' => '用户ID',
        'type' => '类型(1=职员,2=招聘经理,3=驻场经理)',
        'status' => '状态(1=待审核,2=通过,3=拒绝)',
        'remarks' => '拒绝说明',
    ];


    /**
     * @notes 添加场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneAdd()
    {
        return $this->only(['project_id', 'user_id', 'type', 'status', 'remarks']);
    }


    /**
     * @notes 编辑场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'project_id', 'user_id', 'type', 'status', 'remarks']);
    }


    /**
     * @notes 删除场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 审核场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneAudited()
    {
        return $this->only(['id', 'accountServiceFeeAmount', 'accountServiceFeeType', 'feeStandardType', 'feeStandardAmount', 'paymentDate', 'penaltyFee', 'status']);
    }

    /**
     * @notes 工作状态变更场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneWorkAudited()
    {
        return $this->only(['id', 'work_status']);
    }

    /**
     * @notes 变更驻场场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneChangeOnsite()
    {
        return $this->only(['id', 'onsite_user_id']);
    }

    /**
     * @notes 详情场景
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 获取项目绑定的招聘专员和驻场经理
     * @return ProjectTasksAuditValidate
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function sceneNames()
    {
        return $this->only(['project_id', 'type']);
    }
}
