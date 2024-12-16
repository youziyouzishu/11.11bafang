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

namespace app\adminapi\logic;


use app\common\model\ProjectTasksAuditLogs;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * ProjectTasksAuditLogs逻辑
 * Class ProjectTasksAuditLogsLogic
 * @package app\adminapi\logic
 */
class ProjectTasksAuditLogsLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasksAuditLogs::create([
                'audit_id' => $params['audit_id'],
                'creator' => $params['creator'],
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'recruit_user_id' => $params['recruit_user_id'],
                'onsite_user_id' => $params['onsite_user_id'],
                'type' => $params['type'],
                'status' => $params['status'],
                'work_status' => $params['work_status'],
                'remarks' => $params['remarks'],
                'sign_flow_id' => $params['sign_flow_id']
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasksAuditLogs::where('id', $params['id'])->update([
                'audit_id' => $params['audit_id'],
                'creator' => $params['creator'],
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'recruit_user_id' => $params['recruit_user_id'],
                'onsite_user_id' => $params['onsite_user_id'],
                'type' => $params['type'],
                'status' => $params['status'],
                'work_status' => $params['work_status'],
                'remarks' => $params['remarks'],
                'sign_flow_id' => $params['sign_flow_id']
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public static function delete(array $params): bool
    {
        return ProjectTasksAuditLogs::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/09/15 18:51
     */
    public static function detail($params): array
    {
        return ProjectTasksAuditLogs::findOrEmpty($params['id'])->toArray();
    }
}
