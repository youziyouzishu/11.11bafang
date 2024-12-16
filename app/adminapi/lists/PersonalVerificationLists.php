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
use app\common\model\PersonalVerification;
use app\common\lists\ListsSearchInterface;


/**
 * PersonalVerification列表
 * Class PersonalVerificationLists
 * @package app\adminapi\lists
 */
class PersonalVerificationLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'authorize_user_info', 'realname_status', 'psn_id', 'account_mobile', 'account_email', 'psn_name', 'psn_nationality', 'psn_id_card_num', 'psn_id_card_type', 'bank_card_num', 'psn_mobile', 'created_at', 'updated_at'],
        ];
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function lists(): array
    {
        return PersonalVerification::where($this->searchWhere)
            ->field(['id', 'user_id', 'authorize_user_info', 'realname_status', 'psn_id', 'account_mobile', 'account_email', 'psn_name', 'psn_nationality', 'psn_id_card_num', 'psn_id_card_type', 'bank_card_num', 'psn_mobile', 'created_at', 'updated_at'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function count(): int
    {
        return PersonalVerification::where($this->searchWhere)->count();
    }
}
