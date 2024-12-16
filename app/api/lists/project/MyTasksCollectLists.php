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

namespace app\api\lists\project;

use app\api\lists\BaseApiDataLists;
use app\common\enum\YesNoEnum;
use app\common\model\ProjectTasks;
use app\common\model\ProjectTasksAudit;
use app\common\service\FileService;
use think\Db;

/**
 * 文章收藏列表
 * Class ArticleCollectLists
 * @package app\api\lists\article
 */
class MyTasksCollectLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author 张晓科
     * @date 2023/2/24 14:43
     */
    public function queryWhere()
    {
        $where = [];

        // 用户月明细
        if (!empty($this->params['city'])) {
            $where[] = ['', 'exp', Db::raw("FIND_IN_SET('{$this->params['city']}', a.citys)")];
        }

        // 变动类型

        return $where;
    }

    /**
     * @notes 获取收藏列表
     * @return array
     * @author 张晓科
     * @date 2023/9/20 16:29
     */
    public function lists(): array
    {
        $field = "a.*,a.click_virtual+a.click_actual as click,c.avatar,e.org_name as company_name,d.id as project_audit_id,d.status as audit_status,f.status as contract_status";
        $obj = new ProjectTasks();

        // 子查询示例
        $subQuery = ProjectTasksAudit::field('project_id')
            ->where('user_id', $this->userId) // 替换成实际的字段和条件
            ->buildSql();


        $query = $obj->alias('a')
            ->join('admin c', 'c.id = a.creator', 'left')
            ->join('project_tasks_audit d', 'd.project_id = a.id and d.user_id=' . $this->userId, 'left')
            ->join('e_contract f', 'f.project_audit_id = d.id and f.to_user_id=' . $this->userId, 'left')
            ->join('enterprise_verification e', 'e.user_id = a.creator', 'left')
            ->field($field)
            ->where("a.id IN ($subQuery)") // 将子查询应用到主查询
            ->order(['a.id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength);
        if (!empty($this->params['city'])) {
            $query->whereFindInSet('a.citys', "{$this->params['city']}");
        }
        // 打印 SQL 查询
        // echo $query->buildSql();
        // exit;

        $lists = $query->hidden(['task_description', 'click_actual'])->select()->toArray();

        foreach ($lists as &$item) {
            if (!empty($item['avatar'])) {
                $item['avatar'] = FileService::getFileUrl($item['avatar']);
            }
        }

        return $lists;
    }


    /**
     * @notes 获取收藏数量
     * @return int
     * @author 张晓科
     * @date 2023/9/20 16:29
     */
    public function count(): int
    {
        // 子查询示例
        $subQuery = ProjectTasksAudit::field('project_id')
            ->where('user_id', $this->userId) // 替换成实际的字段和条件
            ->buildSql();
        return (new ProjectTasks())->alias('a')
            ->where("a.id IN ($subQuery)") // 将子查询应用到主查询
            ->where([
                'a.is_show' => 0,
            ])
            ->count();
    }
}
