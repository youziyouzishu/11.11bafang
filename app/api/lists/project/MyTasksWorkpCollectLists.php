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
use app\common\model\ProjectTasks;
use app\common\model\ProjectTasksAudit;
use app\common\service\FileService;
use think\Db;

/**
 * 文章收藏列表
 * Class ArticleCollectLists
 * @package app\api\lists\article
 */
class MyTasksWorkpCollectLists extends BaseApiDataLists
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
        //审核通过
        $where[] = ['a.status', "=", 2];
        $where[] = ['a.type', "=", 4];
        if ($this->userInfo['role'] == 2) {
            $where[] = ["a.recruit_user_id", "=", $this->userId];
        }
        if ($this->userInfo['role'] == 3) {
            $where[] = ["a.onsite_user_id", "=", $this->userId];
        }
        // 用户月明细
        if (!empty($this->params['project_id'])) {
            $where[] = ['a.project_id', '=', $this->params['project_id']];
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
        $field = "a.*,p1.psn_name as recruit_user_name,p2.psn_name as onsite_user_name,p3.psn_name as workp_name,p3.psn_mobile as workp_phone,u1.sn as recruit_sn,u2.sn as onsite_sn,u3.sn as workp_sn";
        $obj = new ProjectTasksAudit();


        $query = $obj->alias('a')
            ->where($this->queryWhere())
            ->join('admin c', 'c.id = a.creator', 'left')
            ->join('personal_verification p1', 'p1.user_id = a.recruit_user_id', 'left')
            ->join('personal_verification p2', 'p2.user_id = a.onsite_user_id', 'left')
            ->join('personal_verification p3', 'p3.user_id = a.user_id', 'left')
            ->join('user u1', 'u1.id = a.recruit_user_id', 'left')
            ->join('user u2', 'u2.id = a.onsite_user_id', 'left')
            ->join('user u3', 'u3.id = a.user_id', 'left')
            ->field($field)
            ->order(['a.id' => 'desc']);
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
