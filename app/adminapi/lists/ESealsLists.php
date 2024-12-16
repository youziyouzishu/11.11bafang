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
use app\common\model\ESeals;
use app\common\lists\ListsSearchInterface;


/**
 * ESeals列表
 * Class ESealsLists
 * @package app\adminapi\lists
 */
class ESealsLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'target_id', 'type', 'seal_name'],
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
            $where[] = ['user_id', '=', $this->adminId];
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
     * @date 2024/06/09 09:41
     */
    public function lists(): array
    {
        return ESeals::where($this->searchWhere)
            ->field(['id', 'user_id', 'target_id', 'type', 'seal_name', 'seal_template_style', 'seal_opacity', 'seal_color', 'seal_old_style', 'seal_size'])
            ->limit($this->limitOffset, $this->limitLength)
            ->where($this->queryWhere())
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function count(): int
    {
        return ESeals::where($this->searchWhere)->where($this->queryWhere())->count();
    }
}
