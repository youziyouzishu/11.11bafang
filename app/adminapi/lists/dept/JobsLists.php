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

namespace app\adminapi\lists\dept;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\dept\Jobs;

/**
 * 岗位列表
 * Class JobsLists
 * @package app\adminapi\lists\dept
 */
class JobsLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
{

    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author 张晓科
     * @date 2023/5/26 9:46
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '=' => ['code', 'status']
        ];
    }


    /**
     * @notes  获取管理列表
     * @return array
     * @author heshihu
     * @date 2023/2/21 17:11
     */
    public function lists(): array
    {
        $lists = Jobs::where($this->searchWhere)
            ->append(['status_desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        return $lists;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author 张晓科
     * @date 2023/5/26 9:48
     */
    public function count(): int
    {
        return Jobs::where($this->searchWhere)->count();
    }


    /**
     * @notes 导出文件名
     * @return string
     * @author 张晓科
     * @date 2023/11/24 16:17
     */
    public function setFileName(): string
    {
        return '岗位列表';
    }


    /**
     * @notes 导出字段
     * @return string[]
     * @author 张晓科
     * @date 2023/11/24 16:17
     */
    public function setExcelFields(): array
    {
        return [
            'code' => '岗位编码',
            'name' => '岗位名称',
            'remark' => '备注',
            'status_desc' => '状态',
            'create_time' => '添加时间',
        ];
    }
}
