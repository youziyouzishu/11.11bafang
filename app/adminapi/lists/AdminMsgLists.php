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
use app\common\model\AdminMsg;
use app\common\lists\ListsSearchInterface;


/**
 * AdminMsg列表
 * Class AdminMsgLists
 * @package app\adminapi\lists
 */
class AdminMsgLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'msg_type', 'title', 'message', 'review_time'],
            'between_time' => ['create_time'],
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
     * @date 2024/01/09 20:18
     */
    public function lists(): array
    {
        return AdminMsg::where($this->searchWhere)
            ->field(['id', 'user_id', 'msg_type', 'title', 'message', 'review_time', 'create_time'])
            ->limit($this->limitOffset, $this->limitLength)
            ->where($this->queryWhere())
            ->order(['review_time' => 'asc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }

    /**
     * @notes 消息状态
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public static function  notificationList(): array
    {


        $categories = [
            [
                'name' => '全部',
                'type' => "",
                'count' => 0
            ],
            [
                'name' => '邀请&权限',
                'type' => "1",
                'count' => 0
            ],
            [
                'name' => '审核信息',
                'type' => "2",
                'count' => 0
            ],
            [
                'name' => '财务消息',
                'type' => "3",
                'count' => 0
            ],
            [
                'name' => '日报消息',
                'type' => "4",
                'count' => 0
            ],
            [
                'name' => '周报消息',
                'type' => "5",
                'count' => 0
            ],
            [
                'name' => '功能上线',
                'type' => "6",
                'count' => 0
            ],
            [
                'name' => '公告通知',
                'type' => "7",
                'count' => 0
            ],
        ];

        // 使用时可以直接通过 $categories 这个变量来访问数组。

        $lists = AdminMsg::field(['msg_type', 'count(1) as rt'])
            ->where('review_time', 0)
            ->group(['msg_type'])
            ->select()
            ->toArray();
        $total = 0;
        foreach ($lists as $row) {
            $total += $row['rt'];
            if (isset($categories[$row['msg_type']])) {
                $categories[$row['msg_type']]['count'] = $row['rt'];
            }
        }
        $categories[0]['count'] = $total;
        return $categories;
    }



    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function count(): int
    {
        return AdminMsg::where($this->searchWhere)->where($this->queryWhere())->count();
    }
}
