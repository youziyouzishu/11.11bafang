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

namespace app\common\model;


use app\common\model\BaseModel;
use app\common\service\FileService;
use think\model\concern\SoftDelete;


/**
 * 项目模型
 * Class ProjectTasks
 * @package app\common\model
 */
class ProjectTasks extends BaseModel
{
    use SoftDelete;
    protected $name = 'project_tasks';
    protected $deleteTime = 'delete_time';


    /**
     * @notes 头像获取器 - 头像路径添加域名
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/7/13 11:35
     */
    public function getCooperativeContractAttr($value)
    {
        return empty($value) ? "" : FileService::getFileUrl(trim($value, '/'));
    }
}
