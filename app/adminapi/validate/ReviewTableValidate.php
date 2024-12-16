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
 * 1验证器
 * Class ReviewTableValidate
 * @package app\adminapi\validate
 */
class ReviewTableValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'project_id' => 'require',
        'reviewer_id' => 'require',
        'reviewer_type' => 'require',
        'target_type' => 'require',
        'target_id' => 'require',
        'score' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'project_id' => '项目名称',
        'reviewer_id' => '评价人',
        'reviewer_type' => '评价类型',
        'target_type' => '目标类型',
        'target_id' => '被评价人',
        'score' => '评分',
    ];


    /**
     * @notes 添加场景
     * @return ReviewTableValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneAdd()
    {
        return $this->only(['project_id', 'reviewer_id', 'reviewer_type', 'target_type', 'target_id', 'score']);
    }


    /**
     * @notes 编辑场景
     * @return ReviewTableValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'project_id', 'reviewer_id', 'reviewer_type', 'target_type', 'target_id', 'score']);
    }


    /**
     * @notes 删除场景
     * @return ReviewTableValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return ReviewTableValidate
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
