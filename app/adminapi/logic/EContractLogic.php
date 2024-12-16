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


use app\common\model\EContract;
use app\common\logic\BaseLogic;
use think\facade\Db;

use app\common\model\EnterpriseVerification;
use app\common\http\esign\OrganizeAuth;
use Exception;
use app\common\http\esign\Config;

/**
 * EContract逻辑
 * Class EContractLogic
 * @package app\adminapi\logic
 */
class EContractLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public static function add(array $params)
    {

        Db::startTrans();
        try {

            $contract = EContract::where('user_id', $params['user_id'])->Where('template_id', '89c201c3f07b4734a112eaac7ca45667')->where("status in (0,1)")->find();
            if ($contract) {
                throw new Exception("合同已存在，不可重复发起，请到运营管理 => 合同管理 => 我的合同中的平台合同去签署");
            }
            $params = EnterpriseVerification::where('user_id', $params['user_id'])->find();
            $sdk =  new OrganizeAuth();
            $file_id = $sdk->createByDocTemplate("89c201c3f07b4734a112eaac7ca45667", "八方平台入驻合同", $params['org_name'], $params['legal_rep_name'], $params['org_id_card_num'], $params['psn_mobile'], $params['email'], $params['address']);

            $result = $sdk->createByFile($file_id, "八方平台入驻合同", $params['org_id'], $params['psn_id']);

            $dbret =  EContract::create([
                'template_id' => "89c201c3f07b4734a112eaac7ca45667",
                'user_id' => $params['user_id'],
                'to_user_id' => $params['to_user_id'] ?? 1,
                'type' => $params['type'] ?? 0,
                'send_psn_id' => $params['psn_id'],
                'send_org_id' => $params['org_id'],
                'accept_psn_id' => $params['accept_psn_id'] ?? 0,
                'accept_org_id' => $params['accept_org_id'] ?? "5e7ec709965a47c6bb1fc26a7411f35e",
                'name' => "八方平台入驻合同",
                'is_send' => $params['is_send'] ?? 0,
                'status' => $params['status'] ?? 0,
                'signFlowId' => $result['signFlowId'] ?? '',
                'contract_file' => $params['contract_file'] ?? ""
            ]);
            if (!$dbret) {
                throw new Exception("合同录入失败");
            }

            //企业
            EnterpriseVerification::where([
                'user_id' =>  $params['user_id']
            ])->update([
                'realname_status' => 5,
            ]);


            $result = $sdk->getSignContractUrl($result['signFlowId'], $params['psn_id'], Config::getDomain() . "/business/operate/contracts/e_contract");

            Db::commit();
            return  $result;
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
     * @date 2024/07/14 22:24
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            EContract::where('id', $params['id'])->update([
                'template_id' => $params['template_id'],
                'user_id' => $params['user_id'],
                'to_user_id' => $params['to_user_id'],
                'type' => $params['type'],
                'send_psn_id' => $params['send_psn_id'],
                'send_org_id' => $params['send_org_id'],
                'accept_psn_id' => $params['accept_psn_id'],
                'accept_org_id' => $params['accept_org_id'],
                'name' => $params['name'],
                'is_send' => $params['is_send'],
                'status' => $params['status'],
                'signFlowId' => $params['signFlowId'],
                'contract_file' => $params['contract_file']
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
     * @date 2024/07/14 22:24
     */
    public static function delete(array $params): bool
    {
        return EContract::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public static function detail($params): array
    {
        return EContract::findOrEmpty($params['id'])->toArray();
    }


    /**
     * @notes 获取签署URL
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public static function getSignContractUrl($params): array
    {
        $params =  EContract::findOrEmpty($params['id'])->toArray();
        $sdk =  new OrganizeAuth();
        $result = $sdk->getSignContractUrl($params['signFlowId'], $params['send_psn_id'], $params['redirectUrl'] ?? Config::getDomain() . "/business/operate/contracts/e_contract");
        return $result;
    }

    /**
     * @notes 获取签署URL
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public static function getAppSignContractUrl($params): array
    {
        $params =  EContract::findOrEmpty($params['id'])->toArray();
        $sdk =  new OrganizeAuth();
        $result = $sdk->getSignContractUrl($params['signFlowId'], $params['accept_psn_id'], "bafang://pages/contract/index");
        return $result;
    }

    /**
     * @notes 获取合同URL
     * @param $params
     * @return array
     * @author ideaadmin
     * @date 2024/07/14 22:24
     */
    public static function getDownloadContractUrl($params): array
    {
        $params =  EContract::findOrEmpty($params['id'])->toArray();
        $sdk =  new OrganizeAuth();
        $result = $sdk->getDownloadContractUrl($params['signFlowId']);
        return $result;
    }
}
