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

use app\adminapi\logic\auth\AdminLogic;
use app\common\model\ProjectTasks;
use app\common\logic\BaseLogic;
use app\common\service\FileService;
use Exception;
use think\facade\Db;


/**
 * 项目逻辑
 * Class ProjectTasksLogic
 * @package app\adminapi\logic
 */
class ProjectTasksLogic extends BaseLogic
{


    /**
     * @notes 添加项目
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            $newProject = ProjectTasks::create([
                'project_name' => $params['project_name'],
                'task_description' => $params['task_description'],
                'creator' => $params['creator'],
                'site_managers' => $params['site_managers'],
                'recruitment_start_time' => $params['recruitment_start_time'],
                'recruitment_score_direction' => $params['recruitment_score_direction'],
                'regional_orientation' => $params['regional_orientation'],
                'recruitment_settlement_type' => $params['recruitment_settlement_type'],
                'recruitment_settlement_value' => $params['recruitment_settlement_value'],
                'site_settlement_type' => $params['site_settlement_type'],
                'site_settlement_value' => $params['site_settlement_value'],
                'is_salary' => $params['is_salary'],
                'salary_range' => $params['salary_range'],
                'salary_range_end' => $params['salary_range_end'],
                'interest_tags' => $params['interest_tags'],
                'attr_welfare' => is_array($params['attr_welfare']) ? implode(',', $params['attr_welfare']) : '',
                'work_type' => is_array($params['work_type']) ? implode(',', $params['work_type']) : '',
                'work_time' => is_array($params['work_time']) ? implode(',', $params['work_time']) : '',
                'citys' => is_array($params['citys']) ? implode(',', $params['citys']) : '',
                'up_city' => $params['up_city'] ?? '',
                'cooperative_contract' => FileService::setFileUrl($params['cooperative_contract']) ?? '',
                'min_people' =>  $params['min_people'],
                'max_people' =>  $params['max_people'],
            ]);

            $payStatus = AdminLogic::ProjectUserMoney([
                'user_id' => $params['creator'],
                'action' => 0,
                'num' => "60.00",
                'remark' => "{$params['project_name']} - ID({$newProject->id})"
            ]);
            if ($payStatus !== true) {
                throw new Exception($payStatus);
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
     * @notes 编辑项目
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasks::where('id', $params['id'])->update([
                'project_name' => $params['project_name'],
                'task_description' => $params['task_description'],
                'creator' => $params['creator'],
                'site_managers' => $params['site_managers'],
                'recruitment_specialists' => $params['recruitment_specialists'],
                'recruitment_start_time' => $params['recruitment_start_time'],
                'recruitment_score_direction' => $params['recruitment_score_direction'],
                'regional_orientation' => $params['regional_orientation'],
                'recruitment_settlement_type' => $params['recruitment_settlement_type'],
                'recruitment_settlement_value' => $params['recruitment_settlement_value'],
                'site_settlement_type' => $params['site_settlement_type'],
                'site_settlement_value' => $params['site_settlement_value'],
                'is_salary' => $params['is_salary'],
                'salary_range' => $params['salary_range'],
                'salary_range_end' => $params['salary_range_end'],
                'interest_tags' => $params['interest_tags'],
                'attr_welfare' => implode(',', $params['attr_welfare']),
                'work_type' => is_array($params['work_type']) ? implode(',', $params['work_type']) : '',
                'work_time' => is_array($params['work_time']) ? implode(',', $params['work_time']) : '',
                'citys' => is_array($params['citys']) ? implode(',', $params['citys']) : '',
                'min_people' =>  $params['min_people'],
                'max_people' =>  $params['max_people'],
                'up_city' => $params['up_city'] ?? '',
                'cooperative_contract' => FileService::setFileUrl($params['cooperative_contract']) ?? '',
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
     * @notes 删除项目
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function delete(array $params): bool
    {
        return ProjectTasks::destroy($params['id']);
    }


    /**
     * @notes 关闭项目
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function close(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasks::where('id', $params['id'])->update([
                'is_show' => 1,
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
     * @notes 审核项目
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function audit(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasks::whereIn('id', $params['id'])->update([
                'is_show' => $params['is_show'],
                'show_remarks' => $params['show_remarks']
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
     * @notes 获取项目详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2023/12/10 07:43
     */
    public static function detail($params): array
    {
        return ProjectTasks::findOrEmpty($params['id'])->toArray();
    }
}
