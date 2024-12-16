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


use app\common\model\ProjectDailyReport;
use app\common\logic\BaseLogic;
use app\common\model\ProjectTasksAudit;
use think\facade\Db;


/**
 * ProjectDailyReport逻辑
 * Class ProjectDailyReportLogic
 * @package app\adminapi\logic
 */
class ProjectDailyReportLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectDailyReport::create([
                'task_audit_id' => $params['task_audit_id'],
                'uid' => $params['uid'],
                'report_date' => $params['report_date'],
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'recruit_user_id' => $params['recruit_user_id'],
                'onsite_user_id' => $params['onsite_user_id']
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    //批量写入考勤
    public static function adds(array $params): bool
    {
        Db::startTrans();
        try {
            $ids = $params['ids'];

            // 查询所有提供的 ids 在 ProjectTasksAudit 表中的记录
            $tasks = ProjectTasksAudit::whereIn('id', $ids)->select()->toArray();
            $request = request();

            $client_user = $request->userInfo['user_id'] ?? 0;

            foreach ($tasks as $task) {
                // 检查记录是否已经存在于 ProjectDailyReport 表中
                $existingReport = ProjectDailyReport::where('task_audit_id', $task['id'])
                    ->where('report_date', $params['report_date'])
                    ->findOrEmpty()->toArray();
                if ($existingReport) {
                    // 如果记录存在，更新记录

                    $existingReport->update([
                        'uid' =>  $task['creator'],
                        'project_id' => $task['project_id'],
                        'user_id' => $task['user_id'],
                        'recruit_user_id' => $task['recruit_user_id'],
                        'onsite_user_id' => $task['onsite_user_id'],
                        'client_user' => $client_user
                    ]);
                } else {

                    // 如果记录不存在，创建新记录
                    ProjectDailyReport::create([
                        'task_audit_id' => $task['id'],
                        'report_date' => $params['report_date'],
                        'uid' =>  $task['creator'],
                        'project_id' => $task['project_id'],
                        'user_id' => $task['user_id'],
                        'recruit_user_id' => $task['recruit_user_id'],
                        'onsite_user_id' => $task['onsite_user_id'],
                        'client_user' => $client_user
                    ]);
                }
            }

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
     * @date 2024/09/16 15:29
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectDailyReport::where('id', $params['id'])->update([]);

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
     * @date 2024/09/16 15:29
     */
    public static function delete(array $params): bool
    {
        return ProjectDailyReport::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/09/16 15:29
     */
    public static function detail($params): array
    {
        return ProjectDailyReport::findOrEmpty($params['id'])->toArray();
    }
}
