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
 * ProjectDailyReport验证器
 * Class ProjectDailyReportValidate
 * @package app\adminapi\validate
 */
class ProjectDailyReportValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'task_audit_id' => 'require',
        'uid' => 'require',
        'report_date' => 'require',
        'project_id' => 'require',
        'user_id' => 'require',
        'recruit_user_id' => 'require',
        'onsite_user_id' => 'require',
        'work_status' => 'require',
        'daily_salary' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'task_audit_id' => '任务审计ID',
        'uid' => '人力公司ID',
        'report_date' => '报告日期',
        'project_id' => '项目ID',
        'user_id' => '用户ID',
        'recruit_user_id' => '招聘专员ID',
        'onsite_user_id' => '驻场经理ID',
        'work_status' => '工作状态',
        'daily_salary' => '日薪',
    ];


    /**
     * @notes 添加场景
     * @return ProjectDailyReportValidate
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function sceneAdd()
    {
        return $this->only(['task_audit_id', 'uid', 'report_date', 'project_id', 'user_id', 'recruit_user_id', 'onsite_user_id', 'work_status', 'daily_salary']);
    }


    /**
     * @notes 编辑场景
     * @return ProjectDailyReportValidate
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'task_audit_id', 'uid', 'report_date', 'project_id', 'user_id', 'recruit_user_id', 'onsite_user_id', 'work_status', 'daily_salary']);
    }


    /**
     * @notes 删除场景
     * @return ProjectDailyReportValidate
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return ProjectDailyReportValidate
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 保存日报
     * @return ProjectDailyReportValidate
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function sceneDailyReportSave()
    {
        return $this->only(['ids', 'report_date']);
    }
}
