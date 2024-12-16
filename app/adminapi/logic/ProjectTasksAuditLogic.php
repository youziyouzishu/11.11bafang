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

use app\common\http\esign\OrganizeAuth;
use app\common\model\ProjectTasksAudit;
use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\common\model\EnterpriseVerification;
use app\common\model\PersonalVerification;
use app\common\model\EContract;


use app\common\model\task\ProjectTasks;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Percentiles;
use think\facade\Db;


/**
 * ProjectTasksAudit逻辑
 * Class ProjectTasksAuditLogic
 * @package app\adminapi\logic
 */
class ProjectTasksAuditLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasksAudit::create([
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'type' => $params['type'],
                'status' => $params['status'],
                'remarks' => $params['remarks']
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
     * @date 2024/01/07 18:30
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            ProjectTasksAudit::update([
                'project_id' => $params['project_id'],
                'user_id' => $params['user_id'],
                'type' => $params['type'],
                'status' => $params['status'],
                'remarks' => $params['remarks']
            ], ['id' => $params['id']]);

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
     * @date 2024/01/07 18:30
     */
    public static function delete(array $params): bool
    {
        return ProjectTasksAudit::destroy($params['id']);
    }


    /**
     * @notes 审核
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function audited(array $params)
    {
        Db::startTrans();
        try {


            $userInfo = Admin::where('id', $params['user_id'])->find();
            if ($userInfo->user_money < 100) {
                throw new Exception("余额不足100元，请充值后在发起签署合同流程");
            }

            if ($params['status'] == 3) {
                $bool = ProjectTasksAudit::update([
                    'status' => $params['status'],
                    'remarks' => $params['remarks']
                ], ['id' => $params['id']]);
                Db::commit();
                return  $bool ? true : false;
            } else {
                ProjectTasksAudit::update([
                    'status' => 2,
                ], ['id' => $params['id']]);
            }

            $auditInfo = ProjectTasksAudit::where('id', $params['id'])->find();
            if (isset($params['onsite_id'])) {
                $auditInfo->onsite_user_id = $params['onsite_id'];
                $auditInfo->save();
            }


            $projectInfo = ProjectTasks::where('id', $auditInfo['project_id'])->find();
            $psnInfo = PersonalVerification::where('user_id', $auditInfo['user_id'])->find();
            $orgInfo = EnterpriseVerification::where('user_id', $projectInfo['creator'])->find();
            $sdk =  new OrganizeAuth();
            $contractName =  $auditInfo['type'] == 2 ?? "招聘服务合同";
            $contractName =  $auditInfo['type'] == 3 ?? "驻场服务合同";
            $contractName =  $auditInfo['type'] == 4 ?? "劳务合同";
            // $result = $sdk->templatesTComponents("c738989dfff9407395b465b255dad539");
            // var_dump($result);
            // exit;
            if ($auditInfo['type'] == 2) {
                $templateId =  "a7e8c8efa21b494eabb8ec3ec247430e";
                $docTempate = $sdk->createRecruitByDocTemplate(
                    $templateId,
                    $contractName,
                    $orgInfo['org_name'],
                    $orgInfo['legal_rep_name'],
                    $orgInfo['org_id_card_num'],
                    $orgInfo['psn_mobile'],
                    $orgInfo['email'],
                    $orgInfo['address'],
                    $psnInfo['psn_name'],
                    $psnInfo['psn_id_card_num'],
                    $psnInfo['account_mobile'],
                    $params['accountServiceFeeAmount'] . " " . $params['accountServiceFeeType'], //账号服务费
                    $params['penaltyFee'], // 违约金
                    $params['feeStandardAmount'] . " " . $params['feeStandardType'], //服务费
                    $params['paymentDate'] // 服务费发放日
                );
            } else if ($auditInfo['type'] == 3) {
                $templateId =  "42a78fc8c5ab43e3a9e4b58ddce4e40b";
                $docTempate = $sdk->createZhuchangByDocTemplate(
                    $templateId,
                    $contractName,
                    $orgInfo['org_name'],
                    $orgInfo['legal_rep_name'],
                    $orgInfo['org_id_card_num'],
                    $orgInfo['psn_mobile'],
                    $orgInfo['email'],
                    $orgInfo['address'],
                    $psnInfo['psn_name'],
                    $psnInfo['psn_id_card_num'],
                    $psnInfo['account_mobile'],
                    $params['feeStandardAmount'] . " " . $params['feeStandardType'], //服务费
                    $params['paymentDate'], // 服务费发放日
                    $params['serverAddress'],
                    date("Y年m月d日", strtotime($params['serverSdate'])),
                    date("Y年m月d日", strtotime($params['serverEdate'])),
                    $params['ratio'],
                    $params['cost3'],
                    $params['cost4'],
                    $params['cost5'],
                    $params['cost6'],
                    $params['cost7'],
                    $params['cost8'],
                    $params['cost13'],
                    $params['cost14']
                );
            } else if ($auditInfo['type'] == 4) {
                $templateId =  "80a92f74aa394177a1a8b1f5de17d053";
                $docTempate = $sdk->createWorkByDocTemplate(
                    $templateId,
                    $contractName,
                    $orgInfo['org_name'],
                    $orgInfo['legal_rep_name'],
                    $orgInfo['psn_mobile'],
                    $orgInfo['address'],
                    $psnInfo['psn_name'],
                    $psnInfo['psn_id_card_num'],
                    $psnInfo['account_mobile'],
                    $params['paymentDate'], // 工资发放日
                    date("Y年m月d日", strtotime($params['workSdate'])),
                    date("Y年m月d日", strtotime($params['workEdate'])),
                    $params['workMonth'],
                    $params['workPosition'],
                    $params['payCost'],
                    date("Y年m月d日", time())
                );
            }


            if ($docTempate['code'] !== 0) {
                throw new Exception($docTempate['message']);
            }

            if ($auditInfo['type'] == 2) {
                $result = $sdk->createRecruitByFile($docTempate['data']['fileId'], $contractName, $orgInfo['org_id'], $orgInfo['psn_id'],  $psnInfo['psn_name'], $psnInfo['psn_id']);
            } else  if ($auditInfo['type'] == 3) {
                $result = $sdk->createZhuchangByFile($docTempate['data']['fileId'], $contractName, $orgInfo['org_id'], $orgInfo['psn_id'],  $psnInfo['psn_name'], $psnInfo['psn_id']);
            } else  if ($auditInfo['type'] == 4) {
                $result = $sdk->createWorkByFile($docTempate['data']['fileId'], $contractName, $orgInfo['org_id'], $orgInfo['psn_id'],  $psnInfo['psn_name'], $psnInfo['psn_id']);
            }



            $dbret =  EContract::create([
                'project_id' => $projectInfo['id'],
                'project_audit_id' => $auditInfo['id'],
                'template_id' => $templateId,
                'user_id' => $orgInfo['user_id'],
                'to_user_id' => $auditInfo['user_id'],
                'type' =>  1,
                'send_psn_id' => $orgInfo['psn_id'],
                'send_org_id' => $orgInfo['org_id'],
                'accept_psn_id' =>  $psnInfo['psn_id'],
                'accept_org_id' => $params['accept_org_id'] ?? "",
                'name' => $contractName,
                'is_send' => $params['is_send'] ?? 0,
                'status' => $params['status'] ?? 0,
                'signFlowId' => $result['signFlowId'] ?? '',
                'contract_file' => $params['contract_file'] ?? ""
            ]);
            if (!$dbret) {
                throw new \Exception("合同录入失败");
            }
            $result = $sdk->getSignContractUrl($result['signFlowId'], $orgInfo['psn_id'], "bafang://pages/user/user");
            Db::commit();
            return  $result;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 审核
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function work_audited(array $params)
    {
        Db::startTrans();
        try {
            $bool = ProjectTasksAudit::update([
                'work_status' => $params['work_status'],
                'remarks' => $params['remarks'] ?? ''
            ], ['id' => $params['id']]);
            Db::commit();
            return  $bool ? true : false;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 变更驻场
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function change_onsite(array $params)
    {
        Db::startTrans();
        try {
            $bool = ProjectTasksAudit::update([
                'onsite_user_id' => $params['onsite_user_id'],
                'remarks' => $params['remarks'] ?? ''
            ], ['id' => $params['id']]);
            Db::commit();
            return  $bool ? true : false;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 审核
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function get_client(array $params)
    {
        $result = ProjectTasksAudit::where('type', $params['type'])
            ->where('project_id', $params['project_id'])
            ->join('user', 'project.user_id', '=', 'user.id')
            ->leftjoin('la_personal_verification i', 'la_project_tasks_audit.user_id=i.user_id')
            ->select('i.psn_name', 'la_project_tasks_audit.user_id')
            ->toArray();
        // 根据查询结果构建二维数组
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'psn_name' => $row->psn_name,
                'user_id' => $row->user_id
            ];
        }

        return $data;
    }
    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function detail($params): array
    {
        return ProjectTasksAudit::findOrEmpty($params['id'])->toArray();
    }


    /**
     * @notes 获取项目的招聘专员或驻场经理
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function getListNameByType($params): array
    {

        $ssql = "select b.user_id,a.project_id,a.type,b.psn_name from (
            select user_id,project_id,id,type,creator from la_project_tasks_audit where  `status` = 2 and type in (3)
        ) as a
         left join la_personal_verification as b on a.user_id = b.user_id 
         left join la_e_contract as c on a.id = c.project_audit_id 
         ";
        if ($params['user_id'] != 1) {
            $ssql .= " where a.creator = " .  $params['user_id'] . " and c.status = 2";
        } else {
            $ssql .= "where c.status = 2";
        }

        $result = Db::query($ssql);
        return $result;
    }


    /**
     * @notes 获取人才市场列表
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/01/07 18:30
     */
    public static function getListMembers(): array
    {

        $ssql = "select a.avatar,b.user_id,a.profile,a.role,b.psn_name,b.psn_mobile from (
            select id,avatar from user where  `flow_status` = 3 and role in (3)
        ) as a
         left join la_personal_verification as b on a.id = b.user_id ";
        $result = Db::query($ssql);
        return $result;
    }
}
