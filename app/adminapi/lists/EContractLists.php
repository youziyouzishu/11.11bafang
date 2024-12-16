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
use app\common\model\EContract;
use app\common\lists\ListsSearchInterface;


/**
 * EContract列表
 * Class EContractLists
 * @package app\adminapi\lists
 */
class EContractLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function setSearch(): array
    {
        return [
            '=' => ['template_id', 'user_id', 'to_user_id', 'type', 'send_psn_id', 'send_org_id', 'accept_psn_id', 'accept_org_id', 'name', 'is_send', 'status', 'signFlowId', 'contract_file'],
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
        $active_name = $this->request->get()['active_name'];

        if ($active_name == 'send_contract') {
            $where[] = ['template_id', '<>', "89c201c3f07b4734a112eaac7ca45667"];
        } else {
            $where[] = ['template_id', '=', "89c201c3f07b4734a112eaac7ca45667"];
        }
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
     * @date 2024/07/14 22:24
     */
    public function lists(): array
    {
        // var_dump(EContract::with(['personalVerification'])->where($this->searchWhere)
        //     ->where($this->queryWhere())
        //     ->field(['id', 'template_id', 'user_id', 'to_user_id', 'type', 'send_psn_id', 'send_org_id', 'accept_psn_id', 'accept_org_id', 'name', 'is_send', 'status', 'signFlowId', 'contract_file'])
        //     ->limit($this->limitOffset, $this->limitLength)
        //     ->order(['id' => 'desc'])->buildSql());
        // exit;

        return EContract::with(['personalVerification'])->where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['id', 'template_id', 'user_id', 'to_user_id', 'type', 'send_psn_id', 'send_org_id', 'accept_psn_id', 'accept_org_id', 'name', 'is_send', 'status', 'signFlowId', 'contract_file'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public function count(): int
    {
        return EContract::where($this->searchWhere)
            ->where($this->queryWhere())
            ->count();
    }
}
