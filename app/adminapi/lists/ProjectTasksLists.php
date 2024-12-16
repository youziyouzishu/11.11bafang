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
use app\common\model\ProjectTasks;
use app\common\lists\ListsSearchInterface;


/**
 * 项目列表
 * Class ProjectTasksLists
 * @package app\adminapi\lists
 */
class ProjectTasksLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function setSearch(): array
    {
        return [
            '=' => ['is_show'],
            '%like%' => ['la_project_tasks.project_name'],
            '%like%' => ['la_project_tasks.task_description'],
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
            $where[] = ['la_project_tasks.creator', '=', $this->adminId];
        }
        if (!empty($this->params['project_name'])) {
            $where[] = ['la_project_tasks.project_name', 'like', '%' . $this->params['project_name'] . '%'];
        }
        return $where;
    }


    /**
     * @notes 获取项目列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function lists(): array
    {

        $list = ProjectTasks::where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['la_admin.name as nickname', 'la_project_tasks.is_show', 'la_project_tasks.create_time', 'la_project_tasks.site_settlement_value', 'la_project_tasks.recruitment_settlement_value', 'la_project_tasks.id', 'la_project_tasks.creator', 'la_project_tasks.project_name', 'la_project_tasks.task_description', 'la_project_tasks.site_score_direction', 'la_project_tasks.recruitment_start_time', 'la_project_tasks.recruitment_score_direction', 'la_project_tasks.recruitment_settlement_type', 'la_project_tasks.site_settlement_type', 'la_project_tasks.salary_range', 'la_project_tasks.is_salary', 'la_project_tasks.salary_range_end', 'la_project_tasks.interest_tags', 'la_project_tasks.attr_welfare', 'la_project_tasks.project_score', 'la_project_tasks.work_type', 'la_project_tasks.citys', 'la_project_tasks.work_time', 'la_project_tasks.min_people', 'la_project_tasks.max_people', 'la_project_tasks.click_actual', 'la_project_tasks.up_city', 'la_project_tasks.cooperative_contract', 'la_project_tasks.show_remarks', 'la_enterprise_verification.org_name'])
            ->leftjoin('la_admin', 'la_project_tasks.creator=la_admin.id')
            ->leftjoin('la_enterprise_verification', 'la_project_tasks.creator=la_enterprise_verification.user_id')
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['la_project_tasks.id' => 'desc', 'la_project_tasks.is_show' => 'asc'])
            ->select()
            ->toArray();
        foreach ($list as $key => $value) {
            $list[$key]['attr_welfare'] = explode(',', $value['attr_welfare']);
            $list[$key]['work_type'] = explode(',', $value['work_type']);
            $list[$key]['work_time'] = explode(',', $value['work_time']);
            $list[$key]['citys'] = explode(',', $value['citys']);
        }
        return $list;
    }


    /**
     * @notes 获取项目数量
     * @return int
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function count(): int
    {
        return ProjectTasks::where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['la_admin.name', 'la_project_tasks.is_show', 'la_project_tasks.create_time', 'la_project_tasks.site_settlement_value', 'la_project_tasks.recruitment_settlement_value', 'la_project_tasks.id', 'la_project_tasks.creator', 'la_project_tasks.project_name', 'la_project_tasks.task_description', 'la_project_tasks.site_score_direction', 'la_project_tasks.recruitment_start_time', 'la_project_tasks.recruitment_score_direction', 'la_project_tasks.recruitment_settlement_type', 'la_project_tasks.site_settlement_type', 'la_project_tasks.salary_range', 'la_project_tasks.is_salary', 'la_project_tasks.salary_range_end', 'la_project_tasks.interest_tags', 'la_project_tasks.attr_welfare', 'la_project_tasks.project_score'])
            ->leftjoin('la_admin', 'la_project_tasks.creator=la_admin.id')
            ->count();
    }
}
