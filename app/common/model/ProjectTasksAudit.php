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
use app\common\model\ProjectTasksAuditLogs;

/**
 * ProjectTasksAudit模型
 * Class ProjectTasksAudit
 * @package app\common\model
 */
class ProjectTasksAudit extends BaseModel
{
    use SoftDelete;
    protected $name = 'project_tasks_audit';
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


    public static function update(array $data, $where = [], array $allowField = [], string $suffix = '')
    {
        if (!empty($data['id']) && empty($where)) {
            $id = $data['id'];
        } else if (!empty($where['id'])) {
            $id = $where['id'];
        }

        $upBeforLog =  parent::findOrEmpty($id)->toArray();
        $lastdata = array_merge($upBeforLog, $data);
        $lastdata['audit_id'] =  $lastdata['id'];
        $lastdata['create_time'] =  time();
        $lastdata['update_time'] =  time();
        $request = request();

        $lastdata['admin_user'] = $request->adminInfo['admin_id'] ?? 0;
        $lastdata['client_user'] = $request->userInfo['user_id'] ?? 0;
        unset($lastdata['id']);
        ProjectTasksAuditLogs::create($lastdata);
        return parent::update($data, $where, $allowField, $suffix);
    }
}
