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
 * PersonalVerification验证器
 * Class PersonalVerificationValidate
 * @package app\adminapi\validate
 */
class PersonalVerificationValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'user_id' => 'require',
        'authorize_user_info' => 'require',
        'realname_status' => 'require',
        'psn_id' => 'require',
        'psn_name' => 'require',
        'psn_id_card_num' => 'require',
        'psn_id_card_type' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'user_id' => '用户ID',
        'authorize_user_info' => '用户信息授权状态',
        'realname_status' => '实名状态: 0-未实名, 1-已实名',
        'psn_id' => '个人ID',
        'psn_name' => '个人姓名',
        'psn_id_card_num' => '身份证号码',
        'psn_id_card_type' => '证件类型',
    ];


    /**
     * @notes 添加场景
     * @return PersonalVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function sceneAdd()
    {
        return $this->only(['user_id', 'authorize_user_info', 'realname_status', 'psn_id', 'psn_name', 'psn_id_card_num', 'psn_id_card_type']);
    }


    /**
     * @notes 编辑场景
     * @return PersonalVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'user_id', 'authorize_user_info', 'realname_status', 'psn_id', 'psn_name', 'psn_id_card_num', 'psn_id_card_type']);
    }


    /**
     * @notes 删除场景
     * @return PersonalVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return PersonalVerificationValidate
     * @author ideaadmin
     * @date 2024/06/04 13:03
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
