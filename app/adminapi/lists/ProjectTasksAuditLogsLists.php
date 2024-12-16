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

namespace app\adminapi\lists;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\ProjectTasksAuditLogs;
use app\common\lists\ListsSearchInterface;


/**
 * ProjectTasksAuditLogs列表
 * Class ProjectTasksAuditLogsLists
 * @package app\adminapi\lists
 */
class ProjectTasksAuditLogsLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function setSearch(): array
    {
        return [
            '=' => ['audit_id'],
        ];
    }

    /**
     * @notes 查询条件
     * @param bool $flag
     * @return array
     * @author 张晓科
     * @date 2023/3/1 9:51
     */
    public function queryWhere()
    {
        $where = [];
        if (!in_array($this->adminId, $this->adminIds)) {
            $where[] = ['creator', '=', $this->adminId];
        }
        return $where;
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function lists(): array
    {
        return ProjectTasksAuditLogs::with(['projectinfo', 'userinfo', 'contractinfo'])->where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['la_project_tasks_audit_logs.id', 'la_project_tasks_audit_logs.audit_id', 'la_project_tasks_audit_logs.project_id', 'la_project_tasks_audit_logs.user_id', 'la_project_tasks_audit_logs.recruit_user_id', 'la_project_tasks_audit_logs.onsite_user_id', 'la_project_tasks_audit_logs.type', 'la_project_tasks_audit_logs.status', 'la_project_tasks_audit_logs.work_status',  'la_project_tasks_audit_logs.remarks', 'la_project_tasks_audit_logs.create_time', 'la_project_tasks_audit_logs.update_time', 'la_personal_verification.psn_name as recruit_name', 'c.psn_name as onsite_name', 'e.sn as user_sn', 'f.sn as recruit_sn', 'g.sn as onsite_sn', 'h.name as admin_user', 'i.psn_name as client_user'])
            ->leftjoin('la_personal_verification', 'la_project_tasks_audit_logs.recruit_user_id=la_personal_verification.user_id')
            ->leftjoin('la_personal_verification c', 'la_project_tasks_audit_logs.onsite_user_id=c.user_id')
            ->leftjoin('la_user e', 'la_project_tasks_audit_logs.user_id=e.id')
            ->leftjoin('la_user f', 'la_project_tasks_audit_logs.onsite_user_id=f.id')
            ->leftjoin('la_user g', 'la_project_tasks_audit_logs.recruit_user_id=g.id')
            ->leftjoin('la_admin h', 'la_project_tasks_audit_logs.admin_user=h.id')
            ->leftjoin('la_personal_verification i', 'la_project_tasks_audit_logs.client_user=i.user_id')
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['la_project_tasks_audit_logs.id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public function count(): int
    {
        return ProjectTasksAuditLogs::where($this->searchWhere)->count();
    }
}
