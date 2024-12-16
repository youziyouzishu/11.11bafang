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
 * 项目验证器
 * Class ProjectTasksValidate
 * @package app\adminapi\validate
 */
class ProjectTasksValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'project_name' => 'require',
        'task_description' => 'require',
        'recruitment_start_time' => 'require',
        'recruitment_settlement_type' => 'require',
        'site_settlement_type' => 'require',
        'salary_range' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'project_name' => '项目名称',
        'task_description' => '任务描诉',
        'creator' => '创建人',
        'recruitment_start_time' => '招聘起始时间',
        'recruitment_settlement_type' => '招聘结算类型',
        'site_settlement_type' => '驻场结算类型',
        'salary_range' => '员工薪资范围',
    ];


    /**
     * @notes 添加场景
     * @return ProjectTasksValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneAdd()
    {
        return $this->only(['project_name', 'task_description', 'creator', 'recruitment_start_time', 'recruitment_settlement_type', 'site_settlement_type',  'work_type', 'work_time', 'citys', 'min_people', 'max_people', 'attr_welfare', 'up_city', 'cooperative_contract']);
    }


    /**
     * @notes 编辑场景
     * @return ProjectTasksValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'project_name', 'task_description', 'creator', 'recruitment_start_time', 'recruitment_settlement_type', 'site_settlement_type',  'work_type', 'work_time', 'citys', 'min_people', 'max_people', 'attr_welfare', 'up_city', 'cooperative_contract']);
    }


    /**
     * @notes 删除场景
     * @return ProjectTasksValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 审核场景
     * @return ProjectTasksValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneAudit()
    {
        return $this->only(['id', 'is_show', 'show_remarks']);
    }

    /**
     * @notes 详情场景
     * @return ProjectTasksValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
