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
use app\common\model\ReviewTable;
use app\common\lists\ListsSearchInterface;
use think\facade\Db;

/**
 * 1列表
 * Class ReviewTableLists
 * @package app\adminapi\lists
 */
class ReviewTableLists extends BaseAdminDataLists implements ListsSearchInterface
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
            '=' => ['reviewer_id', 'reviewer_type', 'target_type', 'target_id', 'score'],
            '%like%' => ['review_content']
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
            $where[] = ['target_id', '=', $this->adminId];
        }
        //user_id or target_id 

        if (!empty($this->params['project_name'])) {
            $where[] = ['project.project_name', 'like', '%' . $this->params['project_name'] . '%'];
        }

        // if (!empty($this->params['project_name'])) {
        //     $where[] = ['project.project_name', 'like', '%' . $this->params['project_name'] . '%'];
        // }
        return $where;
    }


    /**
     * @notes 获取1列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function lists(): array
    {
        // $sql = ReviewTable::where($this->searchWhere)
        //     ->alias('l')
        //     ->field(['l.*', 'reviewer.nickname reviewer_name', 'project.project_name', 'target.nickname target_name'])
        //     ->leftjoin('la_user reviewer', 'l.reviewer_id=reviewer.id')
        //     ->leftjoin('la_user target', 'l.target_id=target.id')
        //     ->leftjoin('la_project_tasks project', 'l.reviewer_id=project.id')
        //     ->limit($this->limitOffset, $this->limitLength)
        //     ->order(['l.id' => 'desc'])
        //     ->fetchSql(true)
        //     ->select();
        return ReviewTable::where($this->searchWhere)
            ->where($this->queryWhere())
            ->alias('l')
            ->field(['l.*', 'reviewer.nickname reviewer_name', 'project.project_name', 'target.nickname target_name'])
            ->leftjoin('la_user reviewer', 'l.reviewer_id=reviewer.id')
            ->leftjoin('la_user target', 'l.target_id=target.id')
            ->leftjoin('la_project_tasks project', 'l.project_id=project.id')
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['l.id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取1数量
     * @return int
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function count(): int
    {
        return ReviewTable::where($this->searchWhere)->count();
    }


    /**
     * @notes 获取1详情
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info($id): array
    {
        return ReviewTable::where(['id' => $id])->find()->toArray();
    }

    /**
     * @notes 获取列表用户名
     * @return array
     */
    public function getUserName(): array
    {
        return ReviewTable::where($this->searchWhere)->column('reviewer_name');
    }
}
