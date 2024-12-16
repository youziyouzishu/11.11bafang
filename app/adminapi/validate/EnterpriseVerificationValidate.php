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

namespace app\adminapi\validate;


use app\common\validate\BaseValidate;


/**
 * EnterpriseVerification验证器
 * Class EnterpriseVerificationValidate
 * @package app\adminapi\validate
 */
class EnterpriseVerificationValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'user_id' => 'require',
        'org_id' => 'require',
        'org_name' => 'require',
        'org_id_card_num' => 'require',
        'org_id_card_type' => 'require',
        'legal_rep_name' => 'require',
        'admin_name' => 'require',
        'admin_account' => 'require',
        'realname_status' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'user_id' => '用户ID',
        'org_id' => '组织ID',
        'org_name' => '组织名称',
        'org_id_card_num' => '组织证件号码',
        'org_id_card_type' => '组织证件类型',
        'legal_rep_name' => '法人代表姓名',
        'admin_name' => '管理员姓名',
        'admin_account' => '管理员账号',
        'realname_status' => '实名认证状态',
    ];


    /**
     * @notes 添加场景
     * @return EnterpriseVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function sceneAdd()
    {
        return $this->only(['user_id', 'org_id', 'org_name', 'org_id_card_num', 'org_id_card_type', 'legal_rep_name', 'admin_name', 'admin_account', 'realname_status']);
    }


    /**
     * @notes 编辑场景
     * @return EnterpriseVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'user_id', 'org_id', 'org_name', 'org_id_card_num', 'org_id_card_type', 'legal_rep_name', 'admin_name', 'admin_account', 'realname_status']);
    }


    /**
     * @notes 删除场景
     * @return EnterpriseVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return EnterpriseVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 12:01
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
