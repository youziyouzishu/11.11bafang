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
 * ESeals验证器
 * Class ESealsValidate
 * @package app\adminapi\validate
 */
class ESealsValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'user_id' => 'require',
        'type' => 'require',
        'seal_name' => 'require',
        'seal_template_style' => 'require',
        'seal_opacity' => 'require',
        'seal_color' => 'require',
        'seal_old_style' => 'require',
        'seal_size' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'user_id' => '用户ID',
        'type' => '用户类型',
        'seal_name' => '印章名称',
        'seal_template_style' => '印章模板样式',
        'seal_opacity' => '印章透明度',
        'seal_color' => '印章颜色',
        'seal_old_style' => '旧样式编码',
        'seal_size' => '印章尺寸（宽_高，例如40_40）',
    ];


    /**
     * @notes 添加场景
     * @return ESealsValidate
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function sceneAdd()
    {
        return $this->only(['user_id', 'type', 'seal_name', 'seal_template_style', 'seal_opacity', 'seal_color', 'seal_old_style', 'seal_size']);
    }


    /**
     * @notes 编辑场景
     * @return ESealsValidate
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'user_id', 'type', 'seal_name', 'seal_template_style', 'seal_opacity', 'seal_color', 'seal_old_style', 'seal_size']);
    }


    /**
     * @notes 删除场景
     * @return ESealsValidate
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return ESealsValidate
     * @author ideaadmin
     * @date 2024/06/09 09:41
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
