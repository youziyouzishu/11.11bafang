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
use app\common\model\ProjectTasksAudit;
use app\common\lists\ListsSearchInterface;


/**
 * ProjectTasksAudit列表
 * Class ProjectTasksAuditLists
 * @package app\adminapi\lists
 */
class ProjectTasksAuditLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function setSearch(): array
    {
        return [
            '=' => ['project_id', 'user_id', 'type', 'status', 'remarks'],
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
     * @date 2024/01/07 18:30
     */
    public function lists(): array
    {

        return ProjectTasksAudit::with(['projectinfo', 'userinfo', 'contractinfo'])->where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['la_project_tasks_audit.id', 'la_project_tasks_audit.project_id', 'la_project_tasks_audit.user_id', 'la_project_tasks_audit.recruit_user_id', 'la_project_tasks_audit.onsite_user_id', 'la_project_tasks_audit.type', 'la_project_tasks_audit.status', 'la_project_tasks_audit.work_status',  'la_project_tasks_audit.remarks', 'la_project_tasks_audit.create_time', 'la_project_tasks_audit.update_time', 'la_personal_verification.psn_name as recruit_name', 'c.psn_name as onsite_name', 'e.sn as user_sn', 'f.sn as onsite_sn', 'g.sn as recruit_sn'])
            ->leftjoin('la_personal_verification', 'la_project_tasks_audit.recruit_user_id=la_personal_verification.user_id')
            ->leftjoin('la_personal_verification c', 'la_project_tasks_audit.onsite_user_id=c.user_id')
            ->leftjoin('la_user e', 'la_project_tasks_audit.user_id=e.id')
            ->leftjoin('la_user f', 'la_project_tasks_audit.onsite_user_id=f.id')
            ->leftjoin('la_user g', 'la_project_tasks_audit.recruit_user_id=g.id')
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['status' => 'asc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public function count(): int
    {
        return ProjectTasksAudit::where($this->searchWhere)->count();
    }
}
