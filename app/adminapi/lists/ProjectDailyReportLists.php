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
use app\common\model\ProjectDailyReport;
use app\common\lists\ListsSearchInterface;


/**
 * ProjectDailyReport列表
 * Class ProjectDailyReportLists
 * @package app\adminapi\lists
 */
class ProjectDailyReportLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function setSearch(): array
    {
        return [
            '=' => ['task_audit_id', 'uid', 'report_date', 'project_id'],
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
        if ($this->adminId != 0 && !in_array($this->adminId, $this->adminIds)) {
            $where[] = ['uid', '=', $this->adminId];
        }

        if ($this->userId != 0 && $this->userInfo['role'] == 2) {
            $where[] = ['recruit_user_id', '=', $this->userId];
        }

        if ($this->userId != 0 && $this->userInfo['role'] == 3) {
            $where[] = ['onsite_user_id', '=', $this->userId];
        }

        if ($this->userId != 0 && $this->userInfo['role'] == 4) {
            $where[] = ['user_id', '=', $this->userId];
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
     * @date 2024/09/16 15:29
     */
    public function lists(): array
    {

        return ProjectDailyReport::with(['project', 'hr', 'onsite', 'recruit', 'work'])->where($this->searchWhere)
            ->field(['id', 'task_audit_id', 'uid', 'report_date', 'project_id', 'user_id', 'recruit_user_id', 'onsite_user_id', 'work_status', 'client_user', 'daily_salary'])
            ->where($this->queryWhere())
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function count(): int
    {
        return ProjectDailyReport::where($this->searchWhere)->count();
    }
}
