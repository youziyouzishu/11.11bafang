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
use think\model\concern\SoftDelete;


/**
 * ProjectDailyReport模型
 * Class ProjectDailyReport
 * @package app\common\model
 */
class ProjectDailyReport extends BaseModel
{
    use SoftDelete;
    protected $name = 'project_daily_report';
    protected $deleteTime = 'delete_time';


    /**
     * @notes 关联人力公司
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function hr()
    {
        return $this->hasOne(\app\common\model\EnterpriseVerification::class, 'user_id', 'uid');
    }

    /**
     * @notes 关联驻场经理
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function onsite()
    {
        return $this->hasOne(\app\common\model\PersonalVerification::class, 'user_id', 'onsite_user_id');
    }

    /**
     * @notes 关联招聘经理
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function recruit()
    {
        return $this->hasOne(\app\common\model\PersonalVerification::class, 'user_id', 'recruit_user_id');
    }

    /**
     * @notes 关联员工
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function work()
    {
        return $this->hasOne(\app\common\model\PersonalVerification::class, 'user_id', 'user_id');
    }

    /**
     * @notes 关联项目
     * @return \think\model\relation\HasOne
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public function project()
    {
        return $this->hasOne(\app\common\model\ProjectTasks::class, 'id', 'project_id')->field('id,project_name');
    }
}
