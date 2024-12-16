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
 * ProjectTasksAuditShare验证器
 * Class ProjectTasksAuditShareValidate
 * @package app\adminapi\validate
 */
class ProjectTasksAuditShareValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'unikey' => 'require',
        'project_audit_id' => 'require',
        'user_id' => 'require',
        'type' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'unikey' => '分享KEY',
        'project_audit_id' => '项目审核ID',
        'user_id' => 'share用户ID',
        'type' => '审核类型【ZHAOPING】',
    ];


    /**
     * @notes 添加场景
     * @return ProjectTasksAuditShareValidate
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function sceneGetOrAdd()
    {
        return $this->only(['project_audit_id']);
    }

    /**
     * @notes 添加场景
     * @return ProjectTasksAuditShareValidate
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function sceneCheckUnikey()
    {
        return $this->only(['unikey']);
    }


    /**
     * @notes 添加场景
     * @return ProjectTasksAuditShareValidate
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function sceneAdd()
    {
        return $this->only(['unikey', 'project_audit_id', 'user_id', 'type']);
    }


    /**
     * @notes 编辑场景
     * @return ProjectTasksAuditShareValidate
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'unikey', 'project_audit_id', 'user_id', 'type']);
    }


    /**
     * @notes 删除场景
     * @return ProjectTasksAuditShareValidate
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return ProjectTasksAuditShareValidate
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
