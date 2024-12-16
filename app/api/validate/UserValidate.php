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

namespace app\api\validate;


use app\common\validate\BaseValidate;

/**
 * 用户验证器
 * Class UserValidate
 * @package app\shopapi\validate
 */
class UserValidate extends BaseValidate
{

    protected $rule = [
        'code' => 'require',
    ];

    protected $message = [
        'code.require' => '参数缺失',
    ];


    /**
     * @notes 获取小程序手机号场景
     * @return UserValidate
     * @author 张晓科
     * @date 2023/9/21 16:44
     */
    public function sceneGetMobileByMnp()
    {
        return $this->only(['code']);
    }


    /**
     * @notes 绑定/变更 手机号
     * @return UserValidate
     * @author 张晓科
     * @date 2023/9/21 17:37
     */
    public function sceneBindMobile()
    {
        return $this->only(['mobile', 'code']);
    }


    /**
     * @notes 绑定身份信息
     * @return UserValidate
     * @author 张晓科
     * @date 2023/9/21 17:37
     */
    public function sceneBindRole()
    {
        return $this->only(['role']);
    }

    /**
     * @notes 实名认证信息
     * @return UserValidate
     * @author 张晓科
     * @date 2023/9/21 17:37
     */
    public function sceneRealnameVerify()
    {
        return $this->only(['real_name',  'id_card']);
    }
    /**
     * @notes 绑定身份信息
     * @return UserValidate
     * @author 张晓科
     * @date 2023/9/21 17:37
     */
    public function sceneSaveDetail()
    {
        return $this->only(['sex',  'bank_name', 'bank_account', 'profile', 'city', 'city_address']);
    }
}
