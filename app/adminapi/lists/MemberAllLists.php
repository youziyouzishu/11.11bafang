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
use app\common\model\user\User;

/**
 * ProjectTasksAudit列表
 * Class ProjectTasksAuditLists
 * @package app\adminapi\lists
 */
class MemberAllLists extends BaseAdminDataLists implements ListsSearchInterface
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
            '=' => ['l.role'],
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
        $where[] = ['l.flow_status', '=', 3];
        if (!empty($this->params['role'])) {
            $where[] =  ['l.role', 'in', [intval($this->params['role'])]];
        } else {
            $where[] = ['l.role', 'in', [2, 3]];
        }

        $where[] = ['b.realname_status', '=', 4];
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
        // var_dump(User::alias('l')->where($this->queryWhere())
        //     ->field(['l.id', 'l.avatar', 'b.psn_name', 'b.psn_mobile'])
        //     ->leftjoin('la_personal_verification b', 'b.user_id=l.id')
        //     ->limit($this->limitOffset, $this->limitLength)
        //     ->buildSql());
        // exit;
        return User::alias('l')->where($this->queryWhere())
            ->field(['l.id', 'l.avatar', 'b.psn_name', 'b.psn_mobile', 'l.profile', 'l.role'])
            ->leftjoin('la_personal_verification b', 'b.user_id=l.id')
            ->limit($this->limitOffset, $this->limitLength)
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
        return User::alias('l')->where($this->queryWhere())
            ->field(['l.id', 'l.avatar', 'b.psn_name', 'b.psn_mobile'])
            ->leftjoin('la_personal_verification b', 'b.user_id=l.id')->count();
    }
}
