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

namespace app\adminapi\controller;

use app\adminapi\logic\auth\AdminLogic;
use app\adminapi\logic\EContractLogic;

use app\common\http\esign\OrganizeAuth;
use app\common\http\esign\comm\EsignLogHelper;
use app\common\model\EContract;
use app\common\model\EnterpriseVerification;
use app\common\model\PersonalVerification;
use app\common\model\ProjectTasksAudit;
use app\api\logic\UserLogic;
use app\common\http\tim\IMAccountImporter;
use app\common\http\tim\TencentUserSign;
use app\common\model\auth\Admin;
use app\common\model\auth\AdminRole;
use Monolog\Utils;

/**
 * 管理员登录控制器
 * Class LoginController
 * @package app\adminapi\controller
 */
class AuthController extends BaseAdminController
{
    public array $notNeedLogin = ['callback'];

    /**
     * @notes 账号登录
     * @date 2021/6/30 17:01
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     */
    public function callback()
    {
        try {
            $result =  OrganizeAuth::CallbackVerify();
            EsignLogHelper::writeLog(json_encode($result, JSON_UNESCAPED_UNICODE), 'callback');
            switch ($result['action']) {
                case 'AUTHORIZE_FINISH':
                    $flowInfo = OrganizeAuth::queryAuthFlow($result['authFlowId']);

                    switch ($flowInfo['data']['authType']) {
                        case "ORG":
                            //实名和授权都通过
                            if ($flowInfo["data"]["realNameStatus"] == 1 && $flowInfo["data"]["authorizedStatus"] == 1) {
                                $authinfo = $flowInfo["data"]["authInfo"];
                                $find =  PersonalVerification::where('auth_flow_id', $result['authFlowId'])->find();
                                if ($find) {
                                    EsignLogHelper::writeLog("queryAuthFlow ===>  " . json_encode($flowInfo, JSON_UNESCAPED_UNICODE), 'callback');


                                    $params['flow_status'] = 2;
                                    $params['id'] = $find['user_id'];
                                    UserLogic::SaveInfo($params);
                                    EnterpriseVerification::where('auth_flow_id', $result['authFlowId'])->update([
                                        "psn_mobile" => $authinfo['person']['psnAccount']['accountMobile'] ?? '',
                                        "psn_id" => $authinfo['person']['psnId'] ?? '',
                                        "realname_status" => 4,
                                        "org_name" => $authinfo['organization']['orgName'] ?? '',
                                        "org_id" => $authinfo['organization']['orgId'] ?? '',
                                        'org_id_card_num' =>  $authinfo['organization']['orgInfo']['orgIDCardNum'] ?? '',
                                        'org_id_card_type' => $authinfo['organization']['orgInfo']['orgIDCardType'] ?? '',
                                        'legal_rep_name' =>  $authinfo['organization']['orgInfo']['legalRepName'] ?? '',
                                        'legal_rep_id_card_num' =>  $authinfo['organization']['orgInfo']['legalRepIDCardNum'] ?? '',
                                        'legal_rep_id_card_type' =>  $authinfo['organization']['orgInfo']['legalRepIDCardType'] ?? '',
                                        'admin_name' => '',
                                        'admin_account' => '',
                                    ]);
                                }
                            }
                        case "PSN":
                            //实名和授权都通过
                            if ($flowInfo["data"]["realNameStatus"] == 1 && $flowInfo["data"]["authorizedStatus"] == 1) {
                                $authinfo = $flowInfo["data"]["authInfo"];
                                $find =  PersonalVerification::where('auth_flow_id', $result['authFlowId'])->find();
                                if ($find) {
                                    $params['flow_status'] = 2;
                                    $params['id'] = $find['user_id'];
                                    UserLogic::SaveInfo($params);
                                    PersonalVerification::where('auth_flow_id', $result['authFlowId'])->update([
                                        "psn_mobile" => $authinfo['person']['psnAccount']['accountMobile'] ?? '',
                                        "psn_id" => $authinfo['person']['psnId'] ?? '',
                                        "realname_status" => 4,
                                        "psn_name" => $authinfo['person']['psnInfo']['psnName'] ?? '',
                                        "psn_id_card_num" => $authinfo['person']['psnInfo']['psnIDCardNum'] ?? '',
                                        "psn_id_card_type" => $authinfo['person']['psnInfo']['psnIDCardType'] ?? '',
                                    ]);
                                }
                            }
                    }
                    break;
                case 'AUTH_PASS':


                    if ($result['authType'] == 'PSN') {
                        // $flowInfo = OrganizeAuth::queryAuthFlow($result['authFlowId']);
                        // if ($flowInfo["data"]["realNameStatus"] == 1 && $flowInfo["data"]["authorizedStatus"] == 1) {
                        //     $authinfo = $flowInfo["data"]["authInfo"];
                        //     $find =  PersonalVerification::where('auth_flow_id', $result['authFlowId'])->find();
                        //     if ($find) {
                        //         $params['flow_status'] = 2;
                        //         $params['id'] = $find['user_id'];
                        //         UserLogic::SaveInfo($params);
                        //         PersonalVerification::where('auth_flow_id', $result['authFlowId'])->update([
                        //             "psn_mobile" => $authinfo['psnInfo']['psnAccount']['accountMobile'] ?? '',
                        //             "psn_id" => $result['psnInfo']['psnId'] ?? '',
                        //             "realname_status" => 4,
                        //             "realname" => $psnInfo['data']['name'] ?? '',
                        //             "id_card_num" => $psnInfo['data']['idNo'] ?? '',
                        //             "id_card_type" => $psnInfo['data']['idType'] ?? '',
                        //         ]);
                        //     }
                        // }
                        $psnInfo = OrganizeAuth::personsIdentityInfo($result['psnInfo']['psnId'], 3);
                        EsignLogHelper::writeLog($result['psnInfo']['psnId'], 'callback');
                        EsignLogHelper::writeLog(json_encode($psnInfo, JSON_UNESCAPED_UNICODE), 'callback');
                        $find =  PersonalVerification::where('auth_flow_id', $result['authFlowId'])->find();
                        if ($find) {
                            $params['flow_status'] = 2;
                            $params['id'] = $find['user_id'];
                            UserLogic::SaveInfo($params);
                            PersonalVerification::where('auth_flow_id', $result['authFlowId'])->update([
                                "psn_mobile" => $result['psnInfo']['psnAccount']['accountMobile'] ?? '',
                                "psn_id" => $result['psnInfo']['psnId'] ?? '',
                                "realname_status" => 4,
                                "realname" => $psnInfo['data']['name'] ?? '',
                                "psn_id_card_num" => $psnInfo['data']['idNo'] ?? '',
                                "psn_id_card_type" => $psnInfo['data']['idType'] ?? '',
                            ]);
                        }
                    } else if ($result['authType'] == 'ORG') {
                        $company_info = OrganizeAuth::organizationsIdentityInfo($result['organization']['orgName'], "", 3);
                        EsignLogHelper::writeLog($result['organization']['orgName'], 'callback');
                        EsignLogHelper::writeLog(json_encode($company_info, JSON_UNESCAPED_UNICODE), 'callback');
                        if ($company_info['code'] == 0) {
                            EnterpriseVerification::where('auth_flow_id', $result['authFlowId'])->update([
                                "psn_mobile" => $result['organization']['transactor']['psnAccount']['accountMobile'] ?? '',
                                "psn_id" => $result['organization']['transactor']['psnId'] ?? '',
                                "realname_status" => 4,
                                "org_name" => $company_info['data']['orgName'] ?? '',
                                "org_id" => $company_info['data']['orgId'] ?? '',
                                'org_id_card_num' =>  $company_info['data']['orgInfo']['orgIDCardNum'] ?? '',
                                'org_id_card_type' => $company_info['data']['orgInfo']['orgIDCardType'] ?? '',
                                'legal_rep_name' =>  $company_info['data']['orgInfo']['legalRepName'] ?? '',
                                'legal_rep_id_card_num' =>  $company_info['data']['orgInfo']['legalRepIDCardNum'] ?? '',
                                'legal_rep_id_card_type' => $company_info['data']['orgInfo']['legalRepIDCardType'] ?? '',
                                'admin_name' => $company_info['data']['orgInfo']['adminName'] ?? '',
                                'admin_account' => $company_info['data']['orgInfo']['adminAccount'] ?? '',
                            ]);
                        }
                    }


                    break;
                case 'SIGN_MISSON_COMPLETE':
                    //签署人签署完成回调
                    EsignLogHelper::writeLog($result, 'callback_SIGN_MISSON_COMPLETE');
                    break;
                case 'SIGN_FLOW_COMPLETE':
                    //流程结束逻辑处理
                    EsignLogHelper::writeLog($result, 'callback_SIGN_FLOW_COMPLETE');
                    $updateStatus =  EContract::where('signFlowId', $result['signFlowId'])->where('status', '<>', 2)->update([
                        'status' => $result['signFlowStatus'],
                        'update_time' => time()
                    ]);
                    $contractInfo =   EContract::where('signFlowId', $result['signFlowId'])->find();
                    if ($updateStatus) {
                        AdminLogic::ContractUserMoney([
                            'user_id' => $contractInfo['template_id'] == '89c201c3f07b4734a112eaac7ca45667' ? $contractInfo['to_user_id'] : $contractInfo['user_id'],
                            'action' => 0,
                            'num' => "1.00",
                            'remark' => "合同编号（{$contractInfo['id']}-{$result['signFlowId']}）"
                        ]);
                    }
                    //签署完成同步企业状态
                    if ($contractInfo['template_id'] == '89c201c3f07b4734a112eaac7ca45667' &&  $result['signFlowStatus'] == 2) {
                        if ($contractInfo['type'] == 0) {
                            //企业
                            EnterpriseVerification::where(['user_id' => $contractInfo['user_id']])->update([
                                'realname_status' => 6,
                            ]);
                        }
                    }

                    if ($contractInfo['template_id'] == 'a7e8c8efa21b494eabb8ec3ec247430e' &&  $result['signFlowStatus'] == 2 && $contractInfo['project_audit_id'] > 0) {
                        //改变签约状态
                        ProjectTasksAudit::where(['id' => $contractInfo['project_audit_id']])->update([
                            'status' => 2,
                            'update_time' => time()
                        ]);
                    }


                    break;
            }
        } catch (\Exception $e) {
            EsignLogHelper::writeLog(sprintf('Uncaught Exception %s: "%s" at %s line %s', Utils::getClass($e), $e->getMessage(), $e->getFile(), $e->getLine()), 'callback');
        }
    }
}
