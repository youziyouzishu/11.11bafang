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
 * AdminMsg验证器
 * Class AdminMsgValidate
 * @package app\adminapi\validate
 */
class AdminMsgValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'msg_type' => 'require',
        'title' => 'require',
        'message' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'msg_type' => '类型',
        'title' => '标题',
        'message' => '内容',
    ];


    /**
     * @notes 添加场景
     * @return AdminMsgValidate
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function sceneAdd()
    {
        return $this->only(['msg_type', 'title', 'message']);
    }


    /**
     * @notes 编辑场景
     * @return AdminMsgValidate
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'msg_type', 'title', 'message']);
    }


    /**
     * @notes 删除场景
     * @return AdminMsgValidate
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 阅读场景
     * @return AdminMsgValidate
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function sceneReview()
    {
        return $this->only(['id']);
    }
    /**
     * @notes 详情场景
     * @return AdminMsgValidate
     * @author ideaadmin
     * @date 2024/01/09 20:18
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
