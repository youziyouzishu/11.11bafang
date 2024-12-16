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

use app\api\logic\TasksLogic;
use app\common\model\ProjectTasksAuditShare;
use app\common\logic\BaseLogic;
use PDO;
use think\facade\Db;


/**
 * ProjectTasksAuditShare逻辑
 * Class ProjectTasksAuditShareLogic
 * @package app\adminapi\logic
 */
class ProjectTasksAuditShareLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public static function getOrAdd(array $params)
    {
        Db::startTrans();
        try {
            $find = ProjectTasksAuditShare::where(['user_id' => $params['user_id'], 'project_audit_id' => $params['project_audit_id'], 'type' => $params['type']])->find();
            if ($find) {
                return $find->unikey;
            }
            $unikey = substr(strtoupper(md5(time() . uniqid())), 0, 16);
            ProjectTasksAuditShare::create([
                'unikey' =>  $unikey,
                'project_audit_id' => $params['project_audit_id'],
                'user_id' => $params['user_id'],
                'type' => $params['type']
            ]);

            Db::commit();
            return $unikey;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasksAuditShare::create([
                'unikey' => $params['unikey'],
                'project_audit_id' => $params['project_audit_id'],
                'user_id' => $params['user_id'],
                'type' => $params['type']
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
     * @date 2024/08/13 21:21
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasksAuditShare::where('id', $params['id'])->update([
                'unikey' => $params['unikey'],
                'project_audit_id' => $params['project_audit_id'],
                'user_id' => $params['user_id'],
                'type' => $params['type']
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
     * @date 2024/08/13 21:21
     */
    public static function delete(array $params): bool
    {
        return ProjectTasksAuditShare::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public static function detail($params): array
    {
        return ProjectTasksAuditShare::findOrEmpty($params['id'])->toArray();
    }

    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/08/13 21:21
     */
    public static function check($params)
    {
        $sSql = "select b.*,'入职邀请' as  'title',concat('公司名称:',c.org_name,'\n\n邀请人:',d.psn_name) as content,'/pages/contract/index' as toUrl from (
            select * from la_project_tasks_audit_share where unikey = :unikey
        ) as a left join
        la_project_tasks_audit as b
        on a.project_audit_id = b.id
        left join 
        la_enterprise_verification as c
        on b.creator = c.user_id 
        left join 
        la_personal_verification as d
        on b.user_id = d.user_id   
        ";
        $result = Db::query($sSql, ["unikey" => $params['unikey']]);
        if ($result && isset($params['enter']) && $params['enter'] == 1) {

            //同意逻辑
            $sendData = [
                'user_id' => $params['user_id'],
                'project_id' => $result[0]['project_id'],
                'creator' => $result[0]['creator'],
                'recruit_user_id' => $result[0]['user_id'],
                'role' => '4',
            ];
            if (!TasksLogic::staffSendAudit($sendData)) {
                return false;
            }
        }
        return  $result[0] ?? [];
    }
}
